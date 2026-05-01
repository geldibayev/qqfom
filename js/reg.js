let modal = new bootstrap.Modal(document.getElementById('reg-error-modal'));

const opportunity = document.querySelectorAll('.reg-input, .log-input');
opportunity.forEach(function (item) {
      item.addEventListener('input', removeInvalid);
   }
);

function removeInvalid() {
   this.classList.remove('is-invalid');
}

// REGISTER PHP 

function checkInputs(event, inputClassName) {
   let inputs = document.querySelectorAll(inputClassName);
   let count = 0;
   inputs.forEach(function (input) {
      if (!input.value){
         input.classList.add('is-invalid');
      } else 
         count++;
   });
   if (document.querySelector('input.is-invalid'))
   document.querySelector('input.is-invalid').focus();  
   
   if (inputs.length == count) {
      
         if (document.getElementById('login').value.length < 4) {
            showModalWithText('Login uzunligi 4 ta simboldan kam bo\'lmasligi kerak');
            event.preventDefault();
         }
         else if (document.getElementById('pwd1').value.length < 6) {
            showModalWithText('Parol uzunligi 6 ta simboldan kam bo\'lmasligi kerak');
            event.preventDefault();
         }
         else if (document.getElementById('pwd1').value != document.getElementById('pwd2').value) {
            showModalWithText('Kiritilgan parollar bir xil emas');
            event.preventDefault();
         }
   } else {
      event.preventDefault();
   }
}

function checkLogin(event) {
   let count = 0;
   document.querySelectorAll('.log-input').forEach(function (input) {
      if (!input.value){
         input.classList.add('is-invalid');
      } else 
         count++;
   });
   
   if (document.querySelector('input.is-invalid'))
      document.querySelector('input.is-invalid').focus();  
   
   if (count != 2)
      event.preventDefault();
}



function showModalWithText(text) {
   let alertTextElem = document.getElementById('alert-custom-text');
   alertTextElem.innerText = text;
   modal.show();
}