<div class="table-responsive">
  <table class="table table-striped table-hover">
    <thead class="thead-light">
      <tr>
        <th scope="col">
          <a href="#">Product <i class="fas fa-sort"></i></a>
        </th>
        <th scope="col">
          <a href="#">OP <i class="fas fa-sort"></i></a>
        </th>
        <th scope="col">
          <a href="#">Rating <i class="fas fa-sort"></i></a>
        </th>
      </tr>
    </thead>
    <tbody id="reviewsTable">
      @each('partials.reviewInfo', $reviews, 'review')
    </tbody>
  </table>
</div>
<nav aria-label="table navigation">
  {{ $reviews->links("pagination::bootstrap-4") }}
</nav>