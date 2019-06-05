$('.cart .dropdown-menu').click(function(e) {
	e.stopPropagation();
});

$(document).on("click", ".updateMember", function() {
  var staffMemberId = $(this).data("id");
  $(".modal-body [name=id]").val(staffMemberId);
  // As pointed out in comments,
  // it is unnecessary to have to manually call the modal.
  // $('#addBookDialog').modal('show');
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

  let proceed = document.querySelector(".proceed");
  if (proceed != null)
	proceed.addEventListener("click", proceedToPayment);
	
	let goBack = document.querySelector(".go-back");
	if (goBack != null)
		goBack.addEventListener("click", goBackToBilling);

	let checkout_btn = document.querySelector(".checkout-btn");
	if (checkout_btn != null)
		checkout_btn.addEventListener("click", verifyCheckout);
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

  
  if(id === null){
    sendAjaxRequest(
      "post",
      "/api/wishlist/",
      { id_product: id_product },
      addedToWishlistHandler
    );
  }
  else{
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

  let oldForm = document.querySelector('form#updateWishlist');
  let newForm = getAddToWishListForm(wishlist);
  oldForm.parentNode.replaceChild(newForm, oldForm);
}

function removedFromCartHandler(){
  let cart = JSON.parse(this.responseText);

  let oldForm = document.querySelector('form#updateCart');
  let newForm = getAddToCartForm(cart);
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
    }
    else{
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

  let message = document.querySelector('#addMember .modal-body .message');

  if(response['errors']!=null){
    message.classList.add('error');
    message.classList.remove('success');
    message.innerHTML = response['errors'][0];
    return;
  }
  message.classList.add('success');
  message.classList.remove('error');
  let feedbackMsg = "Staff member added with success";
  message.innerHTML = feedbackMsg;

  let newRow = createStaffMemberRow(response);
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
    }
    else{
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

  let oldForm = document.querySelector('form#updateWishlist');
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
  <input type="hidden" class="d-none    " name="id_product" value=${wishlist.id_product}>
  <input type="hidden" class="d-none    " name="id" value=${wishlist.id}>
  <button type="submit" class="btn btn-primary float-right">
      Remove from wishlist <i class="fas fa-bookmark"></i>
  </button>`
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
  <input type="hidden" class="d-none    " name="id_product" value=${wishlist.id_product}>
  <button type="submit" class="btn btn-primary float-right">
      Add to wishlist <i class="fas fa-bookmark"></i>
  </button>`

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
  let newRow = createStaffMemberRow(staff_member);
  row.parentNode.replaceChild(newRow, row);

  $('#confirmEnable').modal('hide')
  $('#confirmDisable').modal('hide')

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
	if(window.location.pathname.split("/").pop() === "checkout")
		return;

	form = document.querySelector("form[class*=billingInfo]");
  }

  form.innerHTML = newForm;
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
		
		event.preventDefault();
	}
}

addEventListeners();