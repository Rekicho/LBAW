@extends('layouts.app')

@section('css')
<link href="{{ asset('css/search.css')}}" rel="stylesheet">
@endsection

@section('content')

<div class="search-result-bar bg-light">
    <span class="result-count">
    Category: <span class="sec-color font-weight-bold">{{$category->name}}</span>
    </span>
    <div class="filter-container">
        <span class="circle mr-2 pr-1"><i class="fas fa-times px-1"></i>&lt;25€</span>
        <span class="circle pr-1"><i class="fas fa-times px-1"></i>Sports</span>
        <span class="cart fa-stack has-badge" data-count="2" data-toggle="modal" data-target="#exampleModal">
            <i class="fas fa-filter filter-icon fa-stack-1x nav-icon"></i>
        </span>
    </div>
</div>
<div class="products">
    <ul>
        @each('partials.productCard', $products, 'product')
</ul>
<div aria-label="table navigation">
    {{ $products->links("pagination::bootstrap-4") }}
</div>
</div>
<!-- PAGE TURNER -->




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

            <!-- Sorting clause -->
            <div class="filter-clause">
                <span class="float-left filter-title fs20">Sort</span>
                <div class="dropdown float-left filter-btn">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                     aria-haspopup="true" aria-expanded="false">
                        Sort by
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">Price: highest first</a>
                        <a class="dropdown-item" href="#">Price: lowest first</a>
                        <a class="dropdown-item" href="#">Highest ranking</a>
                    </div>
                </div>
            </div>
            <br clear="both" />
            <hr />

            <!-- IF IT IS ON STOCK -->
            <div class="filter-clause">
                <div class="form-check">
                    <label class="form-check-label fs20 d-inline-block w-50" for="defaultCheck1">
                        In Stock
                    </label>
                    <input class="form-check-input d-inline-block" type="checkbox" value="" id="defaultCheck1">
                </div>
            </div>
            <hr />

            <!-- Look for a brand only -->
            <div class="filter-clause">
                <div class="form-group">
                    <label for="formGroupExampleInput" class="fs20">By Brand</label>
                    <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Brand">
                </div>
            </div>


            <!-- Other clauses -->

        </div> <!-- end of modal-body -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary main-color-bg">Apply filter</button>
        </div>
    </div>
</div>
</div>

<div class="container">
</div> <!-- container -->
</div>
<!--wrapper-->


@endsection