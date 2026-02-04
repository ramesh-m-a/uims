<?php

namespace App\Http\Controllers\Master\College;

use App\Http\Controllers\Controller;
use App\Models\Master\Config\Academic\College;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CollegeController extends Controller
{
    /**
     * Display a listing of the colleges.
     */
    public function index()
    {
        // We render the view — the table is populated via server-side DataTables
        return view('master.college.index');
    }

    /**
     * Show the form for creating a new college.
     */
    public function createe()
    {
        return view('master.college.create');
    }

    /**
     * Store a newly created college in storage.
     * Returns JSON for AJAX requests, otherwise redirects.
     */
    public function store(Request $request)
    {
        $rules = [
            'mas_college_name'   => 'required|string|max:255',
            'mas_college_code'   => 'nullable|string|max:50',
            'mas_college_city'   => 'nullable|string|max:150',
            'mas_college_region' => 'nullable|string|max:150',
            'mas_college_status' => 'nullable|string|max:30',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $college = College::create($validator->validated());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'College created successfully.',
                    'college' => $college,
                ], 201);
            }

            return redirect()->route('master.college.index')->with('success', 'College created successfully.');
        } catch (Exception $e) {
            Log::error('College store error: '.$e->getMessage(), ['exception' => $e]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Failed to create college.'], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Failed to create college — please try again.');
        }
    }

    /**
     * Display the specified college.
     */
    public function show(College $college)
    {
        return view('master.college.show', compact('college'));
    }

    /**
     * Show the form for editing the specified college.
     */
    public function editt(College $college)
    {
        return view('master.college.edit', compact('college'));
    }

    /**
     * Update the specified college in storage.
     * Returns JSON for AJAX requests, otherwise redirects.
     */
    public function update(Request $request, College $college)
    {
        $rules = [
            'mas_college_name'   => 'required|string|max:255',
            'mas_college_code'   => 'nullable|string|max:50',
            'mas_college_city'   => 'nullable|string|max:150',
            'mas_college_region' => 'nullable|string|max:150',
            'mas_college_status' => 'nullable|string|max:30',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $college->update($validator->validated());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'College updated successfully.',
                    'college' => $college->fresh(),
                ], 200);
            }

            return redirect()->route('master.college.index')->with('success', 'College updated successfully.');
        } catch (Exception $e) {
            Log::error('College update error: '.$e->getMessage(), ['id' => $college->id, 'exception' => $e]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Failed to update college.'], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Failed to update college — please try again.');
        }
    }

    /**
     * Remove the specified college from storage.
     * Accepts AJAX and normal requests.
     */
    public function destroy(Request $request, College $college)
    {
        try {
            $id = $college->id;
            $college->delete();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'College deleted successfully.', 'id' => $id], 200);
            }

            return redirect()->route('master.college.index')->with('success', 'College deleted successfully.');
        } catch (ModelNotFoundException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'College not found.'], 404);
            }
            return redirect()->route('master.college.index')->with('error', 'College not found.');
        } catch (Exception $e) {
            Log::error('College delete error: '.$e->getMessage(), ['id' => $college->id ?? null, 'exception' => $e]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Failed to delete college.'], 500);
            }

            return redirect()->route('master.college.index')->with('error', 'Failed to delete college — please try again.');
        }
    }

    /**
     * Return just the <tbody> rows HTML for AJAX refresh.
     * Useful if you prefer partial reloads.
     */
    public function rows(Request $request)
    {
        $colleges = College::orderBy('id')->get();
        return view('master.college._rows', compact('colleges'));
    }

    /**
     * Data provider for server-side DataTables.
     * Accepts standard DataTables query params.
     */
    public function datatable(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'mas_college_name',
            2 => 'mas_college_code',
            3 => 'mas_college_city',
            4 => 'mas_college_region'
        ];

        $query = College::query();

        // global search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('mas_college_name', 'like', "%{$search}%")
                    ->orWhere('mas_college_code', 'like', "%{$search}%")
                    ->orWhere('mas_college_city', 'like', "%{$search}%")
                    ->orWhere('mas_college_region', 'like', "%{$search}%");
            });
        }

        // per-column search (DataTables columns[i][search][value])
        if ($name = $request->input('columns.1.search.value')) {
            $query->where('mas_college_name', 'like', "%{$name}%");
        }
        if ($code = $request->input('columns.2.search.value')) {
            $query->where('mas_college_code', 'like', "%{$code}%");
        }
        if ($city = $request->input('columns.3.search.value')) {
            $query->where('mas_college_city', 'like', "%{$city}%");
        }
        if ($region = $request->input('columns.4.search.value')) {
            $query->where('mas_college_region', 'like', "%{$region}%");
        }

        // ordering
        $orderColIndex = $request->input('order.0.column');
        $orderDir      = $request->input('order.0.dir', 'asc');

        if (isset($columns[$orderColIndex])) {
            $query->orderBy($columns[$orderColIndex], $orderDir);
        }

        $totalData     = College::count();
        $totalFiltered = $query->count();

        // paging
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $colleges = $query->skip($start)->take($length)->get();

        // build DataTables rows
        $data = [];
        foreach ($colleges as $c) {
            $actions =
                '<a href="'.route('master.college.edit', $c->id).'" class="btn btn-sm btn-outline-primary">Edit</a> ' .
                '<button class="btn btn-sm btn-outline-danger js-delete-btn" data-id="'.$c->id.'" data-name="'.e($c->mas_college_name).'">Delete</button>';

            $data[] = [
                $c->id,
                $c->mas_college_name,
                $c->mas_college_code,
                $c->mas_college_city,
                $c->mas_college_region,
                $actions,
            ];
        }

        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }

    public function create(Request $request)
    {
        // if modal requested, return partial form HTML only
        if ($request->query('modal')) {
            return view('master.college._form');
        }
        return view('master.college.create');
    }

    public function edit(Request $request, College $college)
    {
        if ($request->query('modal')) {
            return view('master.college._form', compact('college'));
        }
        return view('master.college.edit', compact('college'));
    }

    /**
     * Export full dataset (csv / excel / print)
     * route: master.college.export
     */
    public function export(Request $request)
    {
        $format = $request->query('format', 'csv'); // csv | excel | print

        $cols = ['id', 'mas_college_name', 'mas_college_code', 'mas_college_city', 'mas_college_region', 'mas_college_status'];

        $rows = College::orderBy('mas_college_name')->get($cols);

        if ($format === 'print') {
            // render a simple print view
            return view('master.college.export_print', compact('rows'));
        }

        // Use StreamedResponse to stream CSV (works for large sets)
        $filename = 'colleges_' . date('Ymd_His');
        $ext = ($format === 'excel') ? 'xls' : 'csv';
        $filename .= '.' . $ext;

        $response = new StreamedResponse(function() use ($rows, $cols, $format) {
            $handle = fopen('php://output', 'w');
            // header row
            fputcsv($handle, array_map('strtoupper', $cols));
            foreach ($rows as $r) {
                $line = [];
                foreach ($cols as $c) {
                    $line[] = $r->{$c} ?? '';
                }
                fputcsv($handle, $line);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }

}
