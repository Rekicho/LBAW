@extends('layouts.backoffice')

@section('css')
<link href="{{ asset('css/accounting.css') }}" rel="stylesheet">

@endsection

@section('script')
    <script src="{{asset('js/accounting.js')}}"></script>
@endsection

@section('content')
<div class="alert alert-primary alert-dismissible fade show" role="alert">
    You have a new transaction to confirm
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>

  <ul class="nav nav-tabs mb-3" id="tasks" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="payments-tab" data-toggle="tab" href="#payments" role="tab"
        aria-controls="payments" aria-selected="true">Payments</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="transfers-tab" data-toggle="tab" href="#transfers" role="tab" aria-controls="transfers"
        aria-selected="false">Transfers</a>
    </li>
  </ul>

  <div class="container">
    <div class="tab-content" id="tasksContent">
      <div class="tab-pane fade show active" id="payments" role="tabpanel" aria-labelledby="payments-tab">
        <div class="input-group mb-2">
          <div class="input-group-prepend">
            <span class="input-group-text search-addon"><i class="fas fa-search"></i></span>
          </div>
          <input class="form-control" id="search-payment" type="text" placeholder="Search...">
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="thead-light">
              <tr>
                <th scope="col"><a href="#">Date <i class="fas fa-sort"></i></a></th>
                <th scope="col">Proof</th>
                <th scope="col">Confirm</th>
              </tr>
            </thead>
            <tbody id="paymentsTable">
                  @foreach ($payments as $payment)
                <tr id="purchase-{{$payment->id_purchase}}">
                    <th scope="row">{{$payment->date_time}}</th>
                    <td>{{$payment->id_purchase}}</td>
                    <td>
                        <form class="confirmPurchasePaymentForm">
                        <input type="hidden" name="id_purchase" value="{{$payment->id_purchase}}">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-check-circle"></i>
                        </button>
                    </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
          </table>
        </div>
        <nav aria-label="table navigation">
                {{-- {{ $payments->links("pagination::bootstrap-4") }} --}}
        </nav>
      </div>

      <div class="tab-pane fade" id="transfers" role="tabpanel" aria-labelledby="transfers-tab">
        <form id="search-transfers" class="form-inline my-2 my-lg-0 pb-0 justify-content-md-center">
          <select class="custom-select mb-2">
            <option selected>Filter</option>
            <option value="date">Date</option>
            <option value="amount">Amount</option>
          </select>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text search-addon"><i class="fas fa-search"></i></span>
            </div>
            <input class="form-control" id="search-product" type="text" placeholder="Search..." />
          </div>
        </form>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="thead-light">
              <tr>
                <th scope="col"><a href="#">Date <i class="fas fa-sort"></i></a></th>
                <th scope="col"><a href="#">Product <i class="fas fa-sort"></i></a></th>
                <th scope="col"><a href="#">Type <i class="fas fa-sort"></i></a></th>
                <th scope="col"><a href="#">Amount <i class="fas fa-sort"></i></a></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th scope="row">06/03/2019</th>
                <td><a href="../product.html">#1544</a></td>
                <td>Sale</td>
                <td>1</td>
              </tr>
              <tr>
                <th scope="row">06/03/2019</th>
                <td><a href="../product.html">#4564</a></td>
                <td>Purchase</td>
                <td>100</td>
              </tr>
              <tr>
                <th scope="row">06/03/2019</th>
                <td><a href="../product.html">#8544</a></td>
                <td>Purchase</td>
                <td>50</td>
              </tr>
            </tbody>
          </table>
        </div>
        <nav aria-label="table navigation">
          <ul class="pagination justify-content-center">
            <li class="page-item disabled">
              <a class="page-link" href="#" tabindex="-1" aria-disabled="true" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
              </a>
            </li>
            <li class="page-item active" aria-current="page">
              <a class="page-link" href="#">1</a>
              <span class="sr-only">(current)</span>
            </li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
              <a class="page-link" href="#" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>

  {{-- <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="paymentLabel">Payment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <a href="#">Payment proof</a>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
          <button type="button" class="btn btn-success">Confirm payment <i class="fas fa-check-circle"></i></button>
        </div>
      </div>
    </div>
  </div> --}}
@endsection