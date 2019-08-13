<div class="card ">
    <div class="card-header">Farm #{{ $farm->farm_id }}</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                @if ($farm->hasSpaceAt(1))
                    @include('common.space', ['space' => $farm->getSpace(1)])
                @else
                    @include('common.spaces.space_not_in_use')
                @endif
            </div>
            <div class="col-md-4">
                @if ($farm->hasSpaceAt(2))
                    @include('common.space', ['space' => $farm->getSpace(2)])
                @else
                    @include('common.spaces.space_not_in_use')
                @endif
            </div>
            <div class="col-md-4">
                @if ($farm->hasSpaceAt(3))
                    @include('common.space', ['space' => $farm->getSpace(3)])
                @else
                    @include('common.spaces.space_not_in_use')
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                @if ($farm->hasSpaceAt(4))
                    @include('common.space', ['space' => $farm->getSpace(4)])
                @else
                    @include('common.spaces.space_not_in_use')
                @endif
            </div>
            <div class="col-md-4">
                @if ($farm->hasSpaceAt(5))
                    @include('common.space', ['space' => $farm->getSpace(5)])
                @else
                    @include('common.spaces.space_not_in_use')
                @endif
            </div>
            <div class="col-md-4">
                @if ($farm->hasSpaceAt(6))
                    @include('common.space', ['space' => $farm->getSpace(6)])
                @else
                    @include('common.spaces.space_not_in_use')
                @endif
            </div>
        </div>
    </div>
</div>