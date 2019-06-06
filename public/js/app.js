$('.cart .dropdown-menu').click(function(e) {
	e.stopPropagation();
});

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
    "#wishlist div.single-product-info-text a.delete"
  );
  [].forEach.call(wishlistDeleters, function(deleter) {
    deleter.addEventListener("click", sendDeleteWishListRequest);
  });

  let productCartsDeleters = document.querySelectorAll(
    ":not(#wishlist) div.single-product-info-text a.delete"
  );
  [].forEach.call(productCartsDeleters, function(deleter) {
    deleter.addEventListener("click", sendDeleteCartProductRequest);
  });

  let addStaffMember = document.querySelector("#addMember form");
  if (addStaffMember != null)
    addStaffMember.addEventListener("submit", sendCreateStaffMemberRequest);

  let disableStaffMember = document.querySelector(".confirmDisable form");
  if (disableStaffMember != null)
    disableStaffMember.addEventListener("submit", sendUpdateStaffMemberRequest);

  let enableStaffMember = document.querySelector(".confirmEnable form");
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

  let updateCart = document.querySelector("form#updateCart");
  if (updateCart != null) {
    updateCart.addEventListener("submit", sendUpdateCartRequest);
  }

  let updatePassword = document.querySelector("form#updatePassword");
  if (updatePassword != null)
    updatePassword.addEventListener("submit", sendUpdatePasswordRequest);

  let updateEmail = document.querySelector("form#updateEmail");
  if (updateEmail != null)
    updateEmail.addEventListener("submit", sendUpdateEmailRequest);

    let updateStaffPassword = document.querySelector("div.change-password form");
    if (updateStaffPassword != null)
    updateStaffPassword.addEventListener("submit", sendUpdateStaffPasswordRequest);

  let proceed = document.querySelector(".proceed");
  if (proceed != null)
	proceed.addEventListener("click", proceedToPayment);
	
	let goBack = document.querySelector(".go-back");
	if (goBack != null)
		goBack.addEventListener("click", goBackToBilling);

	let checkout_btn = document.querySelector(".checkout-btn");
	if (checkout_btn != null)
		checkout_btn.addEventListener("click", verifyCheckout);

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

  let disableUser = document.querySelector(".confirmDisableUser form");
  if (disableUser != null)
    disableUser.addEventListener("submit", sendDisableUserRequest);

  let enableUser = document.querySelector(".confirmEnableUser form");
  if (enableUser != null)
    enableUser.addEventListener("submit", sendEnableUserRequest);

    let confirmPurchasePayment = document.querySelectorAll("form.confirmPurchasePaymentForm");
    if (confirmPurchasePayment != null){
      for(let i = 0; i < confirmPurchasePayment.length; i++){
        confirmPurchasePayment[i].addEventListener("submit", sendConfirmPurchasePaymentRequest);
      }
    }
    
  let addReview = document.querySelector("form#addReview");
  if (addReview != null)
	addReview.addEventListener("submit", sendAddReviewRequest);
	
	let received = document.querySelectorAll(".received");
	[].forEach.call(received, function(receivedInstance) {
	receivedInstance.addEventListener("click", confirmReception);
	});
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

function sendDeleteCartProductRequest() {
	let id = this.closest("li.single-product-info-container").getAttribute(
		"data-id"
	);

	sendAjaxRequest(
		"delete",
		"/api/cart/" + id,
		null,
		productCartDeleteHandler
	);
}

