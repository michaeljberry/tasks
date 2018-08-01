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
        $this->signIn();
        $task = make(Task::class);

        $response = $this->post(route('tasks'), $task->toArray());

        $this->get($response->headers->get('Location'))
            ->assertSee($task->name);
    }
}
