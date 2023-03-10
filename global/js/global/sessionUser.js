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

    /* Tiempo de inactividad */

    var interval, mouseMove;

    $(document).mousemove(function () {
      mouseMove = new Date();
      inactividad(function () {
        fetchindata();
      }, 1600);
    });

    var inactividad = function (callback, seconds) {
      clearInterval(interval);
      interval = setInterval(function () {
        var now = new Date();
        var diff = (now.getTime() - mouseMove.getTime()) / 1000;
        if (diff >= seconds) {
          clearInterval(interval);
          callback();
        }
      }, 200);
    };
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
