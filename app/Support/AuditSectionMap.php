<?php

namespace App\Support;

class AuditSectionMap
{
    public static function sectionFor(string $field): string
    {
        return match (true) {
            str_starts_with($field, 'basic_') => 'Basic Details',
            str_starts_with($field, 'address_') => 'Address Details',
            str_starts_with($field, 'bank_') => 'Bank Details',
            str_starts_with($field, 'qualification_') => 'Qualification',
            str_starts_with($field, 'work_') => 'Work Experience',
            str_starts_with($field, 'document_') => 'Documents',
            default => 'General',
        };
    }
}
