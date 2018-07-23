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

        $job = new CollectPlants();

        $currentTasks = Task::where('space_id', $spaceId)
            ->where('job_name', $job->getName())
            ->where('status', Task::TASK_STATUS_ACTIVE)
            ->get();

        if($currentTasks->count() !== 0){
            Session::flash('error', 'You already have this kind of job for this space.');
            return back();
        }

        $farmland = Farmland::find($spaceId);

        $productToSeed = Product::where('pid', $plantToSeed)
            ->where('player_id', $player->id)
            ->first();

        $job->productToSeed = $productToSeed;
        $job->farmland = $farmland;
        $job->goal = 10000;

        $task = new Task();
        $task->job = serialize($job);
        $task->player_id = $player->id;
        $task->status = Task::TASK_STATUS_ACTIVE;
        $task->job_name = $job->getName();
        $task->space_id = $spaceId;
        $task->save();

        return back();
    }
}