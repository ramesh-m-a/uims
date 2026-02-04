<?php

namespace App\Livewire\Concerns;

use Carbon\Carbon;

trait NormalizesDates
{
    /**
     * Convert ANY incoming date safely to DB format (Y-m-d)
     * Supports:
     * - Y-m-d
     * - d/m/Y
     * - d-m-Y
     */
    protected function dbDate($date)
    {
        if (!$date) return null;

        // Already DB format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // dd/mm/yyyy
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        }

        // dd-mm-yyyy
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
            return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
        }

        // Fallback
        return Carbon::parse($date)->format('Y-m-d');
    }

    /**
     * Convert DB date → display format
     */
    protected function displayDate($date)
    {
        if (!$date) return '';

        return Carbon::parse($date)->format('d/m/Y');
    }

    /**
     * Convert ANY incoming date → input safe format (Y-m-d)
     */
    protected function inputDate($date)
    {
        if (!$date) return null;

        return $this->dbDate($date);
    }

    /**
     * Bulk normalize Livewire properties
     */
    protected function normalizeDateProperties(array $properties)
    {
        foreach ($properties as $prop) {
            if (!empty($this->$prop)) {
                $this->$prop = $this->inputDate($this->$prop);
            }
        }
    }
}
