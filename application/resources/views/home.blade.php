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
                <div class="card ">
                    <div class="card-header">Dashboard</div>
                    <div class="card-body">
                        <p>Players list</p>
                        <p>
                            <a href="{{ route('player.create') }}" class="btn btn-secondary">Add new</a>
                        </p>
                        <table class="table table-bordered">
                            <tr>
                                <th>Username</th>
                                <th>Server ID</th>
                                <th>Actions</th>
                            </tr>
                            @foreach($players as $player )
                                <tr>
                                    <td>
                                        <a href="{{ route('player.show', $player->id) }}">
                                            {{$player->username}}
                                        </a>
                                    </td>
                                    <td>{{$player->server_id}}</td>
                                    <td>
                                        <a href="{{ route('player.updateData', $player->id) }}" class="btn btn-sm btn-info">Update data</a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
