<div class="input-group mb-2">
    <div class="input-group-prepend">
      <span class="input-group-text" id="search-addon"><i class="fas fa-search"></i></span>
    </div>
    <input class="form-control" id="search-product" type="text" placeholder="Search..." />
  </div>
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead class="thead-light">
        <tr>
          <th scope="col">
            <a href="#">Name <i class="fas fa-sort"></i></a>
          </th>
          <th scope="col">
            <a href="#">Products <i class="fas fa-sort"></i></a>
          </th>
          <th scope="col">
            <a href="#">Actions <i class="fas fa-sort"></i></a>
          </th>
        </tr>
      </thead>
      <tbody id="categoriesTable">
          @foreach($categories as $category)
              <tr>
                  <th scope="row"><a href="/category/{{$category->id}}">{{$category->name}}</a></th>
                  <td>{{$category->num_products}}</td>
                  <td>
                    <button type="button" class="btn btn-primary btn-sm updateCategory" data-id={{$category->id}} data-toggle="modal" data-target="#addDiscountModal">
                      <i class="fas fa-tags"></i>
                      <span class="button-text">Add discount</span>
                    </button>
                  </td>
              </tr>
          @endforeach
      </tbody>
    </table>
  </div>

  <div class="row">
    <div class="col">
      <!-- blank column to center pagination var -->
    </div>
    <div class="col">
      <nav aria-label="table navigation">
          {{ $categories->links("pagination::bootstrap-4") }}
      </nav>
    </div>
    <div class="col">
      <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addCategory">
        <i class="fas fa-plus-circle"></i>
        <span class="button-text">Add category</span>
      </button>
    </div>
  </div>