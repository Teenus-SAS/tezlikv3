window.historicalIndicatiors = (data) => {
    maxProfitability = 0;
    minProfitability = 0;
    let totalProfitability = 0;
    let averageProfitability = 0;

    if (data.length > 0) {
        maxProfitability = Math.max(...data.map(obj => obj.min_profitability));
        minProfitability = Math.min(...data.map(obj => obj.min_profitability));
        totalProfitability = data.reduce((acc, obj) => acc + obj.min_profitability, 0);
        averageProfitability = totalProfitability / data.length;
    }

    $('#lblMaxProfitability').html(` Rentab +Alta: ${maxProfitability.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`);
    $('#lblMinProfitability').html(` Rentab +Baja: ${minProfitability.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`);
    $('#lblAverageProfitability').html(` Rentab Prom: ${averageProfitability.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`);
}
