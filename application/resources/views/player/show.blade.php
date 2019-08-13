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
                <div class="card ">
                    <div class="card-header"><strong>{{ $player->username }}</strong> details <a href="{{ route('player.updateData', $player->id) }}"
                                                                                                 class="btn btn-sm btn-info">Update data</a></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <div class="row">
                    <div class="col-sm-12">
                        @include('common.storage')
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        @include('common.activities', ['activities' => $player->getActivities()])
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card ">
                    <div class="card-header">Farms</div>
                    <div class="card-body">
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
@endsection
