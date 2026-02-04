<?php

namespace App\Traits;

use Illuminate\Validation\Rule;

trait UsesMasterValidation
{
    protected function masterRules(
        string $table,
        string $nameColumn,
        ?string $codeColumn = null,
        ?int $ignoreId = null,
        string $statusColumn = 'status_id',
        bool $codeRequired = false
    ): array {
        $rules = [
            "form.{$nameColumn}" => [
                'required',
                'string',
                'max:255',
                Rule::unique($table, $nameColumn)->ignore($ignoreId),
            ],

            "form.{$statusColumn}" => [
                'required',
                'integer',
            ],
        ];

        if ($codeColumn) {
            $rules["form.{$codeColumn}"] = array_filter([
                $codeRequired ? 'required' : 'nullable',
                'string',
                'max:50',
                Rule::unique($table, $codeColumn)->ignore($ignoreId),
            ]);
        }

        return $rules;
    }

    /**
     * Field-specific messages (NO ambiguity)
     */
    protected function masterMessages(
        string $label,
        string $nameColumn,
        ?string $codeColumn = null
    ): array {
        $messages = [
            // Name
            "form.{$nameColumn}.required" =>
                "The {$label} name is required.",

            "form.{$nameColumn}.unique" =>
                "The {$label} name \":input\" already exists.",
        ];

        if ($codeColumn) {
            // Code
            $messages["form.{$codeColumn}.required"] =
                "The {$label} code is required.";

            $messages["form.{$codeColumn}.unique"] =
                "The {$label} code \":input\" already exists.";
        }

        return $messages;
    }
}
