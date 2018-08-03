<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class TaskStatusController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Task $task)
    {
        $task->update(request(['status']));

        return back();
    }
}
