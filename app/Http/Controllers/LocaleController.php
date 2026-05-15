<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request)
    {
        $supported = implode(',', config('locales.supported', ['en', 'ar']));
        $validated = $request->validate([
            'locale' => 'required|in:'.$supported,
        ]);

        $request->session()->put('locale', $validated['locale']);

        return redirect()->back();
    }
}
