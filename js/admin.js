let inputs = document.querySelectorAll('input.form-control');

inputs.forEach(input => {
   input.addEventListener('input', function () {
      input.classList.add('is-valid');
   });
});

