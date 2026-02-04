<?php

namespace App\Traits;

use Illuminate\Validation\Rule;

trait MasterValidationFromConfig
{
    protected function rulesFromConfig(array $form, ?int $id = null): array
    {
        $config = $this->masterConfig();
        $rules  = [];

        foreach ($config['columns'] ?? [] as $field => $meta) {

            // Skip fields not in form
            if (!array_key_exists($field, $form)) {
                continue;
            }

            $fieldRules = [];

            // REQUIRED
            if (($meta['required'] ?? false) === true) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // TYPE
            if (($meta['type'] ?? '') === 'string') {
                $fieldRules[] = 'string';
            }

            // UNIQUE
            if (($meta['unique'] ?? false) === true) {
                $fieldRules[] = Rule::unique(
                    $config['table'],
                    $field
                )->ignore($id);
            }

            // FOREIGN KEY
            if (($meta['type'] ?? '') === 'fk' && isset($meta['references'])) {
                $fieldRules[] =
                    'exists:' .
                    $meta['references']['table'] .
                    ',' .
                    $meta['references']['column'];
            }

            $rules["form.$field"] = $fieldRules;
        }

        return $rules;
    }

    protected function messagesFromConfig(): array
    {
        $config   = $this->masterConfig();
        $messages = [];

        foreach ($config['columns'] ?? [] as $field => $meta) {
            $label = strtolower($meta['label'] ?? $field);

            $messages["form.$field.required"] =
                "The {$label} field is required.";

            $messages["form.$field.unique"] =
                "The {$label} \":input\" already exists.";
        }

        return $messages;
    }
}
