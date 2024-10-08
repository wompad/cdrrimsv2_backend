<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboardData(Request $request)
    {

        $userid = $request->userid;
        $year = $request->year;

        //get all years in the database
        $result['years'] = DB::table('tbl_dromic')
            ->selectRaw('DISTINCT EXTRACT(YEAR FROM incident_date) AS year')
            ->get();

        //get all the total_affected
        $result['all_affected'] = DB::table('tbl_all_affected as t1')
            ->leftJoin('tbl_disaster_reports as t2', 't1.disaster_report_uuid', '=', 't2.uuid')
            ->leftJoin('tbl_dromic as t3', 't2.incident_id', '=', 't3.uuid')
            ->select(
                DB::raw('SUM(t1.affected_families) as total_affected_families'),
                DB::raw('SUM(t1.affected_persons) as total_affected_persons')
            )
            ->whereRaw('EXTRACT(YEAR FROM t3.incident_date) = ?', [$year])
            ->first();

        //list all the incident
        $result['incidents'] = DB::table('tbl_dromic')
            ->select(
                'tbl_dromic.*',
                'auth_users.username',
                DB::raw('COUNT(tbl_disaster_reports.uuid) AS total_reports')
            )
            ->join('auth_users', 'tbl_dromic.created_by', '=', 'auth_users.id')
            ->leftJoin('tbl_disaster_reports', 'tbl_dromic.uuid', '=', 'tbl_disaster_reports.incident_id')
            ->where('tbl_dromic.created_by', $userid)
            ->whereRaw('EXTRACT(YEAR FROM tbl_dromic.incident_date) = ?', [$year])
            ->groupBy('tbl_dromic.uuid', 'auth_users.username')
            ->orderBy('tbl_dromic.incident_date', 'DESC')
            ->get();

        //get the total idps inside ec
        $result['idps_inside_ec'] = DB::table('tbl_inside_ec as t1')
            ->select(
                DB::raw('SUM(t1.families_cum) AS total_families_inside_ec'),
                DB::raw('SUM(t1.persons_cum) AS total_persons_inside_ec')
            )
            ->leftJoin('tbl_disaster_reports as t2', 't1.disaster_report_uuid', '=', 't2.uuid')
            ->leftJoin('tbl_dromic as t3', 't2.incident_id', '=', 't3.uuid')
            ->whereRaw('EXTRACT(YEAR FROM t3.incident_date) = ?', [$year])
            ->first();

        //get the total idps outside ec
        $result['idps_outside_ec'] = DB::table('tbl_outside_ec as t1')
            ->select(
                DB::raw('SUM(t1.aff_families_cum) AS total_families_inside_ec'),
                DB::raw('SUM(t1.aff_persons_cum) AS total_persons_inside_ec')
            )
            ->leftJoin('tbl_disaster_reports as t2', 't1.disaster_report_uuid', '=', 't2.uuid')
            ->leftJoin('tbl_dromic as t3', 't2.incident_id', '=', 't3.uuid')
            ->whereRaw('EXTRACT(YEAR FROM t3.incident_date) = ?', [$year])
            ->first();

        $queryAllAssistance = "SELECT SUM(t1.total_cost) AS total_cost FROM (SELECT
                    t1.*,
                    t2.total_cost
                    FROM
                    (
                    SELECT DISTINCT ON
                        ( t3.uuid ) t1.disaster_report_uuid,
                        t3.uuid,
                        t3.incident_name,
                        t2.created_at
                    FROM
                        tbl_fnfi_assistance AS t1
                        LEFT JOIN tbl_disaster_reports AS t2 ON t1.disaster_report_uuid = t2.
                        UUID LEFT JOIN tbl_dromic AS t3 ON t2.incident_id = t3.uuid
                    WHERE
                        EXTRACT ( YEAR FROM t3.incident_date ) = ?
                    ORDER BY
                        t3.UUID,
                        t2.created_at DESC
                    ) t1
                    LEFT JOIN (
                    SELECT
                        t1.disaster_report_uuid,
                        SUM ( t1.total_cost ) AS total_cost
                    FROM
                        ( SELECT t1.uuid, t1.disaster_report_uuid, ( t1.fnfi_cost * t1.fnfi_quantity ) AS total_cost FROM tbl_fnfi_assistance t1 ) t1
                    GROUP BY
                        t1.disaster_report_uuid
                    ORDER BY
                    t1.disaster_report_uuid
                    ) t2 ON t1.disaster_report_uuid = t2.disaster_report_uuid)t1
        ";

        $result['all_assistance'] = collect(DB::select($queryAllAssistance, [$year]))->first();

        $queryAllAffected = "SELECT
                    t1.UUID,
                    UPPER ( t1.incident_name ) incident_name,
                    t2.total_affected_families
                    FROM
                    (
                    SELECT DISTINCT ON
                        ( t3.UUID ) t1.disaster_report_uuid,
                        t3.UUID,
                        t3.incident_name,
                        t2.created_at
                    FROM
                        tbl_all_affected AS t1
                        LEFT JOIN tbl_disaster_reports AS t2 ON t1.disaster_report_uuid = t2.
                        UUID LEFT JOIN tbl_dromic AS t3 ON t2.incident_id = t3.UUID
                    WHERE
                        EXTRACT ( YEAR FROM t3.incident_date ) = ?
                    ORDER BY
                        t3.UUID,
                        t2.created_at DESC
                    ) t1
                    LEFT JOIN (
                    SELECT t1.disaster_report_uuid,
                        SUM ( t1.affected_families ) total_affected_families
                    FROM tbl_all_affected t1
                    GROUP BY t1.disaster_report_uuid
                    ORDER BY t1.disaster_report_uuid )
                    t2 ON t1.disaster_report_uuid = t2.disaster_report_uuid
        ";

        $result['all_affected_chart'] = DB::select($queryAllAffected, [$year]);

        return response()->json($result, 201);
    }
}
