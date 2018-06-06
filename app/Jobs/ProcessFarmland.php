<?php

namespace App\Jobs;

use App\Building\Farmland;
use App\Connector\WolniFarmerzyConnector;
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
        $connector = new WolniFarmerzyConnector();
        $player = $this->farmland->farm->player;
        $connector->login($player);

        $this->farmland->setConnector($connector);

        $this->farmland->fillInFields();

        $this->farmland->updateFields();

        $this->farmland->process();

        $job = (new ProcessFarmland($this->farmland))
            ->delay(Carbon::createFromTimestamp($this->farmland->remain)->addMinutes(2));

        dispatch($job);
    }
}
