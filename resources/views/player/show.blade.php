@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>{{ $player->username }}</strong> details</div>

                    <div class="panel-body">
                        <p>
                            <a href="{{ route('player.updateData', $player->id) }}" class="btn btn-sm btn-info">Update data</a>
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
                                        {{ $product->pid }}
                                    </td>
                                    <td>
                                        {{ $product->amount }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>

                        @foreach($player->farms as $farm)

                            @foreach($farm as $space)
                                {{ $space-> }}
                            @endforeach

                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
