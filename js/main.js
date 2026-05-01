let modal = new bootstrap.Modal(document.getElementById('error-modal'));

const opportunity = document.querySelectorAll('.input-opportunity');
opportunity.forEach(function (item) {
   item.addEventListener('input', removeInvalid);
});

function removeInvalid() {
   this.classList.remove('is-invalid');
}

function addValidationListeners(selector) {
   document.querySelectorAll(selector).forEach(function (item) {
      item.addEventListener('input', removeInvalid);
   });
}

function renderField(id, label, inputClass, suffixText, invalidText) {
   return `<div class="col-lg-3 col-12 col-md-6 mb-4">
            <div class="fw-bold">
               <label for="${id}" class="form-label mb-1">${label} ${suffixText}</label>
               <input type="number" step="any" min="0" inputmode="decimal" class="form-control ${inputClass}" id="${id}">
               <div class="invalid-feedback">${invalidText}</div>
            </div>
         </div>`;
}

function getSelectedPlants() {
   return Array.from(document.querySelectorAll('#second-block input[type=checkbox]:checked')).map(function (item) {
      return {
         id: item.dataset.plantId,
         name: item.dataset.plantName,
         label: item.dataset.plantLabel,
         key: `plant-${item.dataset.plantId}`
      };
   });
}

function firstBtnClick(event, prev, next) {
   event.preventDefault();
   let count = 0;
   let opportunityInputs = document.querySelectorAll('.input-opportunity');

   opportunityInputs.forEach(function (item) {
      if (!item.value) {
         item.classList.add('is-invalid');
      } else {
         count++;
      }
   });

   if (document.querySelector('#first-block input.is-invalid')) {
      document.querySelector('#first-block input.is-invalid').focus();
   }

   if (count === 6) {
      document.getElementById(prev).classList.add('d-none');
      document.getElementById(next).classList.remove('d-none');
   }
}

function secondBtnClick(event, prev, next) {
   event.preventDefault();

   let priceBlock = document.getElementById('price-block');
   let selectedPlants = getSelectedPlants();
   let dynamicHTML = '';

   if (selectedPlants.length >= 2) {
      dynamicHTML += renderField('paxta-narxi', '1 kg Paxta', 'input-price', 'narxi:', 'Paxta narxini kiritish shart');
      dynamicHTML += renderField('bugdoy-narxi', '1 kg Bug\'doy', 'input-price', 'narxi:', 'Bug\'doy narxini kiritish shart');

      selectedPlants.forEach(function (plant) {
         dynamicHTML += renderField(
            `${plant.key}-narxi`,
            `1 kg ${plant.label}`,
            'input-price',
            'narxi:',
            `${plant.label} narxini kiritish shart`
         );
      });

      priceBlock.innerHTML = dynamicHTML;
      addValidationListeners('.input-price');
      document.getElementById(prev).classList.add('d-none');
      document.getElementById(next).classList.remove('d-none');
   } else {
      document.getElementById('modal-change-text').textContent = 'Kamida ikkita ekin turini tanlang';
      modal.show();
   }
}

function showPrevBlock(event, prev, next) {
   event.preventDefault();
   document.getElementById(prev).classList.remove('d-none');
   document.getElementById(next).classList.add('d-none');
}

function thrirdBtnClick(event, prev, next) {
   event.preventDefault();

   let count = 0;
   let price = document.querySelectorAll('.input-price');

   price.forEach(function (item) {
      if (!item.value) {
         item.classList.add('is-invalid');
      } else {
         count++;
      }
   });

   if (document.querySelector('#price-block input.is-invalid')) {
      document.querySelector('#price-block input.is-invalid').focus();
   }

   if (count !== price.length) {
      return;
   }

   let sentnerBlock = document.getElementById('sentner-block');
   let selectedPlants = getSelectedPlants();
   let dynamicHTML = '';

   dynamicHTML += renderField('paxta-sentner', 'Paxta', 'input-sentner', 'sentnerda:', 'To\'ldirilishi shart');
   dynamicHTML += renderField('bugdoy-sentner', 'Bug\'doy', 'input-sentner', 'sentnerda:', 'To\'ldirilishi shart');

   selectedPlants.forEach(function (plant) {
      dynamicHTML += renderField(
         `${plant.key}-sentner`,
         plant.label,
         'input-sentner',
         'sentnerda:',
         'To\'ldirilishi shart'
      );
   });

   sentnerBlock.innerHTML = dynamicHTML;
   addValidationListeners('.input-sentner');
   document.getElementById(prev).classList.add('d-none');
   document.getElementById(next).classList.remove('d-none');
}

function sendFarmerData(event) {
   event.preventDefault();
   let count = 0;
   let sentnerInputs = document.querySelectorAll('.input-sentner');

   sentnerInputs.forEach(function (item) {
      if (!item.value) {
         item.classList.add('is-invalid');
      } else {
         count++;
      }
   });

   if (document.querySelector('#sentner-block input.is-invalid')) {
      document.querySelector('#sentner-block input.is-invalid').focus();
   }

   if (sentnerInputs.length !== count) {
      return;
   }

   let inputs = document.querySelectorAll('input[type="number"]');
   let farmerData = [];

   inputs.forEach(function (input) {
      farmerData.push({
         id: input.id,
         value: input.value
      });
   });

   let request = new XMLHttpRequest();
   request.open('POST', 'answer.php');
   request.setRequestHeader('Content-Type', 'application/json');

   request.onload = function () {
      let responseText = request.responseText.trim();

      if (request.status >= 200 && request.status < 300 && responseText !== 'error') {
         window.location.href = 'show_answer.php?id=' + encodeURIComponent(responseText);
      } else {
         document.getElementById('firth-block').classList.add('d-none');
         document.getElementById('first-block').classList.remove('d-none');
         document.getElementById('modal-change-text').textContent = 'Mavjud resurslar yetarli emas yoki ma\'lumot yuborishda xatolik yuz berdi';
         modal.show();
      }
   };

   request.onerror = function () {
      document.getElementById('modal-change-text').textContent = 'Server bilan bog\'lanishda xatolik yuz berdi';
      modal.show();
   };

   request.send(JSON.stringify(farmerData));
}
