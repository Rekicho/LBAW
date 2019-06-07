<tr data-id={{$product->id}}>
    <th scope="row"><a href="/product/{{$product->id}}">{{$product->id}}</a></th>
    <td>{{$product->stock}}</td>
    <td>{{$product->price}}</td>
    <td>
        @if($product->is_enabled)
            Enabled
        @else
            Disabled
        @endif
    </td>
    <td>
        @if($product->is_enabled)
    <button type="button" class="btn btn-danger btn-sm updateProduct" data-id={{$product->id}} data-toggle="modal" data-target="#confirmProductDisable">
            <i class="fas fa-minus-circle"></i>
            <span class="button-text">Disable</span>
          </button>
    @else
    <button type="button" class="btn btn-success btn-sm updateProduct"  data-id={{$product->id}} data-toggle="modal" data-target="#confirmProductEnable">
        <i class="fas fa-plus-circle"></i>
        <span class="button-text">Enable</span>
      </button>
    @endif

      <button type="submit" class="btn btn-primary btn-sm updateProduct"  data-id={{$product->id}} data-toggle="modal"
        data-target="#updateStockModal">
        <i class="fas fa-wrench"></i>
        <span class="button-text">Update stock</span>
      </button>
      <button type="submit" class="btn btn-primary btn-sm updateProduct"  data-id={{$product->id}} data-toggle="modal"
        data-target="#updatePriceModal">
        <i class="fas fa-euro-sign"></i>
        <span class="button-text">Update price</span>
      </button>
      <button type="submit" class="btn btn-primary btn-sm updateProduct"  data-id={{$product->id}} data-toggle="modal"
        data-target="#addProductDiscountModal">
        <i class="fas fa-tags"></i>
        <span class="button-text">Add discount</span>
      </button>
    </td>
  </tr>