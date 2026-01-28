<?php

declare(strict_types=1);

use App\Mail\ContactUsMail;
use Illuminate\Support\Facades\Mail;
use Livewire\Volt\Volt;

it('sends an email when the contact form is submitted', function () {
    Mail::fake();

    $component = Volt::test('forms.contact.contact-us');

    $component
        ->set('form.name', 'Jane Doe')
        ->set('form.email', 'jane@example.com')
        ->set('form.phone', '+14155550123')
        ->set('form.subject', 'Need some help')
        ->set('form.message', 'Hello, I would like to know more about your services.')
        ->call('send')
        ->assertHasNoErrors();

    Mail::assertSent(ContactUsMail::class, function (ContactUsMail $mail) {
        return $mail->hasTo('dusty@peaceproxy.com')
            && $mail->name === 'Jane Doe'
            && $mail->email === 'jane@example.com'
            && $mail->phone === '+14155550123'
            && $mail->subjectLine === 'Need some help';
    });
});
