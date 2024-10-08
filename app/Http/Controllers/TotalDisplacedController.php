<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TotalDisplacedController extends Controller
{
    public function getTotalDisplaced($disaster_report_uuid)
    {
        $results['region'] = DB::select("
            SELECT
                SUM(t1.families_cum) as families_cum,
                SUM(t1.families_now) as families_now,
                SUM(t1.persons_cum) as persons_cum,
                SUM(t1.persons_now) as persons_now
            FROM (
                SELECT
                    t1.province_psgc_code,
                    t2.name as province_name,
                    t1.municipality_psgc_code,
                    t3.name as municipality_name,
                    SUM(t1.families_cum) as families_cum,
                    SUM(t1.families_now) as families_now,
                    SUM(t1.persons_cum) as persons_cum,
                    SUM(t1.persons_now) as persons_now
                FROM
                    tbl_inside_ec t1
                LEFT JOIN lib_provinces t2 ON t1.province_psgc_code = t2.psgc_code
                LEFT JOIN lib_municipalities t3 ON t1.municipality_psgc_code = t3.psgc_code
                WHERE
                    t1.disaster_report_uuid = ?
                GROUP BY
                    t1.province_psgc_code, t2.name, t3.name, t1.municipality_psgc_code

                UNION ALL

                SELECT
                    t1.host_province_psgc_code as province_psgc_code,
                    t2.name as province_name,
                    t1.host_municipality_psgc_code as municipality_psgc_code,
                    t3.name as municipality_name,
                    SUM(t1.aff_families_cum) as families_cum,
                    SUM(t1.aff_families_now) as families_now,
                    SUM(t1.aff_persons_cum) as persons_cum,
                    SUM(t1.aff_persons_now) as persons_now
                FROM
                    tbl_outside_ec t1
                LEFT JOIN lib_provinces t2 ON t1.host_province_psgc_code = t2.psgc_code
                LEFT JOIN lib_municipalities t3 ON t1.host_municipality_psgc_code = t3.psgc_code
                WHERE
                    t1.disaster_report_uuid = ?
                GROUP BY
                    t1.host_province_psgc_code, t2.name, t3.name, t1.host_municipality_psgc_code
            ) t1
        ", [$disaster_report_uuid, $disaster_report_uuid]);

        $results['province'] = DB::select("
            SELECT
                t1.province_psgc_code,
                t1.province_name,
                SUM(t1.families_cum) as families_cum,
                SUM(t1.families_now) as families_now,
                SUM(t1.persons_cum) as persons_cum,
                SUM(t1.persons_now) as persons_now
            FROM (
                SELECT
                    t1.province_psgc_code,
                    t2.name as province_name,
                    t1.municipality_psgc_code,
                    t3.name as municipality_name,
                    SUM(t1.families_cum) as families_cum,
                    SUM(t1.families_now) as families_now,
                    SUM(t1.persons_cum) as persons_cum,
                    SUM(t1.persons_now) as persons_now
                FROM
                    tbl_inside_ec t1
                LEFT JOIN lib_provinces t2 ON t1.province_psgc_code = t2.psgc_code
                LEFT JOIN lib_municipalities t3 ON t1.municipality_psgc_code = t3.psgc_code
                WHERE
                    t1.disaster_report_uuid = ?
                GROUP BY
                    t1.province_psgc_code, t2.name, t3.name, t1.municipality_psgc_code

                UNION ALL

                SELECT
                    t1.host_province_psgc_code as province_psgc_code,
                    t2.name as province_name,
                    t1.host_municipality_psgc_code as municipality_psgc_code,
                    t3.name as municipality_name,
                    SUM(t1.aff_families_cum) as families_cum,
                    SUM(t1.aff_families_now) as families_now,
                    SUM(t1.aff_persons_cum) as persons_cum,
                    SUM(t1.aff_persons_now) as persons_now
                FROM
                    tbl_outside_ec t1
                LEFT JOIN lib_provinces t2 ON t1.host_province_psgc_code = t2.psgc_code
                LEFT JOIN lib_municipalities t3 ON t1.host_municipality_psgc_code = t3.psgc_code
                WHERE
                    t1.disaster_report_uuid = ?
                GROUP BY
                    t1.host_province_psgc_code, t2.name, t3.name, t1.host_municipality_psgc_code
            ) t1
            GROUP BY
                t1.province_psgc_code, t1.province_name
            ORDER BY
                t1.province_psgc_code
        ", [$disaster_report_uuid, $disaster_report_uuid]);

        $results['municipality'] = DB::select("
            SELECT
                t1.province_psgc_code,
                t1.province_name,
                t1.municipality_psgc_code,
                t1.municipality_name,
                SUM(t1.families_cum) as families_cum,
                SUM(t1.families_now) as families_now,
                SUM(t1.persons_cum) as persons_cum,
                SUM(t1.persons_now) as persons_now
            FROM (
                SELECT
                    t1.province_psgc_code,
                    t2.name as province_name,
                    t1.municipality_psgc_code,
                    t3.name as municipality_name,
                    SUM(t1.families_cum) as families_cum,
                    SUM(t1.families_now) as families_now,
                    SUM(t1.persons_cum) as persons_cum,
                    SUM(t1.persons_now) as persons_now
                FROM
                    tbl_inside_ec t1
                LEFT JOIN lib_provinces t2 ON t1.province_psgc_code = t2.psgc_code
                LEFT JOIN lib_municipalities t3 ON t1.municipality_psgc_code = t3.psgc_code
                WHERE
                    t1.disaster_report_uuid = ?
                GROUP BY
                    t1.province_psgc_code, t2.name, t3.name, t1.municipality_psgc_code

                UNION ALL

                SELECT
                    t1.host_province_psgc_code as province_psgc_code,
                    t2.name as province_name,
                    t1.host_municipality_psgc_code as municipality_psgc_code,
                    t3.name as municipality_name,
                    SUM(t1.aff_families_cum) as families_cum,
                    SUM(t1.aff_families_now) as families_now,
                    SUM(t1.aff_persons_cum) as persons_cum,
                    SUM(t1.aff_persons_now) as persons_now
                FROM
                    tbl_outside_ec t1
                LEFT JOIN lib_provinces t2 ON t1.host_province_psgc_code = t2.psgc_code
                LEFT JOIN lib_municipalities t3 ON t1.host_municipality_psgc_code = t3.psgc_code
                WHERE
                    t1.disaster_report_uuid = ?
                GROUP BY
                    t1.host_province_psgc_code, t2.name, t3.name, t1.host_municipality_psgc_code
            ) t1
            GROUP BY
                t1.province_psgc_code, t1.province_name, t1.municipality_psgc_code, t1.municipality_name
            ORDER BY
                t1.province_psgc_code, t1.municipality_psgc_code
        ", [$disaster_report_uuid, $disaster_report_uuid]);

        return response()->json($results);
    }
}
