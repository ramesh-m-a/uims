<?php

namespace App\Livewire\Master\Config\Exam;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Master\Config\Exam\Batch;
use App\Models\Master\Config\Exam\ExamEligibleStudentDetails;
use App\Services\Exam\BatchEngine;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class BatchUpload extends Component
{
    use WithFileUploads;

    public bool $show = false;
    public $file;

    public array $validatedRows = [];
    public bool $hasErrors = false;

    protected $rules = [
        'file' => 'required|file|mimes:xlsx,xls|max:10240',
    ];

    protected $listeners = ['open-upload-modal' => 'open'];

    public function open()
    {
        $this->reset(['file', 'validatedRows', 'hasErrors']);
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
    }

    /* =====================================================
     | MAIN UPLOAD FLOW
     ===================================================== */
    public function processUpload()
    {
        $this->validate();

        try {

            $uploadId = now()->timestamp;

            $rows = Excel::toArray(
                new class implements \Maatwebsite\Excel\Concerns\ToArray, \Maatwebsite\Excel\Concerns\WithHeadingRow {
                    public function array(array $array) { return $array; }
                },
                $this->file
            )[0];

            $this->validatedRows = [];

            foreach ($rows as $index => $row) {

                $errors = $this->validateRow($row);

                $this->validatedRows[] = [
                    'row_num' => $index + 2,
                    'data'    => $row,
                    'errors'  => $errors,
                ];
            }

            $this->hasErrors = collect($this->validatedRows)
                ->contains(fn($r) => count($r['errors']) > 0);

            if ($this->hasErrors) {
                return;
            }

            foreach ($rows as $row) {
                $this->saveEligible($row, $uploadId);
            }

            $this->generateBatches($uploadId);

            $this->dispatch('toast', type: 'success', message: 'Upload processed successfully');
            $this->dispatch('refresh-batch-table')->to(\App\Livewire\Master\Config\Exam\BatchTable::class);

            $this->close();

        } catch (\Throwable $e) {
            Log::error($e);
            $this->dispatch('toast', type: 'error', message: $e->getMessage());
        }
    }

    /* =====================================================
     | ROW VALIDATION
     ===================================================== */
    private function validateRow(array $row): array
    {
        $errors = [];

        if (!$this->exists('mas_stream', 'mas_stream_name', $row['facultyname'] ?? null))
            $errors[] = "Invalid Faculty";

        if (!in_array(strtoupper(trim($row['courselevel'] ?? '')), ['UG','PG','PG-SS']))
            $errors[] = "Invalid Course Level";

        if (!$this->exists('mas_subject', 'mas_subject_name', $row['subject'] ?? null))
            $errors[] = "Invalid Subject";

        if (!$this->exists('mas_revised_scheme', 'mas_revised_scheme_short_name', $row['scheme'] ?? null))
            $errors[] = "Invalid Scheme";

        if (!$this->exists('mas_college', 'mas_college_code', $row['centrecode'] ?? null))
            $errors[] = "Invalid Centre";

        if (!$this->exists('mas_year', 'mas_year_year', $row['examyear'] ?? null))
            $errors[] = "Invalid Year";

        if (!$this->exists('mas_month', 'mas_month_name', $row['exammonth'] ?? null))
            $errors[] = "Invalid Month";

        if (!$this->exists('mas_college', 'mas_college_code', $row['attachedcollege'] ?? null))
            $errors[] = "Invalid Attached College";

        if (!is_numeric($row['studentcount'] ?? null))
            $errors[] = "Invalid Student Count";

        if (!isset($row['examstartdate']))
            $errors[] = "Missing Exam Start Date";

        return $errors;
    }

    private function exists($table, $column, $value): bool
    {
        if (!$value) return false;

        return DB::table($table)
            ->where($column, trim($value))
            ->exists();
    }

    /* =====================================================
     | SAVE ELIGIBLE
     ===================================================== */
    private function saveEligible(array $row, int $uploadId)
    {
        $streamId = DB::table('mas_stream')->where('mas_stream_name', trim($row['facultyname']))->value('id');

        $degreeId = match (strtoupper(trim($row['courselevel']))) {
            'UG' => 1,
            'PG' => 2,
            'PG-SS' => 3,
        };

        $subjectId  = DB::table('mas_subject')->where('mas_subject_name', trim($row['subject']))->value('id');
        $schemeId   = DB::table('mas_revised_scheme')->where('mas_revised_scheme_short_name', trim($row['scheme']))->value('id');
        $centreId   = DB::table('mas_college')->where('mas_college_code', trim($row['centrecode']))->value('id');
        $yearId     = DB::table('mas_year')->where('mas_year_year', trim($row['examyear']))->value('id');
        $monthId    = DB::table('mas_month')->where('mas_month_name', trim($row['exammonth']))->value('id');
        $attachedId = DB::table('mas_college')->where('mas_college_code', trim($row['attachedcollege']))->value('id');

        ExamEligibleStudentDetails::create([
            'exam_eligible_student_details_faculty_name' => $streamId,
            'exam_eligible_student_details_course_level' => $degreeId,
            'exam_eligible_student_details_subject_name' => $subjectId,
            'exam_eligible_student_details_scheme'       => $schemeId,
            'exam_eligible_student_details_centre_code'  => $centreId,
            'exam_eligible_student_details_exam_year'    => $yearId,
            'exam_eligible_student_details_exam_month'   => $monthId,
            'exam_eligible_student_details_attached_college' => $attachedId,
            'exam_eligible_student_details_stud_count'   => (int)$row['studentcount'],
            'exam_eligible_student_details_upload_session_id' => $uploadId,
            'exam_eligible_student_details_exam_start_date' =>
                Carbon::instance(
                    ExcelDate::excelToDateTimeObject($row['examstartdate'])
                )->toDateString(),
        ]);
    }

    /* =====================================================
     | GENERATE BATCHES (🔥 RESTORED BREAKUP LOGIC)
     ===================================================== */
    private function generateBatches(int $uploadId)
    {
        $groups = DB::table('exam_eligible_student_details')
            ->where('exam_eligible_student_details_upload_session_id', $uploadId)
            ->select(
                'exam_eligible_student_details_faculty_name as stream_id',
                'exam_eligible_student_details_course_level as degree_id',
                'exam_eligible_student_details_subject_name as subject_id',
                'exam_eligible_student_details_scheme as scheme_id',
                'exam_eligible_student_details_centre_code as centre_id',
                'exam_eligible_student_details_exam_year as year_id',
                'exam_eligible_student_details_exam_month as month_id',
                DB::raw('SUM(exam_eligible_student_details_stud_count) as total_students'),
                DB::raw('MIN(exam_eligible_student_details_exam_start_date) as exam_start_date'),
            )
            ->groupBy('stream_id','degree_id','subject_id','scheme_id','centre_id','year_id','month_id')
            ->get();

        foreach ($groups as $g) {

            DB::transaction(function () use ($g, $uploadId) {

                $batch = Batch::create([
                    'mas_batch_stream_id' => $g->stream_id,
                    'mas_batch_degree_id' => $g->degree_id,
                    'mas_batch_subject_id'=> $g->subject_id,
                    'mas_batch_centre_id' => $g->centre_id,
                    'mas_batch_year_id'   => $g->year_id,
                    'mas_batch_month_id'  => $g->month_id,
                    'mas_batch_revised_scheme_id' => $g->scheme_id,
                    'mas_batch_total_students' => $g->total_students,
                    'mas_batch_start_date' => $g->exam_start_date,
                    'mas_batch_status_id' => 1,
                ]);

                /* 🔥 RESTORED: ATTACHED BREAKUP */
                $breakup = DB::table('exam_eligible_student_details')
                    ->where('exam_eligible_student_details_upload_session_id', $uploadId)
                    ->where('exam_eligible_student_details_subject_name', $g->subject_id)
                    ->where('exam_eligible_student_details_centre_code', $g->centre_id)
                    ->where('exam_eligible_student_details_faculty_name', $g->stream_id)
                    ->where('exam_eligible_student_details_course_level', $g->degree_id)
                    ->where('exam_eligible_student_details_scheme', $g->scheme_id)
                    ->where('exam_eligible_student_details_exam_year', $g->year_id)
                    ->where('exam_eligible_student_details_exam_month', $g->month_id)
                    ->select(
                        'exam_eligible_student_details_attached_college as centre_id',
                        DB::raw('SUM(exam_eligible_student_details_stud_count) as total')
                    )
                    ->groupBy('exam_eligible_student_details_attached_college')
                    ->get();

                foreach ($breakup as $row) {

                    DB::table('mas_batch_centre')->updateOrInsert(
                        [
                            'mas_batch_id' => $batch->id,
                            'mas_centre_id' => $row->centre_id,
                        ],
                        [
                            'is_attached' => 1,
                            'status_id'   => 1,
                            'updated_at'  => now(),
                        ]
                    );

                    DB::table('mas_batch_centre_student')->updateOrInsert(
                        [
                            'mas_batch_id' => $batch->id,
                            'mas_centre_id' => $row->centre_id,
                        ],
                        [
                            'student_count' => $row->total,
                            'updated_at'    => now(),
                        ]
                    );
                }

                BatchEngine::regenerate($batch);
            });
        }
    }

    public function render()
    {
        return view('livewire.master.config.exam.batch-upload');
    }
}
