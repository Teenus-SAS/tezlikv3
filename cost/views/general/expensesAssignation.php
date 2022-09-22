<?php

use tezlikv3\dao\UserInactiveTimeDao;

require_once(dirname(dirname(dirname(__DIR__))) . "/api/src/dao/app/global/login/UserInactiveTimeDao.php");
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>
<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Gastos Generales</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Asignación de Gastos Generales</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnNewExpense">Nuevo Gasto</button>
                    <button class="btn btn-info ml-3" id="btnImportNewExpenses">Importar Gastos</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardCreateExpenses">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formCreateExpenses">
                            <div class="gridx3">
                                <label for="">Cuenta</label>
                                <label for="">Valor</label>
                                <label for=""></label>
                                <select class="form-control" name="idPuc" id="idPuc"></select>
                                <input type="text" class="form-control number text-center" id="expenseValue" name="expenseValue">
                                <button class="btn btn-primary" id="btnCreateExpense">Crear Gasto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardImportExpensesAssignation">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportExpesesAssignation" enctype="multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="fileExpensesAssignation" accept=".xls,.xlsx">
                                    <label for="formFile" class="form-label">Importar Asignacion de Gastos</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportExpensesAssignation">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsExpensesAssignation">Descarga Formato</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- page content -->
<div class="page-content-wrapper mt--45">
    <div class="container-fluid">
        <!-- Row 5 -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblExpenses">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../global/js/global/number.js"></script>
<script src="/cost/js/general/expenses/tblExpenses.js"></script>
<script src="/cost/js/general/expenses/expense.js"></script>
<script src="/cost/js/general/puc/configPuc.js"></script>
<script src="../global/js/import/import.js"></script>
<script src="/cost/js/general/expenses/importExpense.js"></script>
<script src="../global/js/import/file.js"></script>