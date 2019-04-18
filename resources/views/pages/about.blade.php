@extends('layouts.app')

@section('content')

<div class="container">
    <h1 class="mt-3 mb-3 space-title">About <span class="sec-color">KEY</span>VALUE</h1>
    <p class="max-text-width float-left p-1">Nowadays, with the growth of the internet and delivery services, we feel
        that most people shouldn't need to travel
        to the closest big city in order to buy what they want. Furthermore, there are products that we can't even find in
        our local shops. Online shopping is the future. Users can navigate our catalog using an advanced search feature,
        save their favorite items and add them to a
        shopping cart. They can sign up or log in either by typing their personal information or just by using an OAuth
        method (Facebook and Google).</p>
    <p class="max-text-width float-left p-1"> They can later check their cart, remove any unwanted item and ultimately
        buy them,
        paying either offline or using another method, such as PayPal. The products will be grouped by their categories,
        brand, price, and popularity. In addition to that, the system will offer a responsive design capable of giving the
        best experience to the user, both in desktop, tablet or smartphone.</p>
    <section class="pb-3 mt-3 ourteam">
        <h2 class="my-3 space-title">Our Team</h2>
        <div class="row text-center py-3">
            <div class="col-md-3 d-flex justify-content-center mb-3">
                <div class="card text-center bg-secondary" style="width: 14rem;">
                    <img class="card-img-top img-fluid" src="images/201604145.jpg" alt="Bruno Sousa">
                    <div class="card-body">
                        <h5 class="card-title">Bruno Sousa</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex justify-content-center mb-3">
                <div class="card text-center bg-secondary" style="width: 14rem;">
                    <img class="card-img-top img-fluid" src="images/201603846.jpg" alt="Pedro Fernandes">
                    <div class="card-body">
                        <h5 class="card-title">Pedro Fernandes</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex justify-content-center mb-3">
                <div class="card text-center bg-secondary" style="width: 14rem;">
                    <img class="card-img-top img-fluid" src="images/201604470.jpg" alt="Pedro Silva">
                    <div class="card-body">
                        <h5 class="card-title">Pedro Silva</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex justify-content-center mb-3">
                <div class="card text-center bg-secondary" style="width: 14rem;">
                    <img class="card-img-top img-fluid" src="images/201605422.jpg" alt="Simão Silva">
                    <div class="card-body">
                        <h5 class="card-title">Simão Silva</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection