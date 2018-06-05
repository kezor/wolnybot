<?php

namespace App\Http\Controllers;


use App\Building\Farmland;
use App\Jobs\ProcessFarmland;
use App\Space;

class FarmlandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dispatchJob($spaceId)
    {
        $farmland = Farmland::find($spaceId);

        $processFarmland = new ProcessFarmland($farmland);

        $this->dispatch($processFarmland);

        return back();
    }

}