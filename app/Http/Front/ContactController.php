<?php

namespace App\Http\Front;

use App\Http\Controller;
use App\Http\Front\Data\ContactData;
use App\Mail\ContactMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    public function submit(ContactData $data): RedirectResponse
    {
        Mail::to(config('mail.from.address'))->send(new ContactMail($data));

        return to_route('contact')->with('success', value: 'Votre mail a bien été envoyé, vous recevrez une réponse dans les plus bref délais.');
    }
}
