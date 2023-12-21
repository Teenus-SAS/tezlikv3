$(document).ready(function () {
    $('.cardDashboard').hide();

    $('#month').change(function (e) {
        e.preventDefault();
        
        this.value == '0' ? loadTblPrices(historical, null, null) : loadTblPrices(historical, 'month', this.value);
    });

    $('#year').change(function (e) {
        e.preventDefault();
        this.value == '0' ? loadTblPrices(historical, null, null) : loadTblPrices(historical, 'year', this.value);
    }); 

    $('.typeHistorical').click(function (e) {
        e.preventDefault();
        
        if (this.id == 'btnList') { 
            $('.cardTblPrices').show(800);
            $('.cardDashboard').hide(800);
        }
        else {
            $('.cardDashboard').show(800);
            $('.cardTblPrices').hide(800);
        }
    });
});