@extends('layouts.app')

@section('css')
<link href="{{ asset('css/profile.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-profile m-md-5 px-0">
    <div class="row">
        <div class="col-md-3 mb-3 profile-aside">
            <div id="profile" class="light-main-color-bg">
                <div class="text-center">
                <b>@ {{$user->username}}</b>
                </div>
                @if($billingInfo != null)
                <div class="text-center">
                    <i class="fas fa-signature"></i> <?php
                        $name = explode(" ", $billingInfo->full_name);
                        $firstName = $name[0];
                        $lastName = $name[count($name) - 1];
                        echo($firstName . " " . $lastName);?>
                </div>
                @endif
                @if($user->email != '')
                <div class="text-center">
                    <i class="far fa-envelope"></i> {{$user->email}}
                </div>
                @endif
                @if($billingInfo != null)
                <div class="text-center mb-3">
                    <i class="fas fa-map-marker"></i> {{$billingInfo->city}}
                </div>
                @endif
                <a href="#history" data-toggle="collapse" data-target="#history">
                    <div class="text-center">
                        <i class="fas fa-history"></i> Purchase History
                    </div>
                </a>
                <a href="#wishlist" data-toggle="collapse" data-target="#wishlist">
                    <div class="text-center">
                        <i class="fas fa-star"></i> Wish List
                    </div>
                </a>
                <a href="#edit" data-toggle="collapse" data-target="#edit">
                    <div class="text-center pb-3">
                        <i class="fas fa-edit"></i> Edit Profile
                    </div>
                </a>
                <a href={{route('logout')}}>
                    <div class="text-center pb-3">
                        <i class="fas fa-power-off"></i> Log out
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-9 text-center profile-right">
            <div id="history" class="collapse show">
                <h2><i class="fas fa-history"></i> Purchase history</h2>
                <div id="accordion">
                    @if($purchaseHistory != null)
                    @each('partials.purchase', $purchaseHistory, 'purchase')
                    @endif
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link w-100 p-0" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true"
                                 aria-controls="collapseOne">
                                    <div class="cart-info">
                                        <span class="float-left">Date: 23/4/2019 23h45</span>
                                        <span class="float-right">Total price: 100€</span>
                                        <br style="clear:both">
                                        <div class="float-left step-by-step mt-3">
                                            <i class="fas fa-circle pay-wait"></i>Waiting for payment: 22/3/2019
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
                    <div class="card">
                        <div class="card-header" id="headingTwo">
                            <h5 class="mb-3">
                                <button class="btn btn-link collapsed w-100 p-0" data-toggle="collapse" data-target="#collapseTwo"
                                 aria-expanded="false" aria-controls="collapseTwo">
                                    <div class="cart-info">
                                        <span class="float-left">Date: 23/4/2019 23h45</span>
                                        <span class="float-right">Total price: 19€</span>
                                        <br style="clear:both">
                                        <div class="float-left step-by-step mt-3">
                                            <i class="fas fa-circle pay-wait"></i>Waiting for payment: 22/3/2019
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
                                        <div class="float-left step-by-step mt-3">
                                            <i class="fas fa-circle pay-complt"></i> Shipped: 25/3/2019
                                        </div>
                                        <br style="clear: both">
                                        <div class="float-left step-by-step mt-3 deactivate">
                                            <i class="fas fa-circle pay-complt deactivate"></i> Completed
                                        </div>
                                    </div>
                                </button>
                                <div class="float-center mt-3">
                                    <a href="#" class="btn btn-primary received"><i class="fas fa-clipboard-check confirm pr-2"></i>Confirm
                                        Reception</a>
                                </div>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
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
                                        10/03/2019 11:11
                                        <div>
                                            <a href="product.html"><button type="button" class="btn btn-secondary">Review</button></a>
                                        </div>
                                        <span class="price float-right">3 x 0,69€</span>
                                    </div>
                                </div>

                                <hr>

                                <div class="single-product-info-container">
                                    <a href="product.html"><img src="http://www.spalding-basketball.com/fileadmin/_processed_/8/9/csm_3001511010317_b_5bc408da7c.jpg"
                                         alt=""></a>
                                    <div class="single-product-info-text">
                                        <div class="row">
                                            <div class="col-6">
                                                <a href="product.html"><span class="title">Football</span></a>
                                            </div>
                                        </div>
                                        10/03/2019 11:11
                                        <div>
                                            <a href="product.html"><button type="button" class="btn btn-secondary">Review</button></a>
                                        </div>
                                        <span class="price float-right">1 x 0,69€</span>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div id="wishlist" class="collapse">
                <h2><i class="fas fa-star"></i> Wish List</h2>
                <ul>
                    @each('partials.productCard', $wishlist, 'product')
            </ul>
        </div>
        <div id="edit" class="collapse">
            <div class="mb-2">
                <h2><i class="fas fa-edit"></i> Edit Profile</h2>
            </div>
            <form class="form-edit-profile light-main-color-bg px-3" action="profile.html">
                <div class="my-3">
                    <h3>Profile Information</h3>
                </div>
                <div class="form-group">
                    <label for="realName">Real Name</label>
                    <input type="text" id="realName" class="form-control" placeholder="Real Name" value="Bruno Sousa" />
                </div>
                <div class="form-group">
                    <label for="prof_email">Email address</label>
                    <input type="email" id="prof_email" class="form-control" placeholder="Email address" value={{$user->email}} />
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="prof_city" class="form-control" placeholder="City" value="Paredes" />
                </div>
                <button class="btn btn-lg btn-primary my-2 float-right" type="submit">
                    Edit
                </button>
            </form>
            <form class="form-edit-billing light-main-color-bg px-3" id="billingInfo">
                <div class="my-3">
                    <h3>Shipping & Billing Information</h3>
                </div>

                @if($billingInfo == null)
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" name="full_name" id="fullName" class="form-control" placeholder="Full Name" />
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" name="address" id="address" class="form-control" placeholder="Address" />
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" name="city" id="city" class="form-control" placeholder="City" />
                    </div>
                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" name="state" id="state" class="form-control" placeholder="State" />
                    </div>
                    <div class="form-group">
                        <label for="zip">Zip Code</label>
                        <input type="text" name="zip_code" id="zip" class="form-control" placeholder="zip" />
                    </div>
                @else
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" name="full_name" id="fullName" class="form-control" placeholder="Full Name" value="{{$billingInfo->full_name}}" />
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" name="address" id="address" class="form-control" placeholder="Address" value="{{$billingInfo->address}}" />
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" name="city" id="city" class="form-control" placeholder="City" value="{{$billingInfo->city}}" />
                    </div>
                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" name="state" id="state" class="form-control" placeholder="State" value="{{$billingInfo->state}}" />
                    </div>
                    <div class="form-group">
                        <label for="zip">Zip Code</label>
                        <input type="text" name="zip_code" id="zip" class="form-control" placeholder="zip" value="{{$billingInfo->zip_code}}" />
                    </div>
                    <input type="hidden" name="id" value={{$billingInfo->id}}>
                @endif
                <button class="btn btn-lg btn-primary my-2 float-right" type="submit">
                    Edit
                </button>
            </form>
            <form class="form-edit-profile light-main-color-bg px-3" action="profile.html">
                <div class="my-3">
                    <h3>Account Information</h3>
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" id="email" class="form-control" placeholder="Email address" value={{$user->email}} />
                </div>
                <div class="form-group">
                    <label for="newPassword">New password</label>
                    <input type="password" id="newPassword" class="form-control" placeholder="New password" />
                </div>
                <div class="form-group">
                    <label for="confirmNewPassword">Confirm New password</label>
                    <input type="password" id="confirmNewPassword" class="form-control" placeholder="Confirm New password" />
                </div>
                <div class="form-group">
                    <label for="oldPassword">Old password</label>
                    <input type="password" id="oldPassword" class="form-control" placeholder="Old password" />
                </div>
                <button class="btn btn-lg btn-primary my-2 float-right" type="submit">
                    Edit
                </button>
            </form>
        </div>
    </div>
</div>
</div>
</div>
</div>
@endsection