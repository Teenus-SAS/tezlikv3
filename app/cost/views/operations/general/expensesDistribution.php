<?php

use tezlikv3\dao\UserInactiveTimeDao;

require_once(dirname(dirname(dirname(dirname(__DIR__)))) . "../../api/src/dao/app/cost/login/UserInactiveTimeDao.php");
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
                        <li class="breadcrumb-item active">Distribución Gastos Generales</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnExpensesDistribution">Distribuir Gastos</button>
                    <button class="btn btn-info ml-3" id="btnImportNewExpensesDistribution">Importar Distribuir Gastos</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardExpensesDistribution">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="gridx2">
                            <label for="">Gastos a distribuir</label>
                            <input type="text" class="form-control number text-center" id="expensesToDistribution" name="assignableExpense" style="width: 200px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-content-wrapper mt--45 mb-5 cardExpensesDistribution">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formExpensesDistribution">
                            <div class="gridx5">
                                <label for="">Referencia</label>
                                <label for="">Nombre Producto</label>
                                <label for="">Und Vendidas</label>
                                <label for="">Vol Ventas</label>
                                <label for=""></label>
                                <select class="form-control" name="refProduct" id="refProduct"></select>
                                <select class="form-control" name="selectNameProduct" id="selectNameProduct"></select>
                                <input type="text" class="form-control number text-center" id="undVendidas" name="unitsSold">
                                <input type="text" class="form-control number text-center" id="volVendidas" name="turnover">
                                <button class="btn btn-primary" id="btnAssignExpenses">Asignar Gasto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardImportDistributionExpenses">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportDistributionExpenses" enctype="multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="fileDistributionExpenses" accept=".xls,.xlsx">
                                    <label for="formFile" class="form-label">Importar Distribución</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportDistributionExpenses">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsDistributionExpenses">Descarga Formato</button>
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
                            <table class="table table-striped" id="tblExpensesDistribution">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../app/cost/js/global/number.js"></script>
<script src="../../app/cost/js/expensesDistribution/tblExpensesDistribution.js"></script>
<script src="../../app/cost/js/products/configProducts.js"></script>
<script src="../../app/cost/js/expensesDistribution/configExpensesDistribution.js"></script>
<script src="../../app/cost/js/expensesDistribution/expensesDistribution.js"></script>
<script src="../../app/cost/js/import/import.js"></script>
<script src="../../app/cost/js/expensesDistribution/importExpensesDistribution.js"></script>
<script src="../../app/cost/js/import/file.js"></script>