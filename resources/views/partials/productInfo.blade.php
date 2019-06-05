<tr>
    <th scope="row"><a href="/product{{$product->id}}">{{$product->id}}</a></th>
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
      <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmDisable">
        <i class="fas fa-minus-circle"></i>
        <span class="button-text">Disable</span>
      </button>
      <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
        data-target="#updateStockModal">
        <i class="fas fa-wrench"></i>
        <span class="button-text">Update stock</span>
      </button>
      <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
        data-target="#updatePriceModal">
        <i class="fas fa-euro-sign"></i>
        <span class="button-text">Update price</span>
      </button>
    </td>
  </tr>