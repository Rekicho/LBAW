@extends('layouts.backoffice')

@section('script')
    <script src="{{asset('js/moderator.js')}}"></script>
@endsection

@section('title', 'Moderating')

@section('content')
<div class="alert alert-primary alert-dismissible fade show" role="alert">
    You have a new report
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>

  <ul class="nav nav-tabs mb-3" id="tasks" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="users-tab" data-toggle="tab" href="#users" role="tab" aria-controls="users"
        aria-selected="true">Users</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="reports-tab" data-toggle="tab" href="#reports" role="tab" aria-controls="reports"
        aria-selected="false">Reports</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab" aria-controls="reviews"
        aria-selected="false">Reviews</a>
    </li>
  </ul>

  <div class="container">
    <div class="tab-content" id="tasksContent">
      <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
        <!-- Filled with ajax call -->
      </div>

      <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
        <!-- Filled with ajax call -->
      </div>
      {{-- TODO: varias paginations, mesmo link --}}
      <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
        <!-- Filled with ajax call -->
      </div>
    </div>
  </div>

  <!-- Modals -->
  <div class="modal fade confirmDisableUser" id="confirmDisable" tabindex="-1" role="dialog" aria-labelledby="confirmDisableLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDisableLabel">Ban user</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="confirmDisableForm">
              <div class="form-group">
                  <label for="reason">Reason </label>
                  <textarea id="reason" rows="3" cols="30" name="reason"></textarea>
              </div>
              <div class="form-group">
                  <label for="end_t">End date </label>
                  <input type="date" id="end_t" name="end_t">
              </div>
              <input type="hidden" name="id">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
          <button type="submit" form="confirmDisableForm" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade confirmEnableUser" id="confirmEnable" tabindex="-1" role="dialog" aria-labelledby="confirmEnableLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmEnableLabel">Unban user</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Are you sure?
          <form id="confirmEnableForm">
              <input type="hidden" name="id">
              <input type="hidden" name="is_enabled" value="true">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
          <button type="submit" form="confirmEnableForm" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="disableReview" tabindex="-1" role="dialog" aria-labelledby="disableReviewLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="disableReviewLabel">Disable review</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Are you sure?
          <form id="disableReviewForm">
            <input type="hidden" name="id_review">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
          <button type="button" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="enableReview" tabindex="-1" role="dialog" aria-labelledby="enableReviewLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="enableReviewLabel">Enable review</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Are you sure?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
          <button type="button" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="userActionsModal" tabindex="-1" role="dialog" aria-labelledby="userActionsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="userActionsModalLabel">Update status</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          Are you sure?
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
          <button type="button" class="btn btn-danger"><i class="fas fa-minus-circle"></i> Ban</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="reportActionsModal" tabindex="-1" role="dialog" aria-labelledby="reportActionsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="reportActionsModalLabel">Update status</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          Are you sure?
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
          <button type="button" class="btn btn-danger"><i class="fas fa-minus-circle"></i> Disable</button>
        </div>
      </div>
    </div>
  </div>
@endsection