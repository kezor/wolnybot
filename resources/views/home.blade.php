@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>

                    <div class="panel-body">
                        <p>Players list</p>
                        <table class="table table-bordered">
                            <tr>
                                <th>Username</th>
                                <th>Server ID</th>
                            </tr>
                            @foreach($players as $player )
                                <tr>
                                    <td>{{$player->username}}</td>
                                    <td>{{$player->server_id}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
