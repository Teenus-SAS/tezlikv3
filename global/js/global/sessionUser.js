$(
  (function () {
    /* Cierre de pagina */
    $(window).on('mouseover', function () {
      window.onbeforeunload = null;
    });
    $(window).on('mouseout', function () {
      window.onbeforeunload = ConfirmLeave;
    });
    $('body').on('click', 'a', function () {
      window.onbeforeunload = null;
    });
    /*
    $(document).keydown(function (e) {
      if (e.key.toUpperCase() == 'W' && prevKey == 'CONTROL') {
        fetchindata();
      } else if (
        e.key.toUpperCase() == 'F4' &&
        (prevKey == 'ALT' || prevKey == 'CONTROL')
      ) {
        fetchindata();
      }
      prevKey = e.key.toUpperCase();
    }); */

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
  })(jQuery)
);

getApi = async (url) => {
  try {
    result = await $.ajax({
      url: url,
    });
    return result;
  } catch (error) {
    return 0;
  }
};

var timeout;
var prevKey = '';

checkSession = async () => {
  data = await getApi('/api/checkSessionUser');

  if (data == 0) {
    location.href = '/';
  }
};
checkSession();

function ConfirmLeave() {
  fetchindata();
}

fetchindata = async () => {
  resp = await getApi('/api/logoutInactiveUser');
  if (resp.inactive) {
    location.href = '/';
    toastr.error(resp.message);
  }
};
