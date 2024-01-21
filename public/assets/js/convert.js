// Drop box
const drag_zone = document.querySelector(".drag-zone");
const fileInput = document.getElementById("inputfile");

// boutons actions
const btn_settings = document.querySelector("#settings");
const btn_delete = document.querySelector("#delete");
const modal = document.querySelector(".modal-content");

function strUUID() {
  return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (c) {
    var r = (Math.random() * 16) | 0,
      v = c == "x" ? r : (r & 0x3) | 0x8;
    return v.toString(16);
  });
}

drag_zone.addEventListener( "dragover", (e) => {
    e.preventDefault();
    e.dataTransfer.dropEffect = "copy";
  },
  true
);

drag_zone.addEventListener("dragenter", () => {
  drag_zone.classList = "drag-zone drag-active";
});

drag_zone.addEventListener("dragleave", () => {
  drag_zone.classList = "drag-zone";
});

drag_zone.addEventListener("drop", (e) => {
  e.preventDefault();
  drag_zone.classList = "drag-zone";

  console.log(e.dataTransfer.files);
  //fileInput.files = e.dataTransfer.files;
});

let popup = document.querySelector(".btn-popup");
let menu_popup = document.querySelector(".menu-popup");

popup.addEventListener("mouseover", () => {
  menu_popup.classList.remove("collapse");
});

menu_popup.addEventListener("mouseleave", () => {
  menu_popup.classList.add("collapse");
});

popup.addEventListener("mouseleave", () => {
  menu_popup.classList.add("collapse");
});

inputfile.addEventListener("change", function () {
  // Vérifiez s'il y a un fichier sélectionné
  if (inputfile.files.length > 0) {
    // Obtenez le premier fichier sélectionné
    const selectedFile = inputfile.files[0];
    let file_name = selectedFile.name;
    let file_size = Math.round(selectedFile.size / 1024);

    if (file_size < 1024) {
      file_size = file_size + "Ko";
    } else {
      file_size = Math.round(file_size / 1024) + "Mo";
    }
    // Créez un objet URL pour le fichier
    const imageURL = URL.createObjectURL(selectedFile);
    let infos = { name: `${file_name}`, size: `${file_size}` };

    onAddImage(infos, imageURL);
  }
});

function selectFormat(select, popup) {
  if (select == null) return;

  select.addEventListener("mouseover", () => {
    popup.classList = "popup-format grid";
    popup.style.top = -popup.clientHeight - 2 + "px";
    popup.style.left =
      select.clientLeft - popup.clientWidth / 2 + select.clientWidth / 2 + "px";
    select.children[1].classList = "bx bx-caret-up";

    select.addEventListener("mouseleave", () => {
      popup.classList = "popup-format";
      select.children[1].classList = "bx bx-caret-down";
    });
  });

  const btnButtonsFormat = [...popup.children];

  btnButtonsFormat.forEach((el) => {
    el.addEventListener("click", () => {
      select.ariaValueText = el.textContent;
      select.children[0].innerText = el.textContent;
      popup.classList = "popup-format";
      select.children[1].classList = "bx bx-caret-down";
    });
  });
}

// fonctions modales
function openModal(modal) {
  modal.classList = "modal-content show";
}

function closeModal() {
  modal.classList = "modal-content hide";
}

function confirm() {
  //alert("Vous avez confirmé !");
  closeModal();
}

function onSettings() {
  openModal(modal);
}

function onAddImage(infos, url) {
  let countObj = strUUID();
  //let countObj = document.querySelectorAll(".image-wrapper").length + 1;
  let root = document.querySelector("#image-root");
  let imagewrapper = document.createElement("div");
  imagewrapper.classList = "image-wrapper";
  imagewrapper.innerHTML = `
  <div class="image-content ">
          <img src="${url}" id="img-${countObj}"></img>
        </div>
        <p class="ellipsis" id="img-infos">${infos.name}</p><p class="size">${infos.size}</p>
        <div class="ml-2 progressbar-wrapper">
          <div class="progressbar"><span>50%</span></div>
        </div>
        <span class="ml-1">Convertir en:</span>
        <div class="select-format" aria-valueText="JPG" id="format-${countObj}">
          <div>JPG</div><i class='bx bx-caret-down'></i>
          <div class="popup-format" id="popup-${countObj}">
            <a class="btn btn-format">JPG</a>
            <a class="btn btn-format">PNG</a>
            <a class="btn btn-format">BMP</a>
            <a class="btn btn-format ">TIFF</a>
          </div>
        </div>
        <div id="settings" onclick="onSettings(this);" class="action"><i class='bx bx-cog'></i></div>
        <div id="delete" onclick="onRemove(this);" class="action"><i class='bx bx-x'></i></div>
  `;

  root.appendChild(imagewrapper);
  const select_format = document.querySelector(`#format-${countObj}`);
  const popup_format = document.querySelector(`#popup-${countObj}`);
  selectFormat(select_format, popup_format);
}

function onRemove(elem) {
  elem.parentElement.remove();
}