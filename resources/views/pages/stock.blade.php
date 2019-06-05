@extends('layouts.backoffice')

@section('script')
    <script src="{{'assets/stock.js'}}"></script>
@endsection

@section('content')
<ul class="nav nav-tabs mb-3" id="tasks" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="products-tab" data-toggle="tab" href="#products" role="tab"
        aria-controls="products" aria-selected="true">Products</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="categories-tab" data-toggle="tab" href="#categories" role="tab" aria-controls="categories"
        aria-selected="false">Categories</a>
    </li>
  </ul>

  <div class="container">
    <div class="tab-content" id="tasksContent">
      <div class="tab-pane fade show active" id="products" role="tabpanel" aria-labelledby="products-tab">
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
      </div>

      <div class="tab-pane fade" id="categories" role="tabpanel" aria-labelledby="categories-tab">
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
              </tr>
            </thead>
            <tbody id="categoriesTable">
                @foreach($categories as $category)
                    <tr>
                        <th scope="row"><a href="/category/{{$category->id}}">{{$category->name}}</a></th>
                        <td>{{$category->num_products}}</td>
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
                {{ $products->links("pagination::bootstrap-4") }}
            </nav>
          </div>
          <div class="col">
            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addCategory">
              <i class="fas fa-plus-circle"></i>
              <span class="button-text">Add category</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modals -->
  <div class="modal fade" id="confirmDisable" tabindex="-1" role="dialog" aria-labelledby="confirmDisableLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDisableLabel">Disable Product</h5>
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

  <div class="modal fade" id="confirmEnable" tabindex="-1" role="dialog" aria-labelledby="confirmEnableLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmEnableLabel">Enable Product</h5>
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

  <div class="modal fade" id="updateStockModal" tabindex="-1" role="dialog" aria-labelledby="updateStockLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateStockLabel">Update Stock</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <label>
                Stock
                <input class="form-control" type="number" name="stock" placeholder="Stock" />
              </label>
            </div>
          </form>
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

  <div class="modal fade" id="updatePriceModal" tabindex="-1" role="dialog" aria-labelledby="updatePriceLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updatePriceLabel">Update Price</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <label>
                New price
                <input class="form-control" type="Number" name="price" placeholder="Price" />
              </label>
            </div>
          </form>
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

  <div class="modal fade" id="addProduct" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addProductModalLabel">Add product</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Select category
                  <select class="form-control" id="sel-category" name="category" required>
                    <option hidden disabled selected value>-</option>
                    @foreach ($categories as $category)
                    <option value="{{$category->name}}">{{$category->name}}</option>
                    @endforeach
                  </select>
                </label>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>
                  Name
                  <input class="form-control" type="text" name="name" placeholder="Name" required />
                </label>
              </div>
              <div class="form-group col-md-6">
                <label>
                  Reference
                  <input class="form-control" type="text" name="reference" placeholder="Reference" required />
                </label>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>
                  Stock
                  <input class="form-control" type="number" name="stock" placeholder="Stock" required />
                </label>
              </div>
              <div class="form-group col-md-6">
                <label>
                  Price
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">€</div>
                    </div>
                    <input class="form-control" type="number" name="price" placeholder="Price" required />
                  </div>
                </label>
              </div>
            </div>

            <img id="product-image" src="images.jpeg" class="img-fluid rounded mx-auto d-block" alt="product image" />

            <div class="custom-file mb-4">
              <input type="file" class="custom-file-input" id="productImage" required />
              <label class="custom-file-label" for="productImage">Choose file</label>
            </div>

            <button type="submit" class="btn btn-med btn-primary">
              <i class="fas fa-plus-circle"></i>
              Add
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


  <div class="modal fade" id="actionsModal" tabindex="-1" role="dialog" aria-labelledby="actionsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="actionsModalLabel">Update</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <ul class="nav nav-pills nav-justified mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="pills-stock-tab" data-toggle="pill" href="#pills-stock" role="tab"
                aria-controls="pills-stock" aria-selected="true">Stock</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="pills-price-tab" data-toggle="pill" href="#pills-price" role="tab"
                aria-controls="pills-price" aria-selected="false">Price</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="pills-status-tab" data-toggle="pill" href="#pills-status" role="tab"
                aria-controls="pills-status" aria-selected="false">Status</a>
            </li>
          </ul>
          <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-stock" role="tabpanel" aria-labelledby="pills-stock-tab">
              <form>
                <div class="form-group">
                  <label>
                    Stock
                    <input class="form-control" type="number" name="stock" placeholder="Stock" />
                  </label>
                </div>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-wrench"></i>
                  Update stock
                </button>
              </form>
            </div>
            <div class="tab-pane fade" id="pills-price" role="tabpanel" aria-labelledby="pills-price-tab">
              <form>
                <div class="form-group">
                  <label>
                    New price
                    <input class="form-control" type="Number" name="price" placeholder="Price" />
                  </label>
                </div>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-euro-sign"></i>
                  Update price
                </button>
              </form>
            </div>
            <div class="tab-pane fade" id="pills-status" role="tabpanel" aria-labelledby="pills-status-tab">
              <p>Are you sure?</p>
              <button type="button" class="btn btn-danger">
                <i class="fas fa-minus-circle"></i>
                Disable
              </button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCategoryModalLabel">Add category</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <label>
                Name
                <input class="form-control" type="text" name="name" placeholder="Name" required />
              </label>
            </div>

            <button type="submit" class="btn btn-med btn-primary">
              <i class="fas fa-plus-circle"></i>
              Add
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
@endsection