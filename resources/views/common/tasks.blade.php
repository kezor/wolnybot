<table class="table table-sm">
    <tr>
        <th>
            Task name
        </th>
        <th>
            Status
        </th>
        <th>
            Action
        </th>
    </tr>
    @foreach($tasks as $task)
        <tr>
            <td>
                {{ $task->getJobName() }}
            </td>
            <td>
                {{ $task->getStatusName() }}
            </td>
            <td>
                @if ($task->isActive())
                    <a href="{{ route('tasks.cancel', $task->id) }}">Cancel</a>
                @endif
            </td>
        </tr>
    @endforeach
</table>