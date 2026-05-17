<?php

namespace App\Http\Controllers;

use App\Services\PresenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresenceController extends Controller
{
    public function online()
    {
        $presenceService = new PresenceService();
        $users = $presenceService->formatForFrontend(Auth::user());

        return response()->json($users);
    }
}
