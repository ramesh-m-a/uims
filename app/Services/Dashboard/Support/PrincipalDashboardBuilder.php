<?php

use App\Services\Dashboard\DashboardRegistry;
use App\Services\Dashboard\Support\DashboardQueryBuilder;

class PrincipalDashboardBuilder
{
    public function build()
    {
        return (new DashboardRegistry())->build('admin');
    }
}
