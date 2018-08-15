<?php

namespace Tests\Feature;

use App\Task;
use Carbon\Carbon;
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
        $task = create(Task::class);

        $this->get(route('tasks'))
            ->assertSee($task->name);
    }

    public function test_a_task_requires_a_name()
    {
        $this->createTask(['name' => null])
            ->assertSessionHasErrors('name');
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

    public function test_tasks_can_be_sorted_by_user()
    {
        $this->signIn();

        $firstTask = create(Task::class, [
            'attributes' => [
                'user_id' => auth()->id()
            ]
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $firstTask->id,
            'user_id' => auth()->id()
        ]);

        $newUser = $this->signIn();

        $secondTask = create(Task::class, [
            'attributes' => [
                'user_id' => auth()->id()
            ]
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $secondTask->id,
            'user_id' => auth()->id()
        ]);

        $thirdTask = create(Task::class);

        $this->assertDatabaseHas('tasks', [
            'id' => $thirdTask->id
        ]);

        $response = $this->getJson("tasks?users=desc")->json();

        $this->assertEquals([
            $thirdTask->id,
            $secondTask->id,
            $firstTask->id
        ], array_column($response, 'user_id'));

        $response = $this->getJson("tasks?users=asc")->json();

        $this->assertEquals([
            $firstTask->id,
            $secondTask->id,
            $thirdTask->id
        ], array_column($response, 'user_id'));

    }

    public function test_a_task_may_have_a_due_date()
    {
        $this->signIn();
        $dueDate = Carbon::tomorrow();
        $task = create(Task::class, [
            'attributes' => [
                'due_date' => $dueDate
            ]
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'due_date' => $dueDate
        ]);
    }

    public function test_tasks_can_be_sorted_by_due_date_earliest_first()
    {
        $dueDateIsToday = Carbon::today();
        $dueDateIsTomorrow = Carbon::tomorrow();

        $response = $this->createTasksAndSort(
            $dueDateIsToday,
            $dueDateIsTomorrow,
            'asc'
        );

        $this->assertEquals([
            $dueDateIsToday->toDateTimeString(),
            $dueDateIsTomorrow->toDateTimeString(),
            null,
        ], array_column($response, 'due_date'));
    }

    public function test_tasks_can_be_sorted_by_due_date_latest_first()
    {
        $dueDateIsToday = Carbon::today();
        $dueDateIsTomorrow = Carbon::tomorrow();

        $response = $this->createTasksAndSort(
            $dueDateIsToday,
            $dueDateIsTomorrow,
            'desc'
        );

        $this->assertEquals([
            $dueDateIsTomorrow->toDateTimeString(),
            $dueDateIsToday->toDateTimeString(),
            null,
        ], array_column($response, 'due_date'));
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

    protected function createTasksAndSort(
        $firstTaskDueDate,
        $secondTaskDueDate,
        $direction
    ){
        $this->signIn();

        // Task with today as due date
        create(Task::class, [
            'attributes' => [
                'due_date' => $firstTaskDueDate
            ]
        ]);

        // Task with tomorrow as due date
        create(Task::class, [
            'attributes' => [
                'due_date' => $secondTaskDueDate
            ]
        ]);

        // Task with no due date
        create(Task::class);

        return $this->getJson("tasks?duedate=$direction")->json();
    }
}
