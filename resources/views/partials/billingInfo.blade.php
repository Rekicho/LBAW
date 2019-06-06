<form class="form-edit-billing light-main-color-bg px-3 billingInfo" data-id={{$billingInfo->id}}>
        <div class="form-group">
            <label for="fullName">Full Name</label>
            <input type="text" id="fullName" name="full_name" class="form-control" placeholder="Full Name" value="{{$billingInfo->full_name}}" />
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" class="form-control" placeholder="Address" value="{{$billingInfo->address}}" />
        </div>
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" id="city" name="city" class="form-control" placeholder="City" value="{{$billingInfo->city}}" />
        </div>
        <div class="form-group">
            <label for="state">State</label>
            <input type="text" id="state" name="state" class="form-control" placeholder="State" value="{{$billingInfo->state}}" />
        </div>
        <div class="form-group">
            <label for="zip">Zip Code</label>
            <input type="text" id="zip" name="zip_code" class="form-control" placeholder="zip" value="{{$billingInfo->zip_code}}" />
        </div>
        <input type="hidden" name="id" value={{$billingInfo->id}}>
    <button class="btn btn-lg btn-primary my-2 float-right" type="submit">
        Edit
    </button>
</form>