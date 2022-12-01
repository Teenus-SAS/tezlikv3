$(document).ready(function () {
  /* Cargar imagen */
  $('#formFile').change(function (e) {
    e.preventDefault();
    avatar.src = URL.createObjectURL(event.target.files[0]);
  });
});
