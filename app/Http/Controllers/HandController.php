<?php

namespace App\Http\Controllers;

use App\Models\GameUser;
use App\Services\HandService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;

class HandController extends Controller
{
    public function ready(Request $request, GameUser $gameUser): RedirectResponse
    {
        app(HandService::class)->ready($gameUser);
        return back();
    }
}
