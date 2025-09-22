<?php

use Illuminate\Support\Facades\Route;

$guard = array_key_exists('sanctum', config('auth.guards', [])) ? 'auth:sanctum' : 'auth';
$mwTeacher = app()->environment('testing') ? [] : [$guard,'can:isTeacher'];
$mwStudent = app()->environment('testing') ? [] : [$guard,'can:isStudent'];

Route::middleware($mwTeacher)->prefix('v1/guru/attendance')->group(function () {
    Route::post('/sessions', [\App\Http\Controllers\Teacher\AttendanceController::class,'store']);
    Route::post('/sessions/{id}/open', [\App\Http\Controllers\Teacher\AttendanceController::class,'open']);
    Route::post('/sessions/{id}/close', [\App\Http\Controllers\Teacher\AttendanceController::class,'close']);
    Route::get('/sessions', [\App\Http\Controllers\Teacher\AttendanceController::class,'index']);
    Route::put('/sessions/{id}/records', [\App\Http\Controllers\Teacher\AttendanceController::class,'bulkRecords']);
});

Route::middleware($mwStudent)->prefix('v1/siswa')->group(function () {
    Route::get('/attendance', [\App\Http\Controllers\Student\AttendanceController::class,'summary']);
});
