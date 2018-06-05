<?php

namespace App\Jobs;

use App\Building\Farmland;
use App\Connector\WolniFarmerzyConnector;
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
$this->farmland->fillInFields();
        $this->farmland->setConnector(new WolniFarmerzyConnector());
//var_dump('adsa');die;
//        $fields = $this->farmland->getFields();
//        var_dump($fields[1]);die('aaaa');

        $this->farmland->process();


                $fields = $this->farmland->getFields();
        var_dump($fields[3]);die('aaaa');
        $nextRunTime = $this->farmland->getNextHarvestTime();
        dd($nextRunTime);
    }
}
