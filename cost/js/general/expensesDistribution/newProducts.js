$(document).ready(function () {
    $('.cardNewProduct').hide();
    
    $('#btnNewProducts').click(function (e) { 
        e.preventDefault();
        
        $('.cardNewProduct').show(800);
        $('#formNewProduct').trigger('reset');
    });

    $('#newRefProduct').change(function (e) {
        e.preventDefault(); 
        
        $('#newNameProduct option').prop('selected', function () {
            return $(this).val() == id;
        }); 
    });

    $('#newNameProduct').change(function (e) {
        e.preventDefault(); 
        
        $('#newRefProduct option').prop('selected', function () {
            return $(this).val() == id;
        }); 
    });

    $('#oldNameProduct').change(function (e) {
        e.preventDefault();
        let id = this.value;
        let data = JSON.parse(sessionStorage.getItem('dataProducts'));

        data = data.find(item => item.id_product == id);

        $('#pAssignableExpense').val((data.assignable_expense).toFixed(2));
    });

    $('#btnAddNewProduct').click(function (e) { 
        e.preventDefault();

        let newProduct = $('#newNameProduct').val();
        let oldProduct = $('#oldNameProduct').val();

        let data = newProduct * oldProduct;

        if (!data || data <= 0) {
            toastr.error('Ingrese todos los campos');
            return false;
        }
        
        let dataExpense = $('#formNewProduct').serialize();

        // let assignableExpense = $('#pAssignableExpense').val();

        dataExpense = `${dataExpense}&assignableExpense=${assignableExpense}`;

        $.post('/api/saveNewProduct', dataExpense,
            function (data, textStatus, jqXHR) {
                message(data);
            },
        );
    });
});