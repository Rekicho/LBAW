$(document).on("click", ".updateMember", function() {
  var staffMemberId = $(this).data("id");
  $(".modal-footer [name=id]").val(staffMemberId);
  // As pointed out in comments,
  // it is unnecessary to have to manually call the modal.
  // $('#addBookDialog').modal('show');
});

function addEventListeners() {
  let addStaffMember = document.querySelector("#addMember form");
  if (addStaffMember != null)
    addStaffMember.addEventListener("submit", sendCreateStaffMemberRequest);

  let disableStaffMember = document.querySelector("#confirmDisable form");
  if (disableStaffMember != null)
    disableStaffMember.addEventListener("submit", sendUpdateStaffMemberRequest);

  let enableStaffMember = document.querySelector("#confirmEnable form");
  if (enableStaffMember != null)
    enableStaffMember.addEventListener("submit", sendUpdateStaffMemberRequest);

  let updateBillingInformation = document.querySelector("form#billingInfo");
  if (updateBillingInformation != null) {
    updateBillingInformation.addEventListener(
      "submit",
      sendUpdateBillingInformationRequest
    );
  }

  let addToWishlist = document.querySelector("form#addToWishlist");
  if (addToWishlist != null) {
    addToWishlist.addEventListener("submit", sendAddToWishlistRequest);
  }
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

function sendAddToWishlistRequest(event) {
  let id_product = this.querySelector("input[name=id_product]").value;
  console.log(id_product);
  sendAjaxRequest(
    "post",
    "/api/wishlist/",
    { id_product: id_product },
    addedToWishlistHandler
  );

  event.preventDefault();
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
    { is_enabled: is_enabled },
    staffMemberUpdatedHandler
  );

  event.preventDefault();
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
    console.log("oi");
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

function addedToWishlistHandler() {
  console.log(this.status);

  let wishlist = this.responseText;
  console.log(wishlist);
}

function staffMemberAddedHandler() {
  console.log(this.status);
  let staff_member = JSON.parse(this.responseText);

  let newRow = createStaffMemberRow(staff_member);
  let table = document.getElementById("staffMemberTable");
  table.appendChild(newRow);
}

function staffMemberUpdatedHandler() {
  console.log(this.status);

  let staff_member = JSON.parse(this.responseText);

  let row = document.querySelector("[data-id='" + staff_member.id + "']");
  console.log(row);
  let newRow = createStaffMemberRow(staff_member);
  row.parentNode.replaceChild(newRow, row);
}

function billingInformationUpdatedHandler() {
  console.log(this.status);
  let billingInfo = JSON.parse(this.responseText);
  let newForm = createBillingInfoForm(billingInfo);
  let form = document.querySelector("form#billingInfo");
  form.innerHTML = newForm;
}

function createBillingInfoForm(billingInfo) {
  console.log(billingInfo.full_name);
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
<input type="hidden" name="id" value=${billingInfo.id}}>
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
  span.innerHTML = staff_member.is_enabled ? "Disable" : "Enable";

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

addEventListeners();
