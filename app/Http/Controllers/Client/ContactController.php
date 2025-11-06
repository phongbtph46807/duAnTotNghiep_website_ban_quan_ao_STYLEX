<?php

namespace App\Http\Controllers\Client;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController
{
    public function index()
    {
        return view('client.contact.index');
    }
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'content' => 'required|string|max:1000',
        ]);

        Contact::create([
            'email' => $request->input('email'),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('client.contact.index')->with('success', 'Cảm ơn bạn đã liên hệ với chúng tôi!');
    }
}
