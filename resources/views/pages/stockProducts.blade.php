<div class="input-group mb-2">
    <div class="input-group-prepend">
      <span class="input-group-text" id="search-addon"><i class="fas fa-search"></i></span>
    </div>
    <input class="form-control" id="search-product" type="text" placeholder="Search..." />
  </div>
  <div class="table-responsive">
    <table class="table table-striped table-hover products">
      <thead class="thead-light">
        <tr>
          <th scope="col">
            <a href="#">Ref. No. <i class="fas fa-sort"></i></a>
          </th>
          <th scope="col">
            <a href="#">Stock <i class="fas fa-sort"></i></a>
          </th>
          <th scope="col">
            <a href="#">Price (€) <i class="fas fa-sort"></i></a>
          </th>
          <th scope="col">
            <a href="#">Status <i class="fas fa-sort"></i></a>
          </th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody id="productsTable">
          @each('partials.productInfo', $products, 'product')
      </tbody>
    </table>
  </div>

  <div class="row">
    <div class="col">
      <!-- blank column to center pagination var -->
    </div>
    <div class="col">
      <nav aria-label="table navigation">
          {{ $products->links("pagination::bootstrap-4") }}
      </nav>
    </div>
    <div class="col">
      <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addProduct">
        <i class="fas fa-plus-circle"></i>
        <span class="button-text">Add product</span>
      </button>
    </div>
  </div>