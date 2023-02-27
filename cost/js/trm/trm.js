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

calcHistoricalTrm();
