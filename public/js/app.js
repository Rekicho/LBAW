
$(document).on("click", ".updateMember", function () {
  var staffMemberId = $(this).data('id');
  $(".modal-footer [name=id]").val( staffMemberId );
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

  let row = document.querySelector('[data-id=\'' + staff_member.id + '\']');
  console.log(row);
  let newRow = createStaffMemberRow(staff_member);
  row.parentNode.replaceChild(newRow, row);
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

  if(staff_member.is_enabled)
    button.setAttribute("data-target", "#confirmDisable")
  else
    button.setAttribute("data-target", "#confirmEnable")

  let icon = document.createElement("i");
  icon.classList = staff_member.is_enabled ? "fas fa-minus-circle" : "fas fa-plus-circle";

  let span = document.createElement("span");
  span.classList="button-text";
  span.innerHTML = staff_member.is_enabled ? "Disable" : "Enable"

  button.appendChild(icon);
  button.appendChild(span);
  let header = document.createElement("th");
  header.setAttribute("scope", "row");
  header.innerHTML = staff_member.username;

  let enabled = document.createElement('td');
  enabled.innerHTML = is_enabled;

  let newCell = document.createElement('td');
  newCell.appendChild(button);

  new_staff_member.appendChild(header);
  new_staff_member.appendChild(enabled);
  new_staff_member.appendChild(newCell);

  return new_staff_member;
}

addEventListeners();