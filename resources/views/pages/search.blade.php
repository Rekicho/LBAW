@extends('layouts.app')


@section('css')
<link href="{{ asset('css/search.css') }}" rel="stylesheet">
@endsection

@section('content')
<nav class="navbar navbar-expand-lg navbar-light lightgrey dept-navbar">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a href="" class="dept-navbar-item">
                <span>
                    Books
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a href="" class="dept-navbar-item">
                <span>
                    Movies
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a href="" class="dept-navbar-item">
                <span>
                    Games
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a href="" class="dept-navbar-item">
                <span>
                    Clothes
                </span>
            </a>
        </li>
    </ul>
</nav>
<!-- 
    MAIN NAV ======================================================================================================
-->

<div class="search-result-bar bg-light">
    <span class="result-count">
        1-16 of {{count($products)}} results for <span class="sec-color font-weight-bold">{{$query}}</span>
    </span>
    <div class="filter-container">
        <span class="cart fa-stack has-badge" data-count="0" data-toggle="modal" data-target="#exampleModal">
            <i class="fas fa-filter filter-icon fa-stack-1x nav-icon"></i>
        </span>
    </div>
</div>

<ul>
    @each('partials.productCard', $products, 'product')
</ul>

<!-- PAGE TURNER -->

<nav aria-label="table navigation">
    {{ $products->links("pagination::bootstrap-4") }}
</nav>


<!-- ALL THE MODALS -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filters</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
			<form id="filter" action="/search">
				<input type="hidden" name="search" value={{$query}}>
				<label for="categoryPicker">Category: </label>
				<select class="form-control" id="categoryPicker" name="category" form="filter">
					<option selected disabled value>Category</option>
					@foreach ($categories as $category)
					<option value="{{$category->id}}">{{$category->name}}</option>
					@endforeach

				</select>
			</form>
        </div> <!-- end of modal-body -->
        <div class="modal-footer">
            <button type="role" form="filter" class="btn btn-primary">Apply filter</button>
        </div>
    </div>
</div>
</div>
@endsection