<div class="card ">
    <div class="card-header">
        Activities
    </div>

    <table class="table table-sm">
        <tr>
            <th>
                Datetime
            </th>
            <th>
                Message
            </th>
        </tr>
        @foreach($activities as $activity)
            <tr>
                <td>
                    {{ $activity->created_at }}
                </td>
                <td>
                    {{ $activity->message }}
                </td>
            </tr>
        @endforeach
    </table>
</div>
