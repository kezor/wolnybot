<div class="panel panel-default">
    <div class="panel-heading">
        Activities
    </div>

    <table class="table">
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
