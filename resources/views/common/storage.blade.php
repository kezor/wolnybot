<div class="panel panel-default">
    <div class="panel-heading">
        Storage
    </div>

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
                    {{ $product->getName() }}
                </td>
                <td>
                    {{ $product->amount }}
                </td>
            </tr>
        @endforeach
    </table>
</div>
