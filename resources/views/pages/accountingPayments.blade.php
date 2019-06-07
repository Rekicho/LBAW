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