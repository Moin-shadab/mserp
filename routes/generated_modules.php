<?php

use Illuminate\Support\Facades\Route;

// Developer Generated Module Routes

Route::middleware(['auth'])->group(function () {
    Route::get('/erp/custom-todos-test', [\App\Http\Controllers\Generated\TaskManagementCustomTodosTestController::class, 'index']);
    Route::get('/erp/custom/task-management/custom-todos-test', [\App\Http\Controllers\Generated\TaskManagementCustomTodosTestController::class, 'index']);
    Route::get('/api/custom/task-management/custom-todos-test/data', [\App\Http\Controllers\Generated\TaskManagementCustomTodosTestController::class, 'getData']);
    Route::post('/api/custom/task-management/custom-todos-test/store', [\App\Http\Controllers\Generated\TaskManagementCustomTodosTestController::class, 'store']);
    Route::post('/api/custom/task-management/custom-todos-test/update/{id}', [\App\Http\Controllers\Generated\TaskManagementCustomTodosTestController::class, 'update']);
    Route::delete('/api/custom/task-management/custom-todos-test/destroy/{id}', [\App\Http\Controllers\Generated\TaskManagementCustomTodosTestController::class, 'destroy']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/erp/task-manager-test-moin', [\App\Http\Controllers\Generated\CommunicationHubTaskManagerTestMoinController::class, 'index']);
    Route::get('/erp/custom/communication-hub/task-manager-test-moin', [\App\Http\Controllers\Generated\CommunicationHubTaskManagerTestMoinController::class, 'index']);
    Route::get('/api/custom/communication-hub/task-manager-test-moin/data', [\App\Http\Controllers\Generated\CommunicationHubTaskManagerTestMoinController::class, 'getData']);
    Route::post('/api/custom/communication-hub/task-manager-test-moin/store', [\App\Http\Controllers\Generated\CommunicationHubTaskManagerTestMoinController::class, 'store']);
    Route::post('/api/custom/communication-hub/task-manager-test-moin/update/{id}', [\App\Http\Controllers\Generated\CommunicationHubTaskManagerTestMoinController::class, 'update']);
    Route::delete('/api/custom/communication-hub/task-manager-test-moin/destroy/{id}', [\App\Http\Controllers\Generated\CommunicationHubTaskManagerTestMoinController::class, 'destroy']);
});
