<?php

namespace App\Services\Dashboard\Support;

use App\Models\Admin\User;
use Illuminate\Support\Facades\DB;

class DashboardQueryBuilder
{
    protected $baseUserQuery;
    protected $draftQuery;

    private const STATUS_DRAFT = 3;
    private const STATUS_VERIFICATION_PENDING = 7;
    private const STATUS_REJECTED = 8;
    private const STATUS_APPROVED = 24;

    public function __construct()
    {
        $this->baseUserQuery = User::query();

        $this->draftQuery = DB::table('user_profile_drafts as d')
            ->join('users as u', 'u.id', '=', 'd.user_id');
    }

    public function usersBreakdown(string $groupByColumn)
    {
        $query = clone $this->baseUserQuery;

        $labelSelect = $groupByColumn;
        $groupByColumns = [$groupByColumn];

        // ---- Department ----
        if ($groupByColumn === 'basic_details.basic_details_department_id') {
            $query->leftJoin('basic_details as bd', 'bd.basic_details_user_id', '=', 'users.id')
                ->leftJoin('mas_department as md', 'md.id', '=', 'bd.basic_details_department_id');

            $labelSelect = 'md.mas_department_name as label';
            $groupByColumns = ['md.id', 'md.mas_department_name'];
        }

        // ---- Designation ----
        if ($groupByColumn === 'user_designation_id') {
            $query->leftJoin('mas_designation as des', 'des.id', '=', 'users.user_designation_id');

            $labelSelect = 'des.mas_designation_name as label';
            $groupByColumns = ['des.id', 'des.mas_designation_name'];
        }

        return $query
            ->selectRaw($labelSelect)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(users.user_status_id = ?) as approved', [self::STATUS_APPROVED])
            ->selectRaw('SUM(users.user_status_id = ?) as pending', [self::STATUS_VERIFICATION_PENDING])
            ->selectRaw('SUM(users.user_status_id = ?) as rejected', [self::STATUS_REJECTED])
            ->groupBy($groupByColumns);
    }

    public function filterByCollege(int $collegeId)
    {
        $this->baseUserQuery = $this->baseUserQuery->where('user_college_id', $collegeId);
        return $this;
    }

    public function principalBreakdown(string $groupByColumn, int $principalId)
    {
        return (clone $this->draftQuery)
            ->where('d.principal_id', $principalId)
            ->select($groupByColumn)
            ->selectRaw("SUM(d.status = 'pending') as pending_reviews")
            ->selectRaw("SUM(d.status = 'approved') as approved_by_me")
            ->selectRaw("SUM(d.status = 'rejected') as rejected_by_me")
            ->groupBy($groupByColumn);
    }

    public function breakdownSet(array $columns)
    {
        return collect($columns)->mapWithKeys(function ($col) {
            $key = str_replace(['user_', '_id', 'basic_details.basic_details_'], '', $col);

            return [
                $key => $this->usersBreakdown($col)->get()
            ];
        })->toArray();
    }

    public function principalBreakdownSet(array $columns, int $principalId)
    {
        return collect($columns)->mapWithKeys(function ($col) use ($principalId) {
            $key = str_replace(['u.', 'user_', '_id'], '', $col);

            return [
                $key => $this->principalBreakdown($col, $principalId)->get()
            ];
        })->toArray();
    }

    public function resolve(string $query, array $params = [], array $context = [])
    {
        return match ($query) {

            'users.count' => User::query()->count(),

            'users.status_count' => User::query()
                ->where('user_status_id', $this->mapStatus($params['status']))
                ->count(),

            'users.stale_drafts' => User::query()
                ->where('user_status_id', self::STATUS_DRAFT)
                ->where('updated_at', '<', now()->subDays($params['days']))
                ->limit($params['limit'] ?? 10)
                ->get(['id', 'name']),

            'users.pending_verification' => User::query()
                ->where('user_status_id', self::STATUS_VERIFICATION_PENDING)
                ->where('updated_at', '<', now()->subHours($params['hours']))
                ->limit($params['limit'] ?? 10)
                ->get(['id', 'name']),

            'users.status_list' => User::query()
                ->where('user_status_id', $this->mapStatus($params['status']))
                ->limit($params['limit'] ?? 10)
                ->get(['id', 'name']),

            'drafts.status_count' => DB::table('user_profile_drafts')
                ->where('principal_id', $context['principal_id'])
                ->where('status', $params['status'])
                ->count(),

            'drafts.pending_list' => DB::table('user_profile_drafts as d')
                ->join('users as u', 'u.id', '=', 'd.user_id')
                ->where('d.principal_id', $context['principal_id'])
                ->where('d.status', 'pending')
                ->limit($params['limit'] ?? 10)
                ->get(['u.id', 'u.name', 'd.submitted_at']),

            default => throw new \Exception("Unknown dashboard query [$query]")
        };
    }

    private function mapStatus(string $status): int
    {
        return match ($status) {
            'approved' => self::STATUS_APPROVED,
            'rejected' => self::STATUS_REJECTED,
            'verification_pending' => self::STATUS_VERIFICATION_PENDING,
            'draft' => self::STATUS_DRAFT,
            default => throw new \Exception("Unknown status [$status]"),
        };
    }
}
