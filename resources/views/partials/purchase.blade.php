<div class="card">
<div class="card-header" id="heading{{$purchase->id}}">
        <h5 class="mb-0">
            <button class="btn btn-link w-100 p-0" data-toggle="collapse" data-target="#collapse{{$purchase->id}}" aria-expanded="true"
             aria-controls="collapse{{$purchase->id}}">
                <div class="cart-info">
                <span class="float-left">Date: {{$purchase->date_time}}</span>
                <span class="float-right">Total price: {{$purchase->price}}€</span>
                    <br style="clear:both">
                    @if(count($purchase->logs)>0)
                        <div class="float-left step-by-step mt-3">
                            <i class="fas fa-circle pay-wait"></i>Waiting for payment: {{$purchase->logs[0]->date_time}}
                    @else
                        <div class="float-left step-by-step mt-3 deactivate">
                            <i class="fas fa-circle pay-wait deactivate"></i>Waiting for payment
                    @endif
                        </div>
                    <br style="clear: both">
                    @if(count($purchase->logs)>1)
                        <div class="float-left step-by-step mt-3">
                            <i class="fas fa-circle pay-wait-apprv"></i>Waiting for approval: {{$purchase->logs[1]->date_time}}
                    @else
                        <div class="float-left step-by-step mt-3 deactivate">
                            <i class="fas fa-circle pay-wait-apprv deactivate"></i>Waiting for approval
                    @endif
                        </div>
                    <br style="clear: both">
                    @if(count($purchase->logs)>2)
                        <div class="float-left step-by-step mt-3">
                            <i class="fas fa-circle pay-paid"></i>Paid: {{$purchase->logs[2]->date_time}}
                    @else
                        <div class="float-left step-by-step mt-3 deactivate">
                            <i class="fas fa-circle pay-paid deactivate"></i>Paid
                    @endif
                        </div>
                    <br style="clear: both">
                    @if(count($purchase->logs)>3)
                        <div class="float-left step-by-step mt-3">
                            <i class="fas fa-circle pay-complt"></i>Shipped: {{$purchase->logs[3]->date_time}}
                    @else
                        <div class="float-left step-by-step mt-3 deactivate">
                            <i class="fas fa-circle pay-complt deactivate"></i>Shipped
                    @endif
                    </div>
                    <br style="clear: both">
                    @if(count($purchase->logs)>4)
                        <div class="float-left step-by-step mt-3">
                            <i class="fas fa-circle pay-complt"></i>Completed: {{$purchase->logs[4]->date_time}}
                    @else
                        <div class="float-left step-by-step mt-3 deactivate">
                            <i class="fas fa-circle pay-complt deactivate"></i>Completed
                    @endif
                </div>
                </div>
            </button>
        </h5>
    </div>

    <div id="collapse{{$purchase->id}}" class="collapse" aria-labelledby="heading{{$purchase->id}}" data-parent="#accordion">
        <div class="card-body">
            @each('partials.purchasedProductCard', $purchase->products, 'product')
            <hr>
        </div>
    </div>
</div>