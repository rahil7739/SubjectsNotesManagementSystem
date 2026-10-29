<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function user(Request $request): View
    {
        $user = $request->user('web');

        return view('dashboard', [
            'user' => $user,
            'subjects' => $user->subjects()->with(['notes' => fn ($query) => $query->with('attachments')])->latest()->get(),
            'notes' => $user->notes()->with(['subject', 'attachments'])->latest()->get(),
        ]);
    }
}
