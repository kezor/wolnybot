@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div>
                    @if ( Session::has('success') )
                        <div class="alert alert-success">
                            <strong>Success!</strong> {{ Session::get('success') }}
                        </div>
                    @endif

                    @if ( Session::has('error') )
                        <div class="alert alert-warning">
                            <strong>Warning!</strong> {{ Session::get('error') }}
                        </div>
                    @endif

                </div>
            </div>
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>{{ $player->username }}</strong> details</div>

                    <div class="panel-body">
                        <p>
                            <a href="{{ route('player.updateData', $player->id) }}" class="btn btn-sm btn-info">Update
                                data</a>
                        </p>
                        <p>
                            Storage
                        </p>
                        <table class="table">
                            <tr>
                                <th>
                                    Item name
                                </th>
                                <th>
                                    Count
                                </th>
                            </tr>
                            @foreach($player->products as $product)
                                <tr>
                                    <td>
                                        {{ $product->getName() }}
                                    </td>
                                    <td>
                                        {{ $product->amount }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>

                        <div>
                            <p>
                                Tasks list
                            </p>
                            <table class="table">
                                <tr>
                                    <th>
                                        Task
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                                @foreach($player->tasks as $task)
                                    <tr>
                                        <td>
                                            {{ $task->getJobName() }}
                                        </td>
                                        <td>
                                            {{ $task->getStatusName() }}
                                        </td>
                                        <td>
                                            @if ($task->isActive())
                                                <a href="{{ route('tasks.changeStatus', ['id' => $task->id, 'status' => \App\Task::TASK_STATUS_CANCELLATON_PENDING]) }}">Disable</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>


                        @foreach($player->farms as $key => $farm)
                            <di>

                                {!! Form::open(['url' => route('player.addTask', $player->id) ]) !!}
                                {{ Form::hidden('space_id', 1) }}

                                <div>
                                    {{ Form::label('plant_id', 'Plant to seed') }}
                                    {{ Form::select('plant_id', $plantsToSeed) }}
                                </div>
                                <div>
                                    {{ Form::submit('Save') }}
                                </div>

                                {!! Form::close() !!}

                            </di>
                            <div class="panel panel-default">
                                <div class="panel-heading">Farm #{{ $farm->farm_id }}</div>
                                <div class="panel-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td>
                                                {{ $farm->getSpaceNameAtPosition(1) }}
                                            </td>
                                            <td>
                                                {{ $farm->getSpaceNameAtPosition(2) }}
                                            </td>
                                            <td>
                                                {{ $farm->getSpaceNameAtPosition(3) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{ $farm->getSpaceNameAtPosition(4) }}
                                            </td>
                                            <td>
                                                {{ $farm->getSpaceNameAtPosition(5) }}
                                            </td>
                                            <td>
                                                {{ $farm->getSpaceNameAtPosition(6) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
