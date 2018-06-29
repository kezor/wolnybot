
<div class="panel panel-default">
    <div class="panel-heading">{{ \App\SpaceMapper::getSpaceNameByPid($space->building_type) }}</div>
    @include('common.tasks', ['tasks' => $space->tasks])
    <div class="panel-body">
        @switch($space->building_type)
            @case(\App\Space::TYPE_FARMLAND)
                @include('common.spaces.farmland')
            @break
        @endswitch
    </div>
</div>