$(document).ready(function () {
    $('.cardDashboard').hide();

    $('#month').change(function (e) {
        e.preventDefault();

        let data = historical;

        if(this.value != '0'){
            let year = $('#year').val();

            if (year && year != '0')
                data = data.filter((item) => item.month == month && item.year == this.value); 
            else
                data = data.filter((item) => item.month == this.value);
        }

        loadTblPrices(data);
        historicalIndicatiors(data); 
    });

    $('#year').change(function (e) {
        e.preventDefault();
        let data = historical;

        if(this.value != '0'){
            month = $('#month').val();
            if (month && month != '0') {
                data = data.filter((item) => item.month == month && item.year == this.value);
            }
            else
                data = data.filter((item) => item.year == this.value);
        }

        loadTblPrices(data);
        historicalIndicatiors(data);
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