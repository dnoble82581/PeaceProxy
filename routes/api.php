<?php

use App\Http\Controllers\Api\EnumController;
use App\Http\Controllers\Api\SubjectController;
use Laravel\Cashier\Http\Controllers\WebhookController;

Route::prefix('enums')->group(function () {
    Route::get(
        '/user-negotiation-roles',
        [EnumController::class, 'userNegotiationRoles']
    )->name('enums.user-negotiation-roles');

    Route::get(
        '/subject-bond-types',
        [EnumController::class, 'subjectNegotiationBondTypes']
    )->name('enums.subject-bond-types');

    Route::get(
        '/warrant-status',
        [EnumController::class, 'warrantStatus']
    )->name('enums.warrant-status');


    Route::get(
        '/warrant-type',
        [EnumController::class, 'warrantTypes']
    )->name('enums.warrant-type');

    Route::get(
        '/genders',
        [EnumController::class, 'genders']
    )->name('enums.genders');

    Route::get(
        '/response-types',
        [EnumController::class, 'responseTypes']
    )->name('enums.response-types');

    Route::get(
        '/question-categories',
        [EnumController::class, 'questionCategories']
    )->name('enums.question-categories');
});

Route::get('/subjects', [SubjectController::class, 'index'])->name('api.subjects');

Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);
