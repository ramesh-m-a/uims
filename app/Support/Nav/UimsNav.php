<?php

namespace App\Support\Nav;

class UimsNav
{
    /**
     * ======================================================
     * UNIVERSAL MODULE ACTIVE CHECK
     * ======================================================
     */
    public static function module(string $module): bool
    {
        $map = self::moduleRouteMap();

        if (!isset($map[$module])) {
            return false;
        }

        $patterns = $map[$module];

        /**
         * =========================================
         * 1️⃣ NORMAL REQUEST MATCH
         * =========================================
         */
        foreach ($patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }

        /**
         * =========================================
         * 2️⃣ LIVEWIRE REQUEST MATCH (🔥 FINAL FIX)
         * =========================================
         */
        if (request()->routeIs('livewire.update')) {

            $referer = request()->header('Referer') ?? '';

            if (!$referer) {
                return false;
            }

            foreach ($patterns as $pattern) {

                try {

                    /**
                     * Convert route pattern → real URL
                     * examiner.appointment-order.view → /examiner/appointment-order/view
                     */
                    if (!str_contains($pattern, '*')) {

                        $url = route($pattern, [], false);

                        if ($url && str_contains($referer, $url)) {
                            return true;
                        }

                    } else {

                        /**
                         * Wildcard route support
                         * examiner.appointment-order.*
                         */
                        $baseRoute = rtrim(str_replace('.*', '', $pattern), '.');

                        $routeCollection = app('router')->getRoutes();

                        foreach ($routeCollection as $route) {

                            if (str_starts_with($route->getName() ?? '', $baseRoute)) {

                                $url = route($route->getName(), [], false);

                                if ($url && str_contains($referer, $url)) {
                                    return true;
                                }
                            }
                        }
                    }

                } catch (\Throwable $e) {
                    // Ignore invalid route generation
                }
            }
        }

        return false;
    }

    /**
     * ======================================================
     * ROUTE PATTERN → URL PATH
     * examiner.appointment-order.* → examiner/appointment-order
     * ======================================================
     */
    private static function routePatternToPath(string $pattern): string
    {
        $pattern = str_replace(['.*', '*'], '', $pattern);
        return str_replace('.', '/', trim($pattern, '.'));
    }

    /**
     * ======================================================
     * MODULE → ROUTE MAP
     * ======================================================
     */
    private static function moduleRouteMap(): array
    {
        return [

            'allocation' => [
                'examiner.allocation',
                'examiner.college.allocation',
                'examiner.allocation.*',
            ],

            'appointment' => [
                'examiner.appointment-order.view',
                'examiner.appointment-order.*',
            ],

            'appoint' => [
                'examiner.appoint',
                'examiner.appoint.*',
            ],

            // ⭐ FUTURE MODULES
            'marks-entry' => [
                'examiner.marks-entry.*',
            ],

            'valuation' => [
                'examiner.valuation.*',
            ],

        ];
    }
}
