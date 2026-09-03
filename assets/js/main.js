const ModalDialog = document.getElementById("crud-modal-overlay");
const form = document.getElementById("crud-modal-form");
const modalTitle = document.getElementById("crud-modal__title");
const buttonSubmit = document.getElementById("crud-modal__btn-submit");
const inputId = document.getElementById("crud-modal__product-id");
const inputName = document.getElementById("crud-modal__product-name");
const inputPrice = document.getElementById("crud-modal__product-price");
const inputDescription = document.getElementById(
  "crud-modal__product-description",
);
const inputAvaibility = document.getElementById("crud-modal__product-date");
const inputStock = document.getElementById("crud-modal__product-stock");
const imageContainer = document.getElementById("crud-modal__image-preview");
const inputImageFile = document.getElementById("crud-modal__product-image");
const curentImage = document.getElementById("crud-modal__image-current");
const modalContainer = document.getElementById("crud-modal");
const btnChangeImage = document.getElementById("crud-modal__btn-product-image");
const errorBlock = document.getElementById("crud-modal__errorBlock");
const formHeader = document.getElementById("crud-modal__header");
const curentImageName = document.getElementById(
  "crud-modal__curent-image-name",
);
const errorList = document.getElementById("crud-modal__errorList");

let blobImageURL = null;
function createPreviewImage(e) {
  const imageFile = e.target.files[0];
  if (imageFile) {
    blobImageURL = URL.createObjectURL(imageFile);
    curentImage.src = blobImageURL;
    imageContainer.style.display = "flex";
  } else {
    imageContainer.style.display = "none";
  }
}
function deleteBlobAdress() {
  if (blobImageURL != null) {
    URL.revokeObjectURL(blobImageURL);
    blobImageURL = null;
  }
}
curentImage.addEventListener("load", deleteBlobAdress);
inputImageFile.addEventListener("change", createPreviewImage);

function crudModal(button) {
  const id = button.dataset.id;
  const name = button.dataset.name;
  const price = button.dataset.price;
  const description = button.dataset.description;
  const date = button.dataset.avaibility;
  const stock = button.dataset.stock;
  const image = button.dataset.image;
  const action = button.dataset.action;

  inputName.readOnly = false;
  inputPrice.readOnly = false;
  inputDescription.readOnly = false;
  inputStock.disabled = false;
  inputAvaibility.readOnly = false;

  inputImageFile.type = "file";

  inputStock.style.display = "flex";
  ModalDialog.style.display = "flex";
  modalContainer.style.border = "none";
  btnChangeImage.style.display = "flex";
  formHeader.style.borderBottom = "solid 1px white";
  errorBlock.style.display = "none";

  if (action == "edit" || action == "delete") {
    if (inputId) inputId.value = id;
    if (inputName) inputName.value = name;
    if (inputPrice) inputPrice.value = price;
    if (inputDescription) inputDescription.value = description;
    if (stock == 1) inputStock.checked = true;
    else inputStock.checked = false;

    if (inputAvaibility && date) {
      const d = new Date(date);
      if (!isNaN(d.getTime())) {
        inputAvaibility.value = d.toISOString().split("T")[0];
      }
    } else inputAvaibility.value = "";

    if (image) {
      curentImage.src = "assets/uploads/" + image;
      curentImageName.value = image;
      imageContainer.style.display = "flex";
    } else if (imageContainer) {
      imageContainer.style.border = "solid 2px #var(--yellow-color)";
    }
    if (action == "edit") {
      modalTitle.textContent = "Edit Product";
      buttonSubmit.textContent = "Save changes";
      btnChangeImage.textContent = "Change image";

      form.action = "crud/update_product.php";
    } else {
      modalTitle.textContent = "Are you sure you want o delete this item?";
      buttonSubmit.textContent = "Delete product";

      modalContainer.style.border = "solid 1px red";
      formHeader.style.borderBottom = "solid 1px red";

      inputStock.disabled = true;
      inputName.readOnly = true;
      inputPrice.readOnly = true;
      inputDescription.readOnly = true;
      inputAvaibility.readOnly = true;

      inputStock.style.display = "flex";
      btnChangeImage.style.display = "none";
      errorBlock.style.display = "none";
      inputImageFile.type = "hidden";

      form.action = "crud/delete_product.php";
    }
  } else if (action == "add") {
    modalTitle.textContent = "Add a new product";
    buttonSubmit.textContent = "Add product";
    btnChangeImage.textContent = "Choose image";

    imageContainer.style.display = "none";
    curentImageName.value = "";

    form.action = "crud/create_product.php";
    form.reset();
  }
}

