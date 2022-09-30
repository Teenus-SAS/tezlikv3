/*$.ajax({
  url: '/api/checkSessionUser',
  success: function (data, textStatus, xhr) {
    if (data.inactive) {
      location.href = '/';
      toastr.error(data.message);
    }
  },
}); */

/* Tiempo de inactividad */
(function ($) {
  var timeout;
  $(document).on('mousemove', function (event) {
    if (timeout !== undefined) {
      window.clearTimeout(timeout);
    }
    timeout = window.setTimeout(function () {
      $(event.target).trigger('mousemoveend');
    }, 5 * 60 * 1000); // pasados 5 minutos
  });
})(jQuery);

$(document).on('mousemoveend', function () {
  $.ajax({
    url: '/api/logoutInactiveUser',
    success: function (data, textStatus, xhr) {
      if (data.inactive) {
        setTimeout(function () {
          location.href = '/';
        }, 2000);
        toastr.error(data.message);
      }
    },
  });
});
