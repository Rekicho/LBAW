<div class="input-group mb-2">
  <div class="input-group-prepend">
    <span class="input-group-text" id="search-addon"><i class="fas fa-search"></i></span>
  </div>
  <input class="form-control" id="search-user" type="text" placeholder="Search..." />
</div>
<div class="table-responsive">
  <table class="table table-striped table-hover">
    <thead class="thead-light">
      <tr>
        <th scope="col">
          <a href="#">Username <i class="fas fa-sort"></i></a>
        </th>
        <th scope="col">
          <a href="#">Status <i class="fas fa-sort"></i></a>
        </th>
        <th scope="col">Actions</th>
      </tr>
    </thead>
    <tbody id="usersTable">
      @each('partials.user', $clients, 'user')
    </tbody>
  </table>  
</div>
<nav aria-label="table navigation">
  {{ $clients->links("pagination::bootstrap-4") }}
</nav>
