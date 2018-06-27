<?php

namespace App\Http\Controllers;


use App\Building\Farmland;
use App\Jobs\ProcessFarmland;
use App\Space;
use App\Task;

class TasksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function changeStatus($taskId, $status)
    {
        $task = Task::find($taskId);

        $task->status = $status;
        $task->save();

        return back();
    }

}