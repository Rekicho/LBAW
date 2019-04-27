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
                <div class="my-3 form-edit-profile light-main-color-bg px-3">
                    <h3>Account Information</h3>

                    <form id="updateEmail">
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Email address" value="{{$user->email}}" />
                        </div>
                        <input type="hidden" name="user_id" id="user_id" class="form-control" value={{$user->id}} />
                        <button class="btn btn-lg btn-primary my-2 float-right" type="submit">
                            Edit
                        </button>
                    </form>
    
                    <form id="updatePassword">
                        <div class="form-group">
                            <label for="new_password">New password</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New password" />
                        </div>
                        <div class="form-group">
                            <label for="new_password_confirmation">Confirm New password</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" placeholder="Confirm New password" />
                        </div>

                        @if ($errors->has('password'))
                        <span class="error">
                            {{ $errors->first('password') }}
                        </span>
                      @endif
                      
                        <div class="form-group">
                            <label for="old_password">Old password</label>
                            <input type="password" name="old_password" id="old_password" class="form-control" placeholder="Old password" />
                        </div>

                        @if ($errors->has('password'))
                        <span class="error">
                            {{ $errors->first('password') }}
                        </span>
                      @endif

                        <input type="hidden" name="user_id" class="form-control" value={{$user->id}} />
                   
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
</div>
@endsection