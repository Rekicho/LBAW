
$(document).on("click", ".updateMember", function () {
  var staffMemberId = $(this).data('id');
  $(".modal-footer [name=id]").val( staffMemberId );
  // As pointed out in comments, 
  // it is unnecessary to have to manually call the modal.
  // $('#addBookDialog').modal('show');
});

function addEventListeners() {
  let itemCheckers = document.querySelectorAll(
    "article.card li.item input[type=checkbox]"
  );
  [].forEach.call(itemCheckers, function(checker) {
    checker.addEventListener("change", sendItemUpdateRequest);
  });

  let itemCreators = document.querySelectorAll("article.card form.new_item");
  [].forEach.call(itemCreators, function(creator) {
    creator.addEventListener("submit", sendCreateItemRequest);
  });

  let itemDeleters = document.querySelectorAll("article.card li a.delete");
  [].forEach.call(itemDeleters, function(deleter) {
    deleter.addEventListener("click", sendDeleteItemRequest);
  });

  let cardDeleters = document.querySelectorAll("article.card header a.delete");
  [].forEach.call(cardDeleters, function(deleter) {
    deleter.addEventListener("click", sendDeleteCardRequest);
  });

  let cardCreator = document.querySelector("article.card form.new_card");
  if (cardCreator != null)
    cardCreator.addEventListener("submit", sendCreateCardRequest);

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

function sendItemUpdateRequest() {
  let item = this.closest("li.item");
  let id = item.getAttribute("data-id");
  let checked = item.querySelector("input[type=checkbox]").checked;

  sendAjaxRequest(
    "post",
    "/api/item/" + id,
    { done: checked },
    itemUpdatedHandler
  );
}

function sendDeleteItemRequest() {
  let id = this.closest("li.item").getAttribute("data-id");

  sendAjaxRequest("delete", "/api/item/" + id, null, itemDeletedHandler);
}

function sendCreateItemRequest(event) {
  let id = this.closest("article").getAttribute("data-id");
  let description = this.querySelector("input[name=description]").value;

  if (description != "")
    sendAjaxRequest(
      "put",
      "/api/cards/" + id,
      { description: description },
      itemAddedHandler
    );

  event.preventDefault();
}

function sendDeleteCardRequest(event) {
  let id = this.closest("article").getAttribute("data-id");

  sendAjaxRequest("delete", "/api/cards/" + id, null, cardDeletedHandler);
}

function sendCreateCardRequest(event) {
  let name = this.querySelector("input[name=name]").value;

  if (name != "")
    sendAjaxRequest("put", "/api/cards/", { name: name }, cardAddedHandler);

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

function itemUpdatedHandler() {
  let item = JSON.parse(this.responseText);
  let element = document.querySelector('li.item[data-id="' + item.id + '"]');
  let input = element.querySelector("input[type=checkbox]");
  element.checked = item.done == "true";
}

function itemAddedHandler() {
  if (this.status != 200) window.location = "/";
  let item = JSON.parse(this.responseText);

  // Create the new item
  let new_item = createItem(item);

  // Insert the new item
  let card = document.querySelector(
    'article.card[data-id="' + item.card_id + '"]'
  );
  let form = card.querySelector("form.new_item");
  form.previousElementSibling.append(new_item);

  // Reset the new item form
  form.querySelector("[type=text]").value = "";
}

function itemDeletedHandler() {
  if (this.status != 200) window.location = "/";
  let item = JSON.parse(this.responseText);
  let element = document.querySelector('li.item[data-id="' + item.id + '"]');
  element.remove();
}

function cardDeletedHandler() {
  if (this.status != 200) window.location = "/";
  let card = JSON.parse(this.responseText);
  let article = document.querySelector(
    'article.card[data-id="' + card.id + '"]'
  );
  article.remove();
}

function cardAddedHandler() {
  if (this.status != 200) window.location = "/";
  let card = JSON.parse(this.responseText);

  // Create the new card
  let new_card = createCard(card);

  // Reset the new card input
  let form = document.querySelector("article.card form.new_card");
  form.querySelector("[type=text]").value = "";

  // Insert the new card
  let article = form.parentElement;
  let section = article.parentElement;
  section.insertBefore(new_card, article);

  // Focus on adding an item to the new card
  new_card.querySelector("[type=text]").focus();
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

function createCard(card) {
  let new_card = document.createElement("article");
  new_card.classList.add("card");
  new_card.setAttribute("data-id", card.id);
  new_card.innerHTML = `

  <header>
    <h2><a href="cards/${card.id}">${card.name}</a></h2>
    <a href="#" class="delete">&#10761;</a>
  </header>
  <ul></ul>
  <form class="new_item">
    <input name="description" type="text">
  </form>`;

  let creator = new_card.querySelector("form.new_item");
  creator.addEventListener("submit", sendCreateItemRequest);

  let deleter = new_card.querySelector("header a.delete");
  deleter.addEventListener("click", sendDeleteCardRequest);

  return new_card;
}

function createItem(item) {
  let new_item = document.createElement("li");
  new_item.classList.add("item");
  new_item.setAttribute("data-id", item.id);
  new_item.innerHTML = `
  <label>
    <input type="checkbox"> <span>${
      item.description
    }</span><a href="#" class="delete">&#10761;</a>
  </label>
  `;

  new_item
    .querySelector("input")
    .addEventListener("change", sendItemUpdateRequest);
  new_item
    .querySelector("a.delete")
    .addEventListener("click", sendDeleteItemRequest);

  return new_item;
}

addEventListeners();
