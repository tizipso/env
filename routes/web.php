<?php

use Dcat\Admin\Extension\Env\Http\Controllers;

Route::prefix('env')->group(function () {
    Route::get('', Controllers\EnvController::class.'@index');
    Route::get('create', Controllers\EnvController::class.'@create');
    Route::get('{id}/edit', Controllers\EnvController::class.'@editor');
    Route::post('', Controllers\EnvController::class.'@toCreate');
    Route::post('{id}', Controllers\EnvController::class.'@toEditor');
    Route::delete('{id}', Controllers\EnvController::class.'@toDelete');
});
