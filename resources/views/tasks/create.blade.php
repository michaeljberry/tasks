<form method="POST" action="/tasks">
    @csrf
    <div>
        <label for="name">Task Name:</label>
        <input type="text" id="name" name="name" required>
    </div>
</form>