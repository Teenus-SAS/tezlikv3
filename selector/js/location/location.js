$(document).ready(function () {
  $('.btnLocation').mouseover(function () {
    $('.btnLocation').css('cursor', 'pointer');
  });

  $('.btnLocation').click(function (e) {
    e.preventDefault();
    id = this.id;

    if (id == 1) {
      location = '../../cost/';
    } else {
      location = '../../planning/';
    }

    location.href = r.location;
  });
});