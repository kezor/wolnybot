@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Add new player</div>

                    <div class="panel-body">
                        {!! Form::open(['route' => 'player.store']) !!}
                        <div>
                            {{ Form::label('username', 'Username') }}
                            {{ Form::text('username', 'Username') }}
                        </div>
                        <div>
                            {{ Form::label('password', 'Password') }}
                            {{ Form::text('password', 'Password') }}
                        </div>
                        <div>
                            {{ Form::label('server_id', 'Server ID') }}
                            {{ Form::number('server_id', 'Server ID') }}
                        </div>
                        <div>
                            {{ Form::submit('Save') }}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
