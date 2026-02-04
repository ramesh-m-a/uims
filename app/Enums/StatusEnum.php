<?php

namespace App\Enums;

class StatusEnum
{
    // SYSTEM
    public const ACTIVE = 'ACTIVE';
    public const INACTIVE = 'INACTIVE';
    public const DRAFT = 'DRAFT';
    public const PROCESSING = 'PROCESSING';
    public const APPROVED = 'APPROVED';
    public const REJECTED = 'REJECTED';
    public const ACCEPTED = 'ACCEPTED';
    public const APPROVAL_PENDING = 'APPROVAL_PENDING';

    // PROFILE
    public const UPLOAD_PHOTO = 'UPLOAD_PHOTO';
    public const UPDATE_BASIC = 'UPDATE_BASIC';
    public const PROFILE_COMPLETED = 'PROFILE_COMPLETED';
    public const SUBMITTED_TO_PRINCIPAL = 'SUBMITTED_TO_PRINCIPAL';
    public const NEED_MORE_INFO = 'NEED_MORE_INFO';
    public const CONTACT_RGUHS = 'CONTACT_RGUHS';

    // COLLEGE
    public const PRINCIPAL_RECOMMENDED = 'PRINCIPAL_RECOMMENDED';
    public const APPLICATION_REJECTED_COLLEGE = 'APPLICATION_REJECTED_COLLEGE';
    public const APPLICATION_APPROVED_COLLEGE = 'APPLICATION_APPROVED_COLLEGE';

    // RGUHS
    public const APPLICATION_APPROVED_RGUHS = 'APPLICATION_APPROVED_RGUHS';
    public const APPLICATION_REJECTED_RGUHS = 'APPLICATION_REJECTED_RGUHS';

    // EXAMINER
    public const ASSIGN = 'ASSIGN';
    public const CHANGE_PENDING = 'CHANGE_PENDING';
    public const CHANGE_APPROVED = 'CHANGE_APPROVED';
    public const CHANGE_REJECTED = 'CHANGE_REJECTED';
}
