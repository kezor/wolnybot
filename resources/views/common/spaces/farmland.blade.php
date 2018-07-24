{!! Form::open(['url' => route('spaces.addTask', $farmland->id), 'class' => 'form-inline' ]) !!}
{{ Form::hidden('player_id', $player->id) }}

<div class="form-group">
    {{ Form::label('plant_id', 'Plant', ['class' => 'sr-only']) }}
    {{ Form::select('plant_id', $plantsToSeed, null, ['placeholder' => '--- Select ---', 'class' => 'form-control  form-control-sm']) }}
</div>
{{ Form::submit('Save', ['class' => 'btn btn-secondary btn-sm']) }}

{!! Form::close() !!}


<table class="table table-sm table-borderless">
    @for($i = 0; $i <10; $i++)
        <tr>
            @for($j = 1; $j <=12; $j++)
                @php ($index = (($i * 12) + $j))
                <td>
                    @php ($field = $farmland->getFieldAtIndex($index))
                    {!!  $field->getStatusIcon() !!}
                </td>
            @endfor
        </tr>
    @endfor
</table>
<a href="{{ route('farmland.cropGarden', $farmland->id) }}" class="btn btn-secondary btn-sm">Crop Garden</a>

{!! Form::open(['url' => route('farmland.seedOnce', $farmland->id), 'class' => 'form-inline' ]) !!}

<div class="form-group">
    {{ Form::label('plant_id', 'Plant', ['class' => 'sr-only']) }}
    {{ Form::select('plant_id', $plantsToSeed, null, ['placeholder' => '--- Select ---', 'class' => 'form-control form-control-sm']) }}
</div>
{{ Form::submit('Seed', ['class' => 'btn btn-secondary btn-sm']) }}

{!! Form::close() !!}

@include('common.activities', ['activities' => $farmland->getActivities()])
