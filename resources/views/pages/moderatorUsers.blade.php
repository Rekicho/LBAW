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
