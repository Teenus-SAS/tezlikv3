$(document).ready(function () {
    $('.cardDashboard').hide();

    $('#month').change(function (e) {
        e.preventDefault();
        
        if(this.value == '0')
            loadTblPrices(historical, null, null)
        else {
            let year = $('#year').val();
            if (year && year != '0')
                loadTblPrices(historical, 'month,year', `${this.value},${year}`);
            else
                loadTblPrices(historical, 'month', this.value);
        }
    });

    $('#year').change(function (e) {
        e.preventDefault();
        if(this.value == '0')
            loadTblPrices(historical, null, null)
        else {
            month = $('#month').val();
            if (month && month != '0')loadTblPrices(historical, 'month,year', `${month},${this.value}`);
            else
                loadTblPrices(historical, 'year', this.value);
        }
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