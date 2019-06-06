@extends('layouts.backoffice')

@section('script')
    <script src="{{asset('js/admin.js')}}"></script>
@endsection

@section('title', 'Administration')

@section('content')

<ul class="nav nav-tabs mb-3" id="tasks" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="staff-tab" data-toggle="tab" href="#staff" role="tab"
        aria-selected="true">Staff Members</a>
    </li>
  </ul>

  <div class="tab-content" id="tasksContent">
    <div class="container">
      <div class="tab-pane fade show active" id="staff" role="tabpanel" aria-labelledby="staff-tab">
        <div class="input-group mb-2">
          <div class="input-group-prepend">
            <span class="input-group-text" id="search-addon"><i class="fas fa-search"></i></span>
          </div>
          <input class="form-control" id="search-member" type="text" placeholder="Search..." />
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="thead-light">
              <tr>
                <th scope="col">
                  <a href="#" class="text-decoration-none">Member <i class="fas fa-sort"></i></a>
                </th>
                <th scope="col">
                  <a href="#" class="text-decoration-none">Status <i class="fas fa-sort"></i></a>
                </th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody id="staffMemberTable">
            @each('partials.user', $staff_members, 'user')
          </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col">
            <!-- blank column to center pagination var -->
          </div>
          <div class="col">
            {{ $staff_members->links("pagination::bootstrap-4") }}
          </div>
          <div class="col">
            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addMember">
              <i class="fas fa-plus-circle"></i>
              <span class="button-text">Add member</span>
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Modals -->
    <div class="modal fade confirmDisable" id="confirmDisable" tabindex="-1" role="dialog" aria-labelledby="disableModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="disableModalLabel">Disable Member</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Are you sure?
            <form id="confirmDisableForm">
                <input type="hidden" name="id" class="id">
                <input type="hidden" name="is_enabled" value="false">
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Close
            </button>
            <button class="btn btn-secondary confirm" type="submit" form="confirmDisableForm">Confirm</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade confirmEnable" id="confirmEnable" tabindex="-1" role="dialog" aria-labelledby="enableModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="enableModalLabel">Enable Member</h5>
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
            <button class="btn btn-secondary confirm" type="submit" form="confirmEnableForm">Confirm</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="addMember" tabindex="-1" role="dialog" aria-labelledby="addMemberModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addMemberModalLabel">Add Member</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="POST" id="addStaffMemberForm">
              <div class="form-row">
                <div class="form-group required col-md-6">
                  <label class="control-label">
                    Username
                    <input class="form-control" type="text" name="username" placeholder="Username" required />
                  </label>
                </div>
                <div class="form-group required col-md-6">
                  <label class="control-label">
                    Password
                    <input class="form-control" type="password" name="password" placeholder="Password" required />
                  </label>
                </div>
              </div>
            </form>
            <span class='message'></span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Close
            </button>
            <button type="submit" form="addStaffMemberForm" class="btn btn-med btn-primary">
              Add
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="staffActionsModal" tabindex="-1" role="dialog" aria-labelledby="staffActionsModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staffActionsModalLabel">Update status</h5>
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