function closeAddEditModal() {
  if (ModalDialog) {
    ModalDialog.style.display = "none";
    errorList.innerHTML = "";
    curentImage.src = "";
    form.reset();
  }
}
function closeOverlay(e) {
  if (e.target === ModalDialog) closeAddEditModal();
}
window.addEventListener("click", closeOverlay);

function openStatusModal(message, status) {
  const modal = document.getElementById("status-modal__overlay");
  const messageContainer = document.getElementById("status-modal__message");
  const statusField = document.getElementById("status-modal__error-type");
  const statusModal = document.getElementById("status-modal__container");
  const statusHeader = document.getElementById("status-modal__header");
  const generealErrorBlock = document.getElementById(
    "status-modal__general-error",
  );

  if (messageContainer) {
    messageContainer.innerHTML = message;

    if (status == "Add error" || status == "Edit error") {
      generealErrorBlock.style.display = "flex";
      messageContainer.style.display = "none";
      statusModal.style.border = "1px solid red";
      statusHeader.style.borderBottom = "1px solid red";
      statusField.style.color = "red";
    } else if (status == "Succes") {
      generealErrorBlock.style.display = "none";
      messageContainer.style.display = "flex";
      statusModal.style.border = "1px solid green";
      statusHeader.style.borderBottom = "1px solid green";
      statusField.style.color = "green";
      statusField.innerHTML = status;
    }

    statusField.innerHTML = status;
    modal.style.display = "flex";
    modalContainer.style.border = "none";
  }
}
function closeStatusModal() {
  const modal = document.getElementById("status-modal__overlay");
  if (modal) modal.style.display = "none";
}
function closeStatusOverlay(e) {
  const modal = document.getElementById("status-modal__overlay");
  if (e.target === modal) closeStatusModal();
}
window.addEventListener("click", closeStatusOverlay);

function validationInputJS(e) {
  let errors = [];

  const trimName = inputName.value.trim();
  if (trimName != "") {
    if (trimName.length < 3 || trimName.length > 50)
      errors.push("Name must be between 3-50 characters.");
  } else errors.push("Name is required.");

  const numberPrice = Number(inputPrice.value.replace(",", "."));
  if (numberPrice != "") {
    if (!isNaN(numberPrice)) {
      if (numberPrice <= 0) errors.push("Price must be greaten than 0.");
    } else
      errors.push("Price must contain only numbers and maximum one point.");
  } else errors.push("Price is required.");

  const trimDescription = inputDescription.value.trim();
  if (trimDescription != "") {
    if (trimDescription.length < 3 || trimDescription.length > 2000)
      errors.push("Description must be between 3-2000 characters.");
  } else errors.push("Description is required");

  if (inputAvaibility.value == "") errors.push("Date is required");

  if (inputImageFile.type != "hidden") {
    if (inputImageFile.files.length != 0) {
      const file = inputImageFile.files[0];
      if (file.type == "image/jpeg" || file.type == "image/png") {
        if (file.size > 2097152) errors.push("Image size must not exceed 2MB ");
      } else errors.push("Image extension must be JPG or PNG.");
    } else if (curentImageName.value == 0) errors.push("Image is required");
  }

  let htmlListElemet = "";

  if (errors.length > 0) {
    e.preventDefault();
    errorBlock.style.display = "block";
    function displayError(error) {
      htmlListElemet += "<li>" + error + "</li>";
    }
    errors.forEach(displayError);
    errorList.innerHTML = htmlListElemet;
  } else {
    errorBlock.style.display = "none";
    errorList.innerHTML = "";
    this.submit();
  }
}

form.addEventListener("submit", validationInputJS);