function productCartDeleteHandler() {
	let product = JSON.parse(this.responseText);
	let elements = document.querySelectorAll(
		'li.single-product-info-container[data-id="' + product.id + '"]'
	);

	[].forEach.call(elements, function(element) {
		element.remove();
	});

	let cart = document.querySelector(
		'.cart'
	);

	cart.setAttribute('data-count', parseInt(cart.getAttribute('data-count')) - 1);
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

function sendAddReviewRequest(event) {
  event.preventDefault();

  let id_product = this.querySelector("input[name=id_product]").value;
  let comment = this.querySelector("input[name=comment]").value;
  let rating = this.querySelector("input[name=rating]").value;

  if(comment.length < 50){
    let span = document.createElement("span");
    span.className+=" error";
    span.innerHTML = "Your comment must have at least 50 characters."
    this.appendChild(span);
    
    return;
  }

  sendAjaxRequest(
    "post",
    "/api/reviews/",
    {
      id_product: id_product,
      comment: comment,
      rating: rating,
    },
    addedReviewHandler
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

function sendUpdateStaffPasswordRequest(event) {
  event.preventDefault();

  let old_password = this.querySelector("input[name=old_password]").value;
  let password = this.querySelector("input[name=new_password]").value;
  let user_id = this.querySelector("input[name=user_id]").value;

  if (old_password != "" && password != "")
    sendAjaxRequest(
      "post",
      "/api/users/" + user_id,
      {
        type: "updateStaffPassword",
        old_password: old_password,
        password: password,
      },
      updatedStaffPasswordHandler
    );
}

function updatedStaffPasswordHandler(){
  let user = JSON.parse(this.responseText);
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

function sendUpdateCartRequest(event) {
  let id_product = this.querySelector("input[name=id_product]").value;
  let id = this.querySelector("input[name=id]");
  let quantity = this.querySelector("input[name=quantity]").value;

  
  if(id === null){
    sendAjaxRequest(
      "post",
      "/api/cart/",
      { id_product: id_product, quantity: quantity },
      addedToCartHandler
    );
  }
  else{
    sendAjaxRequest(
      "delete",
      "/api/cart/" + id.value,
      null,
      removedFromCartHandler
    );
  }

  event.preventDefault();
}

function removedFromWishListHandler(){
  let wishlist = JSON.parse(this.responseText);

  let oldForm = document.querySelector("form#updateWishlist");
  let newForm = getAddToWishListForm(wishlist);
  oldForm.parentNode.replaceChild(newForm, oldForm);
}

function removedFromCartHandler(){
  let cart = JSON.parse(this.responseText);

  let oldForm = document.querySelector('form#updateCart');
  let newForm = getAddToCartForm(cart);
  oldForm.parentNode.replaceChild(newForm, oldForm);
}

// TODO
function addedReviewHandler() {
  console.log(this.status);

  let review = JSON.parse(this.responseText);
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

function sendEnableUserRequest(event) {
  let id = this.querySelector("input[name=id]").value;

  sendAjaxRequest(
    "post",
    "/api/users/" + id,
    { type: "updateUser"},
    userUpdatedHandler
  );

  event.preventDefault();
}

function sendDisableUserRequest(event) {
  event.preventDefault();

  let id_client = this.querySelector("input[name=id]").value;
  let end_t = this.querySelector("input[name=end_t]").value;
  let reason = this.querySelector("textarea[name=reason]").value;

  console.log({ id_client: id_client, end_t: end_t, reason: reason})

  sendAjaxRequest(
    "post",
    "/api/bans/",
    { id_client: id_client, end_t: end_t, reason: reason},
    userUpdatedHandler
  );
}

function userUpdatedHandler(){
  let user = JSON.parse(this.responseText);

  let row = document.querySelector("[data-id='" + user.id + "']");
  let newRow = createUserRow(staff_member);
  row.parentNode.replaceChild(newRow, row);

  $("#confirmEnable").modal("hide");
  $("#confirmDisable").modal("hide");
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

function sendConfirmPurchasePaymentRequest(event) {
  event.preventDefault();                                                                               

  let id = this.querySelector("input[name=id_purchase]").value;
  let state = this.querySelector("input[name=id_purchase]").getAttribute("data-state");
  if(state == "Waiting for payment") {
	sendAjaxRequest(
		"put",
		"/api/purchases/" + id,
		{state: "Waiting for payment approval"},
		null
	);
  }

  sendAjaxRequest(
    "put",
    "/api/purchases/" + id,
    {state: "Paid"},
    null
  );

  sendAjaxRequest(
    "put",
    "/api/purchases/" + id,
    {state: "Shipped"},
    purchasePaymentConfirmedHandler
  );

}     
 
function purchasePaymentConfirmedHandler(){
  let response = JSON.parse(this.responseText);

  let table = document.getElementById("paymentsTable");
  let row = table.querySelector("tr#purchase-" + response.id_purchase);

  // TODO: mensagem a avisar que pagamento foi confirmado

  table.removeChild(row);
}

function categoryDiscountAddedHandler(){
  let discount = JSON.parse(this.responseText);
}

function sendUpdateBillingInformationRequest(event) {
  event.preventDefault();

  let id = document.querySelector("input[name=id]");
  let full_name = document.querySelector("input[name=full_name]").value;
  let city = document.querySelector("input[name=city]").value;
  let address = document.querySelector("input[name=address]").value;
  let state = document.querySelector("input[name=state]").value;
  let zip_code = document.querySelector("input[name=zip_code]").value;

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

  let newRow = createUserRow(response);
  let table = document.getElementById("staffMemberTable");
  table.appendChild(newRow);
}

function updatedEmailHandler() {
  let response = JSON.parse(this.responseText);

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
  let wishlist = JSON.parse(this.responseText);

  let oldForm = document.querySelector("form#updateWishlist");
  let newForm = getRemoveFromWishListForm(wishlist);
  oldForm.parentNode.replaceChild(newForm, oldForm);
}

function addedToCartHandler() {
  let cart = JSON.parse(this.responseText);

  updateCartnewProduct(cart);
}


function getRemoveFromWishListForm(wishlist){
  let form = document.createElement('form');
  form.setAttribute('id' ,'updateWishlist');

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

// function getRemoveFromCartForm(cart){
//   let form = document.createElement('form');
//   form.setAttribute('id' ,'updateCart');

//   form.innerHTML = `
//   <input type="hidden" class="d-none    " name="id_product" value=${cart.id_product}>
//   <input type="hidden" class="d-none    " name="id" value=${cart.id}>
//   <button type="submit" class="btn btn-primary float-right">
//       Remove from cart
//   </button>`
//   form.addEventListener("submit", sendUpdateCartRequest);

//   return form;
// }

function getAddToWishListForm(wishlist){
  let form = document.createElement('form');
  form.setAttribute('id' ,'updateWishlist');

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

function getAddToCartForm(cart){
  let form = document.createElement('form');
  form.setAttribute('id' ,'updateCart');

  form.innerHTML = `
  <input type="hidden" class="d-none    " name="id_product" value=${cart.id_product}>
  <button type="submit" class="btn btn-primary float-right">
      Add to cart
  </button>`

  form.addEventListener("submit", sendUpdateCartRequest);

  return form;
}

function staffMemberUpdatedHandler() {
  let staff_member = JSON.parse(this.responseText);
  let row = document.querySelector("[data-id='" + staff_member.id + "']");
  let newRow = createUserRow(staff_member);
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
  let billingInfo = JSON.parse(this.responseText);
  let newForm = createBillingInfoForm(billingInfo);
  let form = document.querySelector("form[data-id='" + billingInfo.id + "']");

  if(window.location.pathname.split("/").pop() === "checkout") {
	  document.querySelector(".billing-form").innerHTML += `<div class="alert alert-success" role="alert">
	  Shipping & Billing information updated
	</div>`;
  }

  if (form === null) {
	form = document.querySelector("form[class*=billingInfo]");
  }
  
  form.innerHTML = newForm;
  addEventListeners();
}

function createBillingInfoForm(billingInfo) {
  let form = `
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

  if(window.location.pathname.split("/").pop() === "checkout")
  	return form;

return `
<div class="my-3">
  <h3>Shipping & Billing Information</h3>
</div>
` + form;
}

function createUserRow(user) {
  let new_user = document.createElement("tr");
  new_user.setAttribute("data-id", user.id);

  let is_enabled = user.is_enabled ? "Enabled" : "Disabled";

  let button = document.createElement("button");
  button.setAttribute("type", "button");
  button.classList = "btn btn-sm updateMember ";
  button.classList += user.is_enabled ? "btn-danger" : "btn-success";
  button.setAttribute("data-id", user.id);
  button.setAttribute("data-toggle", "modal");

  if (user.is_enabled)
    button.setAttribute("data-target", "#confirmDisable");
  else button.setAttribute("data-target", "#confirmEnable");

  let icon = document.createElement("i");
  icon.classList = user.is_enabled
    ? "fas fa-minus-circle"
    : "fas fa-plus-circle";

  let span = document.createElement("span");
  span.classList = "button-text";
  span.innerHTML = user.is_enabled ? " Disable" : " Enable";

  button.appendChild(icon);
  button.appendChild(span);
  let header = document.createElement("th");
  header.setAttribute("scope", "row");
  header.innerHTML = user.username;

  let enabled = document.createElement("td");
  enabled.innerHTML = is_enabled;

  let newCell = document.createElement("td");
  newCell.appendChild(button);

  new_user.appendChild(header);
  new_user.appendChild(enabled);
  new_user.appendChild(newCell);

  return new_user;
}

function updateCartnewProduct(cart) {
	let cartList = document.querySelector(".cart ul");
	let newProduct = document.createElement("li");
	let productName = document.querySelector(".product-title").textContent;
	let productPrice = document.querySelector("#updateCart .price").textContent;
	let productRating = document.querySelectorAll(".product-info .product-rating .fas,fa-star").length;

	newProduct.classList.add("single-product-info-container");
	newProduct.setAttribute("data-id",cart.id);

	newProduct.innerHTML = 
	`
	<a href="/product/${cart.id_product}"> <img src="/img/product${cart.id_product}.jpg" alt=''></a>
	<div class='single-product-info-text'>
		<div class='row'>
    		<div class='col-6'>
			<a href="/product/${cart.id_product}"><span class='title'>${productName}</span></a>
			</div>
			<div class='col-6 state'>
                <a href='#' class='delete'><i class='fas fa-trash remove'></i></a>
			</div>
			<div>
	`

	for(let i = 0; i < productRating; i++)
		newProduct.innerHTML += "<i class='fas fa-star'></i>";

	for(let i = productRating; i < 5; i++)
		newProduct.innerHTML += "<i class='far fa-star'></i>";

	newProduct.innerHTML +=
	`       </div>
			<span class='oldprice'></span>
			<span class='price float-right'>${cart.quantity}x ${productPrice}</span>
	`;

	newProduct.querySelector("a.delete").addEventListener("click", sendDeleteCartProductRequest);

	cartList.appendChild(newProduct);
}

function proceedToPayment() {
	let full_name = document.querySelector("input[name=full_name]");
	let city = document.querySelector("input[name=city]");
	let address = document.querySelector("input[name=address]");
	let state = document.querySelector("input[name=state]");
	let zip_code = document.querySelector("input[name=zip_code]");

	if(full_name == null || city == null || address == null || state == null || zip_code == null) {
		document.querySelector(".billing-form").innerHTML += `<div class="alert alert-danger" role="alert">
		Shipping & Billing information needed to checkout
		</div>`;

		return;
	}

	if(full_name.valute == "" || city.value == "" || address.value == "" || state.value == "" || zip_code.value == "") {
		document.querySelector(".billing-form").innerHTML += `<div class="alert alert-danger" role="alert">
		Shipping & Billing information needed to checkout
		</div>`;

		return;
	}

	var event;

	if (document.createEvent) {
		event = document.createEvent("HTMLEvents");
		event.initEvent("event", true, true);
	} else {
		event = document.createEventObject();
		event.eventType = "event";
	}

	event.eventName = "event";

	sendUpdateBillingInformationRequest(event);

	document.querySelector(".billing").classList.add("d-none");
	document.querySelector(".payment").classList.remove("d-none");

	var url = location.href;
	location.href = "#payment";
	history.replaceState(null,null,url);
}

function goBackToBilling() {
	document.querySelector(".billing").classList.remove("d-none");
	document.querySelector(".payment").classList.add("d-none");

	var url = location.href;
	location.href = "#billing";
	history.replaceState(null,null,url);
}

function verifyCheckout(event){
	if(document.querySelector(".cart").getAttribute("data-count") == 0) {
		document.querySelector(".final").parentNode.innerHTML += `<div class="alert alert-danger" role="alert">
		Can't checkout an empty cart
		</div>`;
	}
	
	else location.href='/checkout';
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

function confirmReception(event) {
	event.preventDefault();
	let id = this.getAttribute("data-id");

	sendAjaxRequest(
		"put",
		"/api/purchases/" + id,
		{state: "Completed"},
		purchaseCompleted
	);
}

function purchaseCompleted() {
	let response = JSON.parse(this.responseText);
	let purchase = document.querySelector("#heading" + response["id_purchase"]);

	purchase.children[0].removeChild(purchase.querySelector(".received-div"));

	let complete = purchase.querySelector(".deactivate");

	complete.classList.remove("deactivate");
	complete.children[0].classList.remove("deactivate");

	let today = new Date();
	let month = '' + (today.getMonth() + 1);
    let day = '' + today.getDate();
	let year = today.getFullYear();
	
	if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

	complete.innerHTML += ": " + [year, month, day].join('-');
}

function getVals() {
	var parent = this.parentNode;
	var slides = parent.getElementsByTagName("input");
	  var slide1 = parseFloat( slides[2].value );
	  var slide2 = parseFloat( slides[3].value );

	if( slide1 > slide2 ){ var tmp = slide2; slide2 = slide1; slide1 = tmp; }
	
	var display1 = parent.getElementsByClassName("above")[0];
	var display2 = parent.getElementsByClassName("below")[0];

	display1.value = slide1;
	display2.value = slide2;
  }
  
window.onload = function() {
	var sliderSections = document.getElementsByClassName("range-slider");
	if (sliderSections == null)
		return;
	for( var x = 0; x < sliderSections.length; x++ ) {
		var sliders = sliderSections[x].getElementsByTagName("input");
		for( var y = 0; y < sliders.length; y++ ) {
			if( sliders[y].type ==="range" ) {
				sliders[y].oninput = getVals;
			}
		}
	}
}

addEventListeners();