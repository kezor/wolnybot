<?php

namespace App\Http\Controllers;

use App\Task;

class TasksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function cancel($taskId){
        $task = Task::find($taskId);

        $task->status = Task::TASK_STATUS_CANCELED;
        $task->save();

        return back();
    }

}