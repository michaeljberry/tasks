@forelse ($tasks as $task)
    <div>{{ $task->name }}</div>
    <div>{{ $task->status }}</div>
@empty
    <p>You currently have no tasks</p>
@endforelse