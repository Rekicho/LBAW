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
        @each('partials.payment', $payments, 'payment')
      </tbody>
    </table>
  </div>
  <nav aria-label="table navigation">
        {{ $payments->links("pagination::bootstrap-4") }}
  </nav>