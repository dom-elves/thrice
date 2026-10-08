<?php

namespace App\Http\Controllers;

use App\Models\GameUser;
use App\Services\HandService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HandController extends Controller
{
    public function ready(Request $request, GameUser $gameUser): RedirectResponse
    {
        $handService = app(HandService::class);
        $handService->ready($gameUser);

        return back();
    }
}
