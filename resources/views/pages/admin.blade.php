@extends('layouts.backoffice')

@section('content')

<ul class="nav nav-tabs mb-3" id="tasks" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="staff-tab" data-toggle="tab" href="#staff" role="tab" aria-controls="products"
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
            @each('partials.staffmember', $staff_members, 'staff_member')
          </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col">
            <!-- blank column to center pagination var -->
          </div>
          <div class="col">
            <nav aria-label="table navigation">
              <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                  <a class="page-link" href="#" tab-index="-1" aria-disabled="true">
                    <!-- or aria-label="Previous" if not disabled (check https://getbootstrap.com/docs/4.3/components/pagination/#disabled-and-active-states)-->
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
    <div class="modal fade" id="confirmDisable" tabindex="-1" role="dialog" aria-labelledby="disableModalLabel"
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
    <div class="modal fade" id="confirmEnable" tabindex="-1" role="dialog" aria-labelledby="enableModalLabel"
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
            <form>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>
                    Username
                    <input class="form-control" type="text" name="username" placeholder="Username" required />
                  </label>
                </div>
                <div class="form-group col-md-6">
                  <label>
                    Password
                    <input class="form-control" type="text" name="password" placeholder="Password" required />
                  </label>
                </div>
              </div>

              <button type="submit" class="btn btn-med btn-primary">
                <i class="fas fa-plus-circle"></i> Add
              </button>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Close
            </button>
            <button type="button" class="btn btn-primary">Save changes</button>
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
