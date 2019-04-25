<div class="card">
    <div class="card-header" id="headingOne">
        <h5 class="mb-0">
            <button class="btn btn-link w-100 p-0" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true"
             aria-controls="collapseOne">
                <div class="cart-info">
                <span class="float-left">Date: {{$purchase->purchase_date}}</span>
                    <span class="float-right">Total price: 100€</span>
                    <br style="clear:both">
                    <div class="float-left step-by-step mt-3">
                            @if($purchase!=null)
                            <i class="fas fa-circle pay-wait"></i>Waiting for payment: {{$purchase->log_date}}
                            @else
                            <i class="fas fa-circle pay-wait deactivate"></i>Waiting for payment
                            @endif
                    </div>
                    <br style="clear: both">
                    <div class="float-left step-by-step mt-3">
                        <i class="fas fa-circle pay-wait-apprv"></i>Waiting for approval: 23/3/2019
                    </div>
                    <br style="clear: both">
                    <div class="float-left step-by-step mt-3">
                        <i class="fas fa-circle pay-paid"></i> Paid: 24/3/2019
                    </div>
                    <br style="clear: both">
                    <div class="float-left step-by-step mt-3 deactivate">
                        <i class="fas fa-circle pay-complt deactivate"></i> Shipped
                    </div>
                    <br style="clear: both">
                    <div class="float-left step-by-step mt-3 deactivate">
                        <i class="fas fa-circle pay-complt deactivate"></i> Completed
                    </div>
                </div>
            </button>
        </h5>
    </div>

    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
        <div class="card-body">
            <div class="single-product-info-container">
                <a href="product.html"><img src="https://i5.walmartimages.ca/images/Large/020/6_1/999999-628915250206_1.jpg"
                     alt=""></a>
                <div class="single-product-info-text">
                    <div class="row">
                        <div class="col-6">
                            <a href="product.html"><span class="title">Football</span></a>
                        </div>
                    </div>
                    10/03/2019 22:22
                    <div>
                        <a href="product.html"><button type="button" class="btn btn-secondary">Review</button></a>
                    </div>
                    <span class="price float-right">5 x 10,00€</span>
                </div>
            </div>
            <hr>
            <div class="single-product-info-container">
                <a href="product.html"><img src="https://images-na.ssl-images-amazon.com/images/I/61A5lkj1eXL._SX425_.jpg"
                     alt=""></a>
                <div class="single-product-info-text">
                    <div class="row">
                        <div class="col-6">
                            <a href="product.html"><span class="title">Pet Ball</span></a>
                        </div>
                    </div>
                    29/02/2019 12:21
                    <div>
                        <a href="product.html"><button type="button" class="btn btn-secondary">Review</button></a>
                    </div>
                    <span class="price float-right">10 x 5,00€</span>
                </div>
            </div>
        </div>
    </div>
</div>