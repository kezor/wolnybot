<?php

namespace App\Http\Controllers;


use App\Building\Farmland;
use App\Player;
use App\Product;
use App\Task;
use App\Tasks\CollectPlants;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class SpacesController extends Controller
{
    public function addTask($spaceId)
    {
        $player = Player::find(Input::get('player_id'));
        $plantToSeed = Input::get('plant_id');

        $collectPlantsJob = new CollectPlants();

        $currentTasks = Task::where('space_id', $spaceId)
            ->where('job_name', $collectPlantsJob->getName())
            ->where('status', Task::TASK_STATUS_ACTIVE)
            ->get();

        if($currentTasks->count() !== 0){
            Session::flash('error', 'You already have task for this field.');
            return back();
        }

        $farmland = Farmland::find($spaceId);

        $productToSeed = Product::where('pid', $plantToSeed)
            ->where('player_id', $player->id)
            ->first();

        $collectPlantsJob->productToSeed = $productToSeed;
        $collectPlantsJob->farmland = $farmland;
        $collectPlantsJob->goal = 10000;

        $task = new Task();
        $task->job = serialize($collectPlantsJob);
        $task->player_id = $player->id;
        $task->status = Task::TASK_STATUS_ACTIVE;
        $task->job_name = $collectPlantsJob->getName();
        $task->space_id = $spaceId;
        $task->save();

        return back();
    }
}