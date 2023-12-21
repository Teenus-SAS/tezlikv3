$(document).ready(function () {
    $('.cardDashboard').hide();

    $('#month').change(function (e) {
        e.preventDefault();
        
        this.value != '0' ? loadTblPrices(1, this.value, null) : loadTblPrices(null, null, null);
    });

    $('#year').change(function (e) {
        e.preventDefault();
        this.value != '0' ? loadTblPrices(2, null, this.value) : loadTblPrices(null, null, null);
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