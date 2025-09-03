<?php

use App\Http\Middleware\IdentifyTenantMiddleware;
use App\Http\Middleware\RedirectToTenantDashboardMiddleware;
use Livewire\Volt\Volt;

Broadcast::routes([
'middleware' => ['web', 'auth', IdentifyTenantMiddleware::class], // Add your middleware like 'tenant'
]);

Route::middleware([
    'web', RedirectToTenantDashboardMiddleware::class,
])->group(function () {
    Volt::route('/', 'pages.welcome');
});

// TENANT AWARE ROUTES
Route::domain('{tenantSubdomain}.'.config('app.domain'))->middleware([
    'web', 'auth', App\Http\Middleware\IdentifyTenantMiddleware::class,
])->group(function () {

    Volt::route('/pricing', 'billing.pricing')->name('tenant.pricing');
    Volt::route('/billing', 'billing.index')->name('billing.index');

    //	DASHBOARD ROUTES
    Route::prefix('/dashboard')->group(function () {
        Volt::route('', 'pages.dashboard.dashboard')
            ->name('dashboard');

        Volt::route('/negotiations', 'pages.dashboard.negotiations')
            ->name('dashboard.negotiations');

        Volt::route('/settings', 'pages.dashboard.settings')
            ->name('dashboard.settings');

        Volt::route('/users', 'pages.dashboard.users')
            ->name('dashboard.users');

        Volt::route('/assessments', 'pages.dashboard.assessments')
            ->name('pages.dashboard.assessments');

    });

    //	Testing Calls here. delete later
    Volt::route('/dev/call-ui', 'pages.calls.call-tester')
        ->name('dev.call.ui');

    //	NEGOTIATIONS ROUTES
    Route::prefix('/negotiations')->group(function () {
        Volt::route(
            '/{negotiation:title}/negotiation-noc',
            'pages.negotiation.noc'
        )->name('negotiation-noc');

        Volt::route('/create', 'forms.negotiation.create')
            ->name('negotiation.create');
    });

    //	NEGOTIATION ROUTES
    Route::prefix('/negotiation')->group(function () {
        // Subject routes
        Volt::route('/{negotiation?}/subject/{subject:name}/edit', 'forms.subject.edit-subject')
            ->name('subject.edit');

        Volt::route(
            '/{negotiationId}/warrant/create',
            'pages.subject.create-warrant'
        )->name('negotiation.subject.create-warrant');

        Volt::route(
            '/{negotiationId}/warrant/{warrantId}/update',
            'pages.subject.update-warrant'
        )->name('negotiation.subject.update-warrant');

        Volt::route('/{negotiation?}/subject/{subject:name}/show', 'pages.subject.show-subject')
            ->name('subject.show');

        // Contact Point routes
        Volt::route('/{negotiationId}/subject/{subjectId}/contact-point/create', 'forms.contact.create-contact-point')
            ->name('contact-point.create');

        Volt::route('/{negotiationId}/subject/{subjectId}/contact-point/{contactPointId}/edit', 'forms.contact.edit-contact-point')
            ->name('contact-point.edit');

        // Hostage routes
        Volt::route('/{negotiation?}/hostage/create', 'forms.hostage.create-hostage')
            ->name('hostage.create');

        Volt::route('/{negotiation?}/hostage/{hostage}/edit', 'forms.hostage.edit-hostage')
            ->name('hostage.edit');

        Volt::route('/{negotiation?}/hostage/{hostage}/show', 'forms.hostage.view-hostage')
            ->name('hostage.show');

        // Warning routes
        Volt::route('/{negotiationId}/subject/{subjectId}/warning/create', 'forms.warning.create-warning')
            ->name('warning.create');

        Volt::route('/{negotiationId}/subject/{subjectId}/warning/{warningId}/edit', 'forms.warning.edit-warning')
            ->name('warning.edit');
    });

});

require __DIR__.'/auth.php';
//require __DIR__.'/api.php';
