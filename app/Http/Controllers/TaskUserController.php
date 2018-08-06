<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class TaskUserController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Task $task)
    {
        $task->update(request(['user_id']));

        return back();
    }
}
