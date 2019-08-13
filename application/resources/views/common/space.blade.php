
<div class="card ">
    <div class="card-header">
        {{ \App\SpaceMapper::getSpaceNameByPid($space->building_type) }}
        Status: {{ $space->getStatus() }}
    </div>
    @include('common.tasks', ['tasks' => $space->tasks])
    <div class="card-body">
        @switch($space->building_type)
            @case(\App\Space::TYPE_FARMLAND)
                @include('common.spaces.farmland', ['farmland' => $space])
            @break
        @endswitch
    </div>
</div>