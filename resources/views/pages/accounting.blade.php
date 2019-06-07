@extends('layouts.backoffice')

@section('css')
<link href="{{ asset('css/accounting.css') }}" rel="stylesheet">
@endsection

@section('title', 'Accounting')

@section('script')
    <script src="{{asset('js/accounting.js')}}"></script>
@endsection

@section('content')

  <ul class="nav nav-tabs mb-3" id="tasks" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="payments-tab" data-toggle="tab" href="#payments" role="tab"
        aria-controls="payments" aria-selected="true">Payments</a>
    </li>
  </ul>

  <div class="container">
    <div class="tab-content" id="tasksContent">
      <div class="tab-pane fade show active" id="payments" role="tabpanel" aria-labelledby="payments-tab">
        <!-- Filled with ajax -->
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