<div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="item-affix-name">Name: </label>
                <input type="text" class="form-control required" id="item-affix-name" name="name" wire:model="itemAffix.name"> 
                @error('itemAffix.name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="item-affix-type">Type: </label>
                <select class="form-control required" name="item-affix-type" wire:model="itemAffix.type">
                    <option value="">Please select</option>
                    @foreach($types as $type)
                        <option value={{$type}}>{{$type}}</option>
                    @endforeach
                </select>
                @error('itemAffix.type') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="item-affix-description">Description: </label>
                <textarea class="form-control required" name="item-affix-description" wire:model="itemAffix.description"></textarea>
                @error('itemAffix.description') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="item-affix-cost">Cost: </label>
                <input type="number" class="form-control required" id="item-affix-cost" name="name" wire:model="itemAffix.cost"> 
                @error('itemAffix.cost') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>