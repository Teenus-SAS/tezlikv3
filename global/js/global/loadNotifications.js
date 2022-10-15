$(document).ready(function () {
  loadNotification = () => {
    $.ajax({
      url: '/api/recentNotification',
      success: function (resp) {
        if (resp.length == 0) {
          $('#clear').css('display', 'none');
          $('#showAll').css('display', 'none');
        }
        getNotifications(resp);
      },
    });
  };

  loadNotification();

  /* Cargar notificaciones */
  getNotifications = (data) => {
    $('#notify-scrollbar').empty();

    let n = 0;

    data.length > 5 ? (count = 5) : (count = data.length);

    for (i = 0; i < count; i++) {
      if (data[i].check_notification == 1) {
        n = n + 1;
        font = 'bold';
      } else font = 'normal';

      if (!data[i].logo) img = '';
      else img = data[i].logo;

      // Calcular tiempo transcurrido
      fecha = new Date(data[i].date_notification);
      lateDay = new Date(fecha.getFullYear(), fecha.getMonth() + 1, 0);
      hoy = new Date();

      tiempoPasado = hoy - fecha;
      segs = 1000;
      mins = segs * 60;
      hours = mins * 60;
      days = hours * 24;
      months = days * 30.416666666666668;
      years = months * 12;

      //calculo
      anos = Math.floor(tiempoPasado / years);

      tiempoPasado = tiempoPasado - anos * years;
      meses = Math.floor(tiempoPasado / months);

      tiempoPasado = tiempoPasado - meses * months;
      dias = Math.floor(tiempoPasado / days);

      tiempoPasado = tiempoPasado - dias * days;
      horas = Math.floor(tiempoPasado / hours);

      tiempoPasado = tiempoPasado - horas * hours;
      minutos = Math.floor(tiempoPasado / mins);

      segundos = Math.floor(tiempoPasado / 1000);

      if (segundos <= 60) time = `${segundos} seconds`;
      else if (minutos <= 60) time = `${minutos} mins`;
      else if (horas <= 24) time = `${horas} hours`;
      else if (dias <= lateDay.getDate()) time = `${dias} days`;

      $('#notify-scrollbar').append(`
        <a href="javascript:void(0);" class="dropdown-item notification-item" style="font-weight: ${font}">
          <div class="media">
            <div class="avatar-xs">
              <img class="img-fluid rounded-circle" src="${img}"> 
            </div>
            <p class="media-body">
              ${data[i].description}
              <small class="text-muted">${time} ago </small>
            </p>
          </div>
        </a>
      `);
    }
    $('#count').html(n);
  };

  /* Limpiar notificaciones */
  $('#clear').click(function (e) {
    e.preventDefault();
    $.get('/api/updateCheckNotification', function (data, textStatus, jqXHR) {
      msgNotification(data);
    });
  });

  msgNotification = (data) => {
    if (data.success == true) {
      loadNotification();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
