<?php

namespace App\Jobs;

use App\Building\Farmland;
use App\Connector\WolniFarmerzyConnector;
use App\Service\GameService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessFarmland implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Farmland
     */
    private $farmland;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Farmland $farmland)
    {
        $this->farmland = $farmland;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $player = $this->farmland->farm->player;

        $gameService = new GameService($player);

        $gameService->updateStock();
        $gameService->updateBuildings();

        $this->farmland->fillInFields();

        $gameService->collectReadyPlants($this->farmland);
        $gameService->seedPlants($this->farmland);
        $gameService->waterPlants($this->farmland);

        $job = (new ProcessFarmland($this->farmland))
            ->delay(Carbon::createFromTimestamp($this->farmland->remain)->addMinutes(2));

        dispatch($job);
    }
}
