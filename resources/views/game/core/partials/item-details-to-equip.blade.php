<h6>Item Details</h6>
<dl>
    <dt>Name:</dt>
    <dd>{{$item->name}}</dd>
    <dt>Base Damage:</dt>
    <dd>{{$item->base_damage}}</dd>
    <dt>Base AC:</dt>
    <dd>{{$item->base_ac}}</dd>
    <dt>Type:</dt>
    <dd>{{$item->type}}</dd>
</dl>
<hr />

<h6>Item Affixes</h6>
@if (is_null($item->itemPrefix) && is_null($item->itemSuffix))
    <div class="alert alert-info">
        There are no affixes on this item.
    </div>
@else
    @if (!is_null($item->prefix))
        <dl>
            <dt>Name:</dt>
            <dd>{{$item->itemPrefix->name}}</dd>
            <dt>Base Damage:</dt>
            <dd>{{$item->itemPrefix->base_damage_mod * 100}}%</dd>
            <dt>Base Ac:</dt>
            <dd>{{$item->itemPrefix->base_ac_mod * 100}}%</dd>
            <dt>Description:</dt>
            <dd>{{$item->itemPrefix->description}}</dd>
        </dl>
    @endif
    @if (!is_null($item->prefix))
        <dl>
            <dt>Name:</dt>
            <dd>{{$item->itemSuffix->name}}</dd>
            <dt>Base Damage:</dt>
            <dd>{{$item->itemSuffix->base_damage_mod * 100}}%</dd>
            <dt>Description:</dt>
            <dd>{{$item->itemSuffix->description * 100}}%</dd>
        </dl>
    @endif
@endif