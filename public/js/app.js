$(document).on("click", ".updateMember", function() {
  var staffMemberId = $(this).data("id");
  $(".modal-body [name=id]").val(staffMemberId);
});

$(document).on("click", ".updateProduct", function() {
  var productId = $(this).data("id");
  $(".modal-body [name=id]").val(productId);
  $("#addProductDiscountForm [name=id]").val(productId);
});

$(document).on("click", ".updateCategory", function() {
  var categoryId = $(this).data("id");
  $("#addDiscountForm [name=id]").val(categoryId);
});

function addEventListeners() {
  let wishlistDeleters = document.querySelectorAll(
    "div.single-product-info-text a.delete"
  );
  [].forEach.call(wishlistDeleters, function(deleter) {
    deleter.addEventListener("click", sendDeleteWishListRequest);
  });

  let addStaffMember = document.querySelector("#addMember form");
  if (addStaffMember != null)
    addStaffMember.addEventListener("submit", sendCreateStaffMemberRequest);

  let disableStaffMember = document.querySelector("#confirmDisable form");
  if (disableStaffMember != null)
    disableStaffMember.addEventListener("submit", sendUpdateStaffMemberRequest);

  let enableStaffMember = document.querySelector("#confirmEnable form");
  if (enableStaffMember != null)
    enableStaffMember.addEventListener("submit", sendUpdateStaffMemberRequest);

  let updateBillingInformation = document.querySelector(
    "form[class*=billingInfo]"
  );
  if (updateBillingInformation != null) {
    updateBillingInformation.addEventListener(
      "submit",
      sendUpdateBillingInformationRequest
    );
  }

  let updateWishlist = document.querySelector("form#updateWishlist");
  if (updateWishlist != null) {
    updateWishlist.addEventListener("submit", sendUpdateWishlistRequest);
  }

  let updatePassword = document.querySelector("form#updatePassword");
  if (updatePassword != null)
    updatePassword.addEventListener("submit", sendUpdatePasswordRequest);

  let updateEmail = document.querySelector("form#updateEmail");
  if (updateEmail != null)
    updateEmail.addEventListener("submit", sendUpdateEmailRequest);

  let addProduct = document.querySelector("#addProduct form");
  if (addProduct != null)
    addProduct.addEventListener("submit", sendAddProductRequest);

  let updatePrice = document.querySelector("form#updatePriceForm");
  if (updatePrice != null)
    updatePrice.addEventListener("submit", sendUpdatePriceRequest);

  let updateStock = document.querySelector("form#updateStockForm");
  if (updateStock != null)
    updateStock.addEventListener("submit", sendUpdateStockRequest);

  let disableProduct = document.querySelector("form#confirmDisableForm");
  if (disableProduct != null)
    disableProduct.addEventListener("submit", sendUpdateProductRequest);

  let enableProduct = document.querySelector("form#confirmEnableForm");
  if (enableProduct != null)
    enableProduct.addEventListener("submit", sendUpdateProductRequest);

  let addCategory = document.querySelector("#addCategory form");
  if (addCategory != null)
    addCategory.addEventListener("submit", sendAddCategoryRequest);

  let addCategoryDiscount = document.querySelector("form#addDiscountForm");
  if (addCategoryDiscount != null)
    addCategoryDiscount.addEventListener("submit", sendAddCategoryDiscountRequest);

    let addProductDiscount = document.querySelector("form#addProductDiscountForm");
  if (addProductDiscount != null)
    addProductDiscount.addEventListener("submit", sendAddProductDiscountRequest);
}

function encodeForAjax(data) {
  if (data == null) return null;
  return Object.keys(data)
    .map(function(k) {
      return encodeURIComponent(k) + "=" + encodeURIComponent(data[k]);
    })
    .join("&");
}

function sendAjaxRequest(method, url, data, handler) {
  let request = new XMLHttpRequest();

  request.open(method, url, true);
  request.setRequestHeader(
    "X-CSRF-TOKEN",
    document.querySelector('meta[name="csrf-token"]').content
  );
  request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  request.addEventListener("load", handler);
  request.send(encodeForAjax(data));
}

function sendAddProductRequest(event) {
  event.preventDefault();

  let formData = new FormData(this);
  console.log(this);
  let data = {};
  for (var [key, value] of formData.entries()) {
    data[key] = value;
    console.log(key, value);
  }

  sendAjaxRequest("put", "/api/products/", data, productAddedHandler);
}

function sendAddCategoryRequest(event) {
  event.preventDefault();

  let name = this.querySelector("input[name=name]").value;

  sendAjaxRequest(
    "put",
    "/api/categories/",
    { name: name },
    categoryAddedHandler
  );
}

function sendUpdatePriceRequest(event) {
  event.preventDefault();

  let id = this.querySelector("input[name=id]").value;
  console.log(id);
  let price = this.querySelector("input[name=price]").value;

  sendAjaxRequest(
    "post",
    "/api/products/" + id,
    { type: "price", price: price },
    priceUpdatedHandler
  );
}

function sendUpdateStockRequest(event) {
  event.preventDefault();

  let id = this.querySelector("input[name=id]").value;
  console.log(id);
  let stock = this.querySelector("input[name=stock]").value;

  sendAjaxRequest(
    "post",
    "/api/products/" + id,
    { type: "stock", stock: stock },
    stockUpdatedHandler
  );
}

function productAddedHandler() {
  let product = JSON.parse(this.responseText);

  console.log(product);
}

function categoryAddedHandler() {
  let category = JSON.parse(this.responseText);

  console.log(category);
}

function sendDeleteWishListRequest() {
  let id = this.closest("li.single-product-info-container").getAttribute(
    "data-id"
  );

  sendAjaxRequest(
    "delete",
    "/api/wishlist/" + id,
    null,
    wishListDeletedHandler
  );
}

function wishListDeletedHandler() {
  let product = JSON.parse(this.responseText);
  let element = document.querySelector(
    'li.single-product-info-container[data-id="' + product.id + '"]'
  );
  element.remove();
}

function sendUpdateEmailRequest(event) {
  event.preventDefault();

  let email = this.querySelector("input[name=email]").value;
  let user_id = this.querySelector("input[name=user_id]").value;

  if (email != "")
    sendAjaxRequest(
      "post",
      "/api/users/" + user_id,
      {
        type: "updateEmail",
        email: email
      },
      updatedEmailHandler
    );
}

function sendUpdatePasswordRequest(event) {
  event.preventDefault();

  let old_password = this.querySelector("input[name=old_password]").value;
  let password = this.querySelector("input[name=new_password]").value;
  let password_confirmation = this.querySelector(
    "input[name=new_password_confirmation]"
  ).value;
  let user_id = this.querySelector("input[name=user_id]").value;

  if (old_password != "" && password != "" && password_confirmation != "")
    sendAjaxRequest(
      "post",
      "/api/users/" + user_id,
      {
        type: "updatePassword",
        old_password: old_password,
        password: password,
        password_confirmation: password_confirmation
      },
      updatedPasswordHandler
    );
}

function sendUpdateWishlistRequest(event) {
  let id_product = this.querySelector("input[name=id_product]").value;
  let id = this.querySelector("input[name=id]");

  if (id === null) {
    sendAjaxRequest(
      "post",
      "/api/wishlist/",
      { id_product: id_product },
      addedToWishlistHandler
    );
  } else {
    sendAjaxRequest(
      "delete",
      "/api/wishlist/" + id.value,
      null,
      removedFromWishListHandler
    );
  }

  event.preventDefault();
}

function removedFromWishListHandler() {
  console.log(this.status);

  let wishlist = JSON.parse(this.responseText);

  let oldForm = document.querySelector("form#updateWishlist");
  let newForm = getAddToWishListForm(wishlist);
  oldForm.parentNode.replaceChild(newForm, oldForm);
}

function sendCreateStaffMemberRequest(event) {
  let username = this.querySelector("input[name=username]").value;
  let password = this.querySelector("input[name=password]").value;

  if (username != "" && password != "")
    sendAjaxRequest(
      "post",
      "/api/users/",
      { username: username, password: password },
      staffMemberAddedHandler
    );

  event.preventDefault();
}

function sendUpdateStaffMemberRequest(event) {
  let id = this.querySelector("input[name=id]").value;
  let is_enabled = this.querySelector("input[name=is_enabled]").value;

  sendAjaxRequest(
    "post",
    "/api/users/" + id,
    { type: "updateMember", is_enabled: is_enabled },
    staffMemberUpdatedHandler
  );

  event.preventDefault();
}

function sendUpdateProductRequest(event) {
  let id = this.querySelector("input[name=id]").value;
  let is_enabled = this.querySelector("input[name=is_enabled]").value;

  sendAjaxRequest(
    "post",
    "/api/products/" + id,
    { type: "updateProduct", is_enabled: is_enabled },
    productUpdatedHandler
  );

  event.preventDefault();
}

function sendAddCategoryDiscountRequest(event) {
  event.preventDefault();                                                                               

  let id = this.querySelector("input[name=id]").value;
  let start = this.querySelector("input[name=start]").value;
  let end = this.querySelector("input[name=end]").value;
  let value = this.querySelector("input[name=value]").value;

  sendAjaxRequest(
    "put",
    "/api/discounts/",
    { id_category: id, start: start, end: end, value: value },
    categoryDiscountAddedHandler
  );

}    

function sendAddProductDiscountRequest(event) {
  event.preventDefault();                                                                               

  let id = this.querySelector("input[name=id]").value;
  // let start = this.querySelector("input[name=start]").value;
  // let end = this.querySelector("input[name=end]").value;
  let value = this.querySelector("input[name=value]").value / 100;

  sendAjaxRequest(
    "post",
    "/api/products/" + id,
    { type: "discount", discount: value },
    productUpdatedHandler
  );

}      

function categoryDiscountAddedHandler(){
  let discount = JSON.parse(this.responseText);
}

function sendUpdateBillingInformationRequest(event) {
  event.preventDefault();

  let id = this.querySelector("input[name=id]");
  let full_name = this.querySelector("input[name=full_name]").value;
  let city = this.querySelector("input[name=city]").value;
  let address = this.querySelector("input[name=address]").value;
  let state = this.querySelector("input[name=state]").value;
  let zip_code = this.querySelector("input[name=zip_code]").value;

  if (id === null) {
    sendAjaxRequest(
      "post",
      "/api/billingInfo/",
      {
        full_name: full_name,
        city: city,
        address: address,
        state: state,
        zip_code: zip_code
      },
      billingInformationUpdatedHandler
    );
  } else {
    sendAjaxRequest(
      "post",
      "/api/billingInfo/" + id.value,
      {
        full_name: full_name,
        city: city,
        address: address,
        state: state,
        zip_code: zip_code
      },
      billingInformationUpdatedHandler
    );
  }
}

function updatedPasswordHandler() {
  let response = JSON.parse(this.responseText);
  console.log(response);
  let form = document.querySelector("form#updatePassword");
  let span = form.querySelector("span.message");

  if (response["errors"] != null) {
    let newError = document.createElement("span");
    newError.classList.add("message");
    newError.classList.add("error");
    newError.classList.remove("success");

    if (span == null) {
      newError.innerHTML = response["errors"][0];

      form.appendChild(newError);
    } else {
      span.classList.add("error");
      span.classList.remove("success");
      span.innerHTML = response["errors"][0];
    }
  } else if (span != null) {
    span.classList.remove("error");
    span.classList.add("success");
    span.innerHTML = "Successfuly changed password.";
  }
}

function staffMemberAddedHandler() {
  console.log(this.status);
  let response = JSON.parse(this.responseText);

  let message = document.querySelector("#addMember .modal-body .message");

  if (response["errors"] != null) {
    message.classList.add("error");
    message.classList.remove("success");
    message.innerHTML = response["errors"][0];
    return;
  }
  message.classList.add("success");
  message.classList.remove("error");
  let feedbackMsg = "Staff member added with success";
  message.innerHTML = feedbackMsg;

  let newRow = createStaffMemberRow(response);
  let table = document.getElementById("staffMemberTable");
  table.appendChild(newRow);
}

function updatedEmailHandler() {
  let response = JSON.parse(this.responseText);
  console.log(response);

  let form = document.querySelector("form#updateEmail");
  let span = form.querySelector("span.message");

  if (response["errors"] != null) {
    let newError = document.createElement("span");
    newError.classList.add("message");
    newError.classList.add("error");
    newError.classList.remove("success");
    if (span == null) {
      newError.innerHTML = response["errors"][0];

      form.appendChild(newError);
    } else {
      span.innerHTML = response["errors"][0];
    }
  } else if (span != null) {
    newError.classList.remove("error");
    newError.classList.add("success");
    span.innerHTML = "Successfuly changed email.";
  }
}

function addedToWishlistHandler() {
  console.log(this.status);

  let wishlist = JSON.parse(this.responseText);

  let oldForm = document.querySelector("form#updateWishlist");
  let newForm = getRemoveFromWishListForm(wishlist);
  oldForm.parentNode.replaceChild(newForm, oldForm);
}

function getRemoveFromWishListForm(wishlist) {
  let form = document.createElement("form");
  form.setAttribute("id", "updateWishlist");

  form.innerHTML = `
  <br style="clear:both">
  <input type="hidden" class="d-none    " name="id_product" value=${
    wishlist.id_product
  }>
  <input type="hidden" class="d-none    " name="id" value=${wishlist.id}>
  <button type="submit" class="btn btn-primary float-right">
      Remove from wishlist <i class="fas fa-bookmark"></i>
  </button>`;
  form.addEventListener("submit", sendUpdateWishlistRequest);

  return form;
}

function getAddToWishListForm(wishlist) {
  let form = document.createElement("form");
  form.setAttribute("id", "updateWishlist");

  form.innerHTML = `
  <br style="clear:both">
  <input type="hidden" class="d-none    " name="id_product" value=${
    wishlist.id_product
  }>
  <button type="submit" class="btn btn-primary float-right">
      Add to wishlist <i class="fas fa-bookmark"></i>
  </button>`;

  form.addEventListener("submit", sendUpdateWishlistRequest);

  return form;
}

function stockUpdatedHandler() {}

function priceUpdatedHandler() {}

function staffMemberUpdatedHandler() {
  console.log(this.status);

  let staff_member = JSON.parse(this.responseText);
  let row = document.querySelector("[data-id='" + staff_member.id + "']");
  let newRow = createStaffMemberRow(staff_member);
  row.parentNode.replaceChild(newRow, row);

  $("#confirmEnable").modal("hide");
  $("#confirmDisable").modal("hide");
}

function productUpdatedHandler() {
  console.log(this.status);

  let product = JSON.parse(this.responseText);
  let row = document.querySelector("[data-id='" + product.id + "']");
  let newRow = createProductRow(product);
  row.parentNode.replaceChild(newRow, row);

  $("#confirmEnable").modal("hide");
  $("#confirmDisable").modal("hide");
  $("#addProductDiscountModal").modal("hide");

}

function billingInformationUpdatedHandler() {
  console.log(this.status);
  let billingInfo = JSON.parse(this.responseText);
  let newForm = createBillingInfoForm(billingInfo);
  let form = document.querySelector("form[data-id='" + billingInfo.id + "']");

  if (form === null) form = document.querySelector("form[class*=billingInfo]");
  form.innerHTML = newForm;
}

function createBillingInfoForm(billingInfo) {
  console.log(billingInfo.id);
  return `
  <div class="my-3">
  <h3>Shipping & Billing Information</h3>
</div>

  <div class="form-group">
  <label for="fullName">Full Name</label>
  <input type="text" name="full_name" id="fullName" class="form-control" placeholder="Full Name" value="${
    billingInfo.full_name
  }" />
</div>
<div class="form-group">
  <label for="address">Address</label>
  <input type="text" name="address" id="address" class="form-control" placeholder="Address" value="${
    billingInfo.address
  }" />
</div>
<div class="form-group">
  <label for="city">City</label>
  <input type="text" name="city" id="city" class="form-control" placeholder="City" value="${
    billingInfo.city
  }" />
</div>
<div class="form-group">
  <label for="state">State</label>
  <input type="text" name="state" id="state" class="form-control" placeholder="State" value="${
    billingInfo.state
  }" />
</div>
<div class="form-group">
  <label for="zip">Zip Code</label>
  <input type="text" name="zip_code" id="zip" class="form-control" placeholder="zip" value="${
    billingInfo.zip_code
  }" />
</div>
<input type="hidden" name="id" value=${billingInfo.id}>
<button class="btn btn-lg btn-primary my-2 float-right" type="submit">
Edit
</button>`;
}

function createStaffMemberRow(staff_member) {
  let new_staff_member = document.createElement("tr");
  new_staff_member.setAttribute("data-id", staff_member.id);

  let is_enabled = staff_member.is_enabled ? "Enabled" : "Disabled";

  let button = document.createElement("button");
  button.setAttribute("type", "button");
  button.classList = "btn btn-sm updateMember ";
  button.classList += staff_member.is_enabled ? "btn-danger" : "btn-success";
  button.setAttribute("data-id", staff_member.id);
  button.setAttribute("data-toggle", "modal");

  if (staff_member.is_enabled)
    button.setAttribute("data-target", "#confirmDisable");
  else button.setAttribute("data-target", "#confirmEnable");

  let icon = document.createElement("i");
  icon.classList = staff_member.is_enabled
    ? "fas fa-minus-circle"
    : "fas fa-plus-circle";

  let span = document.createElement("span");
  span.classList = "button-text";
  span.innerHTML = staff_member.is_enabled ? " Disable" : " Enable";

  button.appendChild(icon);
  button.appendChild(span);
  let header = document.createElement("th");
  header.setAttribute("scope", "row");
  header.innerHTML = staff_member.username;

  let enabled = document.createElement("td");
  enabled.innerHTML = is_enabled;

  let newCell = document.createElement("td");
  newCell.appendChild(button);

  new_staff_member.appendChild(header);
  new_staff_member.appendChild(enabled);
  new_staff_member.appendChild(newCell);

  return new_staff_member;
}

