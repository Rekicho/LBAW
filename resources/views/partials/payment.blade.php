<tr id="purchase-{{$payment->id_purchase}}">
    <th scope="row">{{$payment->date_time}}</th>
    <td>{{$payment->id_purchase}}</td>
    <td>
        <form class="confirmPurchasePaymentForm">
        <input type="hidden" data-state="{{$payment->purchase_state}}" name="id_purchase" value="{{$payment->id_purchase}}">
        <button type="submit" class="btn btn-success btn-sm">
            <i class="fas fa-check-circle"></i>
        </button>
    </form>
    </td>
</tr>