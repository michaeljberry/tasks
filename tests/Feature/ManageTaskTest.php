<?php

namespace Tests\Feature;

use App\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TaskTest extends TestCase
{
    use DatabaseMigrations;

    public function test_guests_may_not_view_tasks()
    {
        $this->withExceptionHandling();

        $this->get(route('tasks'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_may_not_create_tasks()
    {
        $this->withExceptionHandling();

        $this->get(route('create-task'))
            ->assertRedirect(route('login'));

        $this->post(route('tasks'))
            ->assertRedirect(route('login'));
    }

    public function test_an_authorized_user_can_create_tasks()
    {
        $this->withExceptionHandling()->signIn();
        $task = make(Task::class);

        $response = $this->post(route('tasks'), $task->toArray());

        $this->get($response->headers->get('Location'))
            ->assertSee($task->name);
    }

    public function test_an_authorized_user_can_view_tasks()
    {
        $this->signIn();
        $this->task = create(Task::class);

        $this->get(route('tasks'))
            ->assertSee($this->task->name);
    }

    public function test_a_task_may_be_marked_as_complete()
    {
        $this->signIn();
        $task = create(Task::class);

        $this->markTaskAsComplete($task);
    }

    public function test_a_completed_task_may_be_marked_as_incomplete()
    {
        $this->signIn();
        $task = create(Task::class);

        $this->markTaskAsComplete($task);

        $this->markTaskAsIncomplete($task);
    }

    public function test_a_task_can_be_assigned_a_default_user()
    {
        $this->signIn();
        $task = create(Task::class, [
            'attributes' => [
                'user_id' => auth()->id()
            ]
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => auth()->id()
        ]);
    }

    public function test_a_tasks_user_can_be_changed()
    {
        $this->signIn();

        $task = create(Task::class, [
            'attributes' => [
                'user_id' => auth()->id()
            ]
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => auth()->id()
        ]);

        $newUser = $this->signIn();

        $this->patch($task->path() . "/user", ['user_id' => auth()->id()]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => auth()->id()
        ]);
    }

    public function createTask($overrides = [])
    {
        $this->withExceptionHandling()->signIn();

        $task = make(Task::class, ['attributes' => $overrides]);

        return $this->post(route('tasks'), $task->toArray());
    }

    public function markTaskAsComplete(Task $task)
    {
        $complete = 1;

        $this->patch($task->path() . "/status", ['status' => $complete]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => $complete
        ]);
    }

    public function markTaskAsIncomplete(Task $task)
    {
        $incomplete = 0;

        $this->patch($task->path() . "/status", ['status' => $incomplete]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => $incomplete
        ]);
    }
}
