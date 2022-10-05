$(document).ready(function () {
  /*$.ajax({
    url: '/api/checkSessionUser',
    success: function (data, textStatus, xhr) {
      if (data.inactive) {
        location.href = '/';
        toastr.error(data.message);
      }
    },
  }); */
  var timeout;
  var prevKey = '';

  /* Cierre de pagina 
  $(window).on('mouseover', function () {
    window.onbeforeunload = null;
  });
  $(window).on('mouseout', function () {
    window.onbeforeunload = ConfirmLeave;
  });
  function ConfirmLeave() {
    logoutUser();
  }*/

  $(document).keydown(function (e) {
    if (e.key.toUpperCase() == 'W' && prevKey == 'CONTROL') {
      logoutUser();
    } else if (
      e.key.toUpperCase() == 'F4' &&
      (prevKey == 'ALT' || prevKey == 'CONTROL')
    ) {
      logoutUser();
    }
    prevKey = e.key.toUpperCase();
  });

  /* Tiempo de inactividad */
  $(document).on('mousemove', function (event) {
    if (timeout !== undefined) {
      window.clearTimeout(timeout);
    }
    timeout = window.setTimeout(function () {
      $(event.target).trigger('mousemoveend');
    }, 7 * 60 * 1000); // pasados 5 minutos
  });

  $(document).on('mousemoveend', function () {
    fetchindata();
  });

  fetchindata = async () => {
    resp = await logoutUser();

    if (resp.inactive) {
      setTimeout(function () {
        location.href = '/';
      }, 2000);
      toastr.error(resp.message);
    }
  };

  logoutUser = async () => {
    try {
      result = await $.ajax({
        url: '/api/logoutInactiveUser',
      });
      return result;
    } catch (error) {
      console.error(error);
    }
  };
});
