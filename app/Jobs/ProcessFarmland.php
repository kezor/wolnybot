<?php

namespace App\Jobs;

use App\Building\Farmland;
use App\Player;
use App\Product;
use App\Service\GameService;
use App\Task;
use App\Tasks\CollectPlants;
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
     * @var Task
     */
    private $task;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $player = Player::find($this->task->player_id);

        $gameService = new GameService($player);
//        var_dump('Update stocke');
        $gameService->updateStock();
//        var_dump('Update buildings');
        $gameService->updateBuildings();

        /** @var CollectPlants $collectPlants */
        $collectPlants = unserialize($this->task->job);

        $farmland = Farmland::find($collectPlants->farmland->id);

        $farmland->fillInFields();

//        var_dump($farmland->fields);

//        var_dump('collect ready plants');
        $gameService->collectReadyPlants($farmland);

//        var_dump("collected");
        /** @var Product $productFromStock */
        $productFromStock = Product::where('player_id', $player->id)
            ->where('pid', $collectPlants->productToSeed->pid)
            ->first();
//        var_dump($productFromStock);
//var_dump($collectPlants->goal);
//var_dump($productFromStock->amount);
        if ($collectPlants->goal < $productFromStock->amount) {

//            var_dump('seed plants');

            $gameService->seedPlants($farmland);
//            var_dump('water plants');

            $gameService->waterPlants($farmland);

//            var_dump('dispach new job plants');

            $job = (new ProcessFarmland($this->task))
                ->delay(Carbon::createFromTimestamp($farmland->remain)->addMinutes(2));
//            var_dump(Carbon::createFromTimestamp($farmland->remain)->addMinutes(2));
//            dispatch($job);
        }
    }
}