function createProductRow(product) {
  let new_product = document.createElement("tr");
  new_product.setAttribute("data-id", product.id);

  let is_enabled = product.is_enabled ? "Enabled" : "Disabled";

  let button = document.createElement("button");
  button.setAttribute("type", "button");
  button.classList = "btn btn-sm updateProduct ";
  button.classList += product.is_enabled ? "btn-danger" : "btn-success";
  button.setAttribute("data-id", product.id);
  button.setAttribute("data-toggle", "modal");

  if (product.is_enabled) button.setAttribute("data-target", "#confirmDisable");
  else button.setAttribute("data-target", "#confirmEnable");

  let icon = document.createElement("i");
  icon.classList = product.is_enabled
    ? "fas fa-minus-circle"
    : "fas fa-plus-circle";

  let span = document.createElement("span");
  span.classList = "button-text";
  span.innerHTML = product.is_enabled ? " Disable" : " Enable";

  button.appendChild(icon);
  button.appendChild(span);

  let stockButton = document.createElement("button");
  stockButton.setAttribute("type", "submit");
  stockButton.classList = "btn btn-primary btn-sm updateProduct ";
  stockButton.setAttribute("data-id", product.id);
  stockButton.setAttribute("data-toggle", "modal");
  stockButton.setAttribute("data-target", "#updateStockModal");

  let stockIcon = document.createElement("i");
  stockIcon.classList = "fas fa-wrench";

  let stockSpan = document.createElement("span");
  stockSpan.classList = "button-text";
  stockSpan.innerHTML = "Update stock";

  stockButton.appendChild(stockIcon);
  stockButton.appendChild(stockSpan);

  let priceButton = document.createElement("button");
  priceButton.setAttribute("type", "submit");
  priceButton.classList = "btn btn-primary btn-sm updateProduct ";
  priceButton.setAttribute("data-id", product.id);
  priceButton.setAttribute("data-toggle", "modal");
  priceButton.setAttribute("data-target", "#updatePriceModal");

  let priceIcon = document.createElement("i");
  priceIcon.classList = "fas fa-euro-sign";

  let priceSpan = document.createElement("span");
  priceSpan.classList = "button-text";
  priceSpan.innerHTML = "Update price";

  priceButton.appendChild(priceIcon);
  priceButton.appendChild(priceSpan);

  let discountButton = document.createElement("button");
  discountButton.setAttribute("type", "submit");
  discountButton.classList = "btn btn-primary btn-sm updateProduct ";
  discountButton.setAttribute("data-id", product.id);
  discountButton.setAttribute("data-toggle", "modal");
  discountButton.setAttribute("data-target", "#addProductDiscountModal");

  let discountIcon = document.createElement("i");
  discountIcon.classList = "fas fa-tags";

  let discountSpan = document.createElement("span");
  discountSpan.classList = "button-text";
  discountSpan.innerHTML = "Add discount";

  discountButton.appendChild(discountIcon);
  discountButton.appendChild(discountSpan);

  let header = document.createElement("th");
  header.setAttribute("scope", "row");
  header.innerHTML = `<a href="/product/${product.id}">${product.id}</a>`;

  let stock = document.createElement("td");
  stock.innerHTML = product.stock;
  let price = document.createElement("td");
  price.innerHTML = product.price;

  let enabled = document.createElement("td");
  enabled.innerHTML = is_enabled;

  let newCell = document.createElement("td");
  newCell.appendChild(button);
  newCell.appendChild(stockButton);
  newCell.appendChild(priceButton);
  newCell.appendChild(discountButton);

  new_product.appendChild(header);
  new_product.appendChild(stock);
  new_product.appendChild(price);
  new_product.appendChild(enabled);
  new_product.appendChild(newCell);

  return new_product;
}

addEventListeners();
