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
        if ($this->task->isCanceled()) {
            return;
        }
        $player = Player::find($this->task->player_id);

        $gameService = new GameService($player);

        $gameService->updateStock();

        $gameService->updateBuildings();

        /** @var CollectPlants $collectPlants */
        $collectPlants = unserialize($this->task->job);

        $farmland = Farmland::find($collectPlants->farmland->id);

        $farmland->fillInFields();

        $gameService->collectReadyPlants($farmland);

        if($this->task->isCancelationPending()){
            $this->task->status = Task::TASK_STATUS_CANCELED;
            $this->task->save();
            return;
        }

        /** @var Product $productFromStock */
        $productFromStock = Product::where('player_id', $player->id)
            ->where('pid', $collectPlants->productToSeed->pid)
            ->first();

        if ($collectPlants->goal > $productFromStock->amount) {

            $gameService->seedPlants($farmland, $productFromStock);

            $gameService->waterPlants($farmland);


            $job = (new ProcessFarmland($this->task))
                ->delay(Carbon::createFromTimestamp($farmland->remain)->addMinutes(2));
            dispatch($job);
        }
    }
}
