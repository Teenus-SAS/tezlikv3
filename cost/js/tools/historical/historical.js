$(document).ready(function () {
    $('#typeHistorical').change(function (e) {
        e.preventDefault();
        
        loadTblPrices(this.value);
    });
});