<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\LibProvincesController;
use App\Http\Controllers\LibMunicipalitiesController;
use App\Http\Controllers\LibBarangayController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DromicReportController;
use App\Http\Controllers\DisasterDromicReportController;
use App\Http\Controllers\AllAffectedController;
use App\Http\Controllers\LGUAssistanceController;
use App\Http\Controllers\OutsideECController;
use App\Http\Controllers\FNFIController;
use App\Http\Controllers\FNFIAssistanceController;
use App\Http\Controllers\EvacuationCenterController;
use App\Http\Controllers\InsideEcController;
use App\Http\Controllers\TotalDisplacedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SexAgeSectorDataController;

use App\Http\Middleware\corsMiddleware;
use App\Http\Middleware\Cors;

use Illuminate\Http\Request;

//AuthUserController
Route::post('/register_user', [AuthUserController::class, 'store']);
Route::get('/get_users', [AuthUserController::class, 'getUsers']);
Route::put('/update_user/{userid}', [AuthUserController::class, 'updateUser']);

//Libraries
Route::get('/getprovinces', [LibProvincesController::class, 'getprovinces']);

//LibLibMunicipalitiesControllerraries
Route::get('/getmunicipalities', [LibMunicipalitiesController::class, 'getmunicipalities']);
Route::get('/getmunicipalitiesbyprovince/{province_psgc}', [LibMunicipalitiesController::class, 'getMunicipalitiesByProvince']);

//LibBarangayController
Route::get('/getbarangaybymunicipality/{municipality_psgc}', [LibBarangayController::class, 'getBarangaysByMunicipality']);
Route::get('/getbarangaybyprovince/{province_psgc}', [LibBarangayController::class, 'getBarangaysByProvince']);

//LoginController
Route::post('/login', [LoginController::class, 'login']);

//DromicReportController
Route::post('/save_incident', [DromicReportController::class, 'store']);
Route::get('/get_incidents/{userid}', [DromicReportController::class, 'getIncidents']);
Route::put('/update_incident/{incidentid}', [DromicReportController::class, 'updateIncident']);
Route::get('/get_latest_report/{incidentid}', [DromicReportController::class, 'getLatestDromicReport']);
Route::get('/get_dromic_report_uuid/{uuid}', [DromicReportController::class, 'getReportbyDisasterUUID']);
Route::get('/get_all_reports/{incidentid}', [DromicReportController::class, 'getAllReports']);
Route::delete('/delete_incident/{incidentid}', [DromicReportController::class, 'destroy']);

//DisasterDromicReportController
Route::post('/save_new_report', [DisasterDromicReportController::class, 'store']);
Route::post('/save_new_dromic_report', [DisasterDromicReportController::class, 'newDROMICReport']);

//AllAffectedController
Route::post('/validate_data', [AllAffectedController::class, 'validateData']);
Route::post('/save_damage_per_brgy', [AllAffectedController::class, 'batchSave']);
Route::post('/all_affected_province/{disaster_report_id}', [AllAffectedController::class, 'getAffectedProvince']);
Route::post('/update_affected_data', [AllAffectedController::class, 'updateAffected']);
Route::delete('/delete_affected_data/{uuid}', [AllAffectedController::class, 'destroy']);
Route::get('/get_summary_report/{disaster_report_uuid}', [AllAffectedController::class, 'getMainSummaryReport']);

//LGUAssistanceController
Route::post('/save_lgu_assistance', [LGUAssistanceController::class, 'storeOrUpdate']);

//OutsideECController
Route::post('/validate_outside_ec', [OutsideECController::class, 'validateOutsideECRequest']);
Route::post('/save_outside_ec', [OutsideECController::class, 'batchSave']);
Route::get('/get_all_outside_ec/{disaster_report_id}', [OutsideECController::class, 'getAllOutsideEC']);
Route::put('/update_outside_ec/{uuid}', [OutsideECController::class, 'updateOutsideEC']);
Route::delete('/delete_outside_ec/{uuid}', [OutsideECController::class, 'destroy']);

//FNFIController
Route::post('/save_item', [FNFIController::class, 'store']);
Route::get('/get_items', [FNFIController::class, 'getAllItems']);
Route::put('/update_item/{uuid}', [FNFIController::class, 'update']);

//FNFAssistanceController
Route::post('/validate_form', [FNFIAssistanceController::class, 'validateForm']);
Route::post('/save_fnfi_assistance', [FNFIAssistanceController::class, 'batchSave']);
Route::get('/get_all_fnfi_assistance/{disaster_report_id}', [FNFIAssistanceController::class, 'getFNFIAssistance']);
Route::delete('/delete_fnfi_assistance/{uuid}', [FNFIAssistanceController::class, 'destroy']);
Route::get('/get_fnfi_assistance_report/{disaster_report_id}', [FNFIAssistanceController::class, 'getFNFIAssistanceReport']);

//EvacuationCenterController
Route::post('/save_new_ec', [EvacuationCenterController::class, 'store']);
Route::get('/get_all_ecs', [EvacuationCenterController::class, 'getAllECs']);
Route::post('/get_all_ecs2', [EvacuationCenterController::class, 'paginateECs']);
Route::put('/update_ec/{uuid}', [EvacuationCenterController::class, 'updateEC']);
Route::delete('/delete_ec/{uuid}', [EvacuationCenterController::class, 'deleteEC']);
Route::get('/evacuation_centers_brgy/{brgy_psgc_code}', [EvacuationCenterController::class, 'getEvacuationCentersByBarangay']);
Route::get('/paginateECs', [EvacuationCenterController::class, 'paginateECs2']);
Route::get('/check_existing_ec/{uuid}', [EvacuationCenterController::class, 'checkExistingEC']);

//InsideEcController
Route::post('/save_data_inside_ec', [InsideEcController::class, 'store']);
Route::post('/check_existing_ec', [InsideEcController::class, 'checkEC']);
Route::post('/get_all_ec_report/{disaster_report_uuid}', [InsideEcController::class, 'getAllECReport']);
Route::put('/update_ec_detail/{uuid}', [InsideEcController::class, 'updateECDetails']);
Route::delete('/delete_ec_detail/{uuid}', [InsideEcController::class, 'deleteEvacuationCenter']);

//TotalDisplacedController
Route::get('/get_total_displaced/{disaster_report_uuid}', [TotalDisplacedController::class, 'getTotalDisplaced']);

//DashboardController
Route::post('/get_dashboard_data', [DashboardController::class, 'getDashboardData']);

//SexAgeSectorDataController
Route::post('/save_sex_age_sector_data', [SexAgeSectorDataController::class, 'store']);
Route::get('/getAllSADData/{disaster_report_uuid}', [SexAgeSectorDataController::class, 'getAllSADData']);

Route::post('/apitoken', [AuthUserController::class, 'apitoken']);

// Route::middleware('auth:sanctum', 'throttle:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
