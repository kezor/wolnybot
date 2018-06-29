@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
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
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>{{ $player->username }}</strong> details <a href="{{ route('player.updateData', $player->id) }}"
                                                                                                   class="btn btn-sm btn-info">Update data</a></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <div class="row">
                    <div class="col-md-12">
                        @include('common.storage')
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-heading">Farms</div>
                    <div class="panel-body">
                        @foreach($player->farms as $key => $farm)
                            <div class="row">
                                <div class="col-md-12">
                                    @include('common.farm', ['farm' => $farm])
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
