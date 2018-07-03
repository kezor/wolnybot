<?php

namespace App\Http\Controllers;


use App\Building\Farmland;
use App\Jobs\ProcessFarmland;
use App\Product;
use App\Service\BuildingsService\FarmlandService;
use Illuminate\Support\Facades\Input;

class FarmlandsController extends Controller
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

    public function collect($farmlandId)
    {
        /** @var Farmland $farmland */
        $farmland = Farmland::find($farmlandId);

        $gameService = new FarmlandService($farmland->farm->player);

        $gameService->collectReadyPlants($farmland);

        return back();
    }

    public function seedOnce($farmlandId)
    {
        /** @var Farmland $farmland */
        $farmland = Farmland::find($farmlandId);

        $plant = Product::where('pid', Input::get('plant_id'))
            ->where('player_id', $farmland->farm->player->id)
            ->first();

        $gameService = new FarmlandService($farmland->farm->player);

        $gameService->seedPlants($farmland, $plant);

        return back();
    }

}