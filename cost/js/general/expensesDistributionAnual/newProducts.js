$(document).ready(function () {
    $('.cardNewProduct').hide();
    
    $('#btnNewProducts').click(function (e) { 
        e.preventDefault();
        
        $('.cardImportExpenses').hide(800);
        $('.cardExpensesDistribution').hide(800); 
        $('.cardNewProduct').toggle(800);
        $('#formNewProduct').trigger('reset');
    });

    $('#newRefProduct').change(function (e) {
        e.preventDefault(); 
        let id = this.value;
        
        $('#newNameProduct option').prop('selected', function () {
            return $(this).val() == id;
        }); 
    });

    $('#newNameProduct').change(function (e) {
        e.preventDefault(); 
        let id = this.value;
        
        $('#newRefProduct option').prop('selected', function () {
            return $(this).val() == id;
        }); 
    });

    $('#oldNameProduct').change(function (e) {
        e.preventDefault();
        let id = this.value;
        let data = JSON.parse(sessionStorage.getItem('dataProducts'));

        data = data.find(item => item.id_product == id);

        $('#pAssignableExpense').val((parseFloat(data.assignable_expense)).toFixed(2));
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

        $.post('/api/saveNewProduct', dataExpense,
            function (data, textStatus, jqXHR) {
                messageDistribution(data, 1);
            },
        );
    });
});