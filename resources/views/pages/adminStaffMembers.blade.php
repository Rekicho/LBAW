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