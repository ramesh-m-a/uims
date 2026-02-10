<?php

use App\Services\Dashboard\DashboardRegistry;
use App\Services\Dashboard\Support\DashboardQueryBuilder;

class CollegeDashboardBuilder
{
    public function build()
    {
        return (new DashboardRegistry())->build('admin');
    }

}
