

{!! Form::open(['url' => route('spaces.addTask', $space->id), 'class' => 'form-inline' ]) !!}
    {{ Form::hidden('player_id', $player->id) }}

    <div class="form-group form-group-sm">
        {{ Form::label('plant_id', 'Plant', ['class' => 'sr-only']) }}
        {{ Form::select('plant_id', $plantsToSeed, null, ['placeholder' => '--- Select ---', 'class' => 'form-control']) }}
    </div>
    {{ Form::submit('Save', ['class' => 'btn btn-default btn-sm']) }}

{!! Form::close() !!}
