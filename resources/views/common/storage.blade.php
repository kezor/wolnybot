<div class="card">
    <div class="card-header">
        Storage
    </div>
    <ul class="list-group list-group-flush">
        @foreach($player->products as $product)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $product->getName() }}
                <span class="badge badge-primary badge-pill">{{ $product->amount }}</span>
            </li>
        @endforeach
    </ul>
</div>