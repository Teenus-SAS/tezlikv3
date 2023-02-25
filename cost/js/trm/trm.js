// Calcular y modificar trm historico de los ultimos 2 años
calcHistoricalTrm = async () => {
  try {
    result = await $.ajax({
      url: '/api/updateHitoricalTRM',
    });
    return result;
  } catch (error) {
    return 0;
  }
};

let date = new Date();

let hours = date.getHours();
let minutes = date.getMinutes();

if (hours == 0 && minutes == 0) calcHistoricalTrm();
