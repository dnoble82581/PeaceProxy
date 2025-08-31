<?php

use App\Http\Controllers\Api\EnumController;
use Laravel\Cashier\Http\Controllers\WebhookController;

Route::get(
    'enums/user-negotiation-roles',
    [EnumController::class, 'userNegotiationRoles']
)->name('enums.user-negotiation-roles');

Route::get(
    'enums/subject-bond-types',
    [EnumController::class, 'subjectNegotiationBondTypes']
)->name('enums.subject-bond-types');

Route::get(
    'enums/warrant-status',
    [EnumController::class, 'warrantStatus']
)->name('enums.warrant-status');


Route::get(
    'enums/warrant-type',
    [EnumController::class, 'warrantTypes']
)->name('enums.warrant-type');

Route::get(
    'enums/genders',
    [EnumController::class, 'genders']
)->name('enums.genders');


Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);
