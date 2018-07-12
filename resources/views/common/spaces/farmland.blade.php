{!! Form::open(['url' => route('spaces.addTask', $farmland->id), 'class' => 'form-inline' ]) !!}
{{ Form::hidden('player_id', $player->id) }}

<div class="form-group form-group-sm">
    {{ Form::label('plant_id', 'Plant', ['class' => 'sr-only']) }}
    {{ Form::select('plant_id', $plantsToSeed, null, ['placeholder' => '--- Select ---', 'class' => 'form-control']) }}
</div>
{{ Form::submit('Save', ['class' => 'btn btn-default btn-sm']) }}

{!! Form::close() !!}


<table class="table">
    @for($i = 0; $i <10; $i++)
        <tr>
            @for($j = 1; $j <=12; $j++)
                @php ($index = (($i * 12) + $j))
                <td>
                    @php ($field = $farmland->getFieldAtIndex($index))
                    {{$field->phase}}
                </td>
            @endfor
        </tr>
    @endfor
</table>
<a href="{{ route('farmland.cropGarden', $farmland->id) }}" class="btn btn-default btn-sm">Crop Garden</a>

{!! Form::open(['url' => route('farmland.seedOnce', $farmland->id), 'class' => 'form-inline' ]) !!}

<div class="form-group form-group-sm">
    {{ Form::label('plant_id', 'Plant', ['class' => 'sr-only']) }}
    {{ Form::select('plant_id', $plantsToSeed, null, ['placeholder' => '--- Select ---', 'class' => 'form-control']) }}
</div>
{{ Form::submit('Seed', ['class' => 'btn btn-default btn-sm']) }}

{!! Form::close() !!}

@include('common.activities', ['activities' => $farmland->getActivities()])
