<?php

class AllocationPermissionResolver
{
    public static function resolve($user): AllocationPermissions
    {
        $p = new AllocationPermissions();

        if (is_null($user->user_role_id)) {
            // ADMIN
            $p->canChangeExaminer = true;
            $p->canChangeDate = true;
            $p->canAddAdditional = true;
            $p->canUndoAdditional = true;
            $p->canContactRGUHS = true;
        }

        if ($user->user_role_id === 3) {
            // COLLEGE
            $p->canRequestExaminerChange = true;
        }

        return $p;
    }
}
