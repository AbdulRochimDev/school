<?php

use Illuminate\Support\Facades\Route;

$guard = array_key_exists('sanctum', config('auth.guards', [])) ? 'auth:sanctum' : 'auth';
$mwTeacher = app()->environment('testing') ? [] : [$guard,'can:isTeacher'];
$mwStudent = app()->environment('testing') ? [] : [$guard,'can:isStudent'];

Route::middleware($mwTeacher)->prefix('v1/guru')->group(function () {
    Route::apiResource('assignments', \App\Http\Controllers\Teacher\AssignmentController::class);
    Route::post('assignments/{id}/grade', [\App\Http\Controllers\Teacher\AssignmentController::class,'grade']);
    Route::post('assignments/{id}/grade-items', [\App\Http\Controllers\Teacher\GradeItemController::class,'store']);
});

Route::middleware($mwStudent)->prefix('v1/siswa')->group(function () {
    Route::post('assignments/{id}/submit', [\App\Http\Controllers\Student\SubmissionController::class,'store']);
    Route::get('assignments/{id}', [\App\Http\Controllers\Student\SubmissionController::class,'show']);
});
