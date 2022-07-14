<?php

use tezlikv3\dao\UserInactiveTimeDao;

require_once(dirname(dirname(dirname(__DIR__))) . "../api/src/dao/app/global/login/UserInactiveTimeDao.php");
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>
<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Productos</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Asignación de procesos al producto</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnCreateProcess">Nuevo Proceso</button>
                    <button class="btn btn-info ml-3" id="btnImportNewProductProcess">Importar Procesos</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardCreateRawMaterials">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="gridx2">
                            <label for="">Referencia</label>
                            <label for="">Producto</label>
                            <select class="form-control" name="refProduct" id="refProduct"></select>
                            <select class="form-control" name="selectNameProduct" id="selectNameProduct"></select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="page-content-wrapper mt--45 mb-5 cardAddProcess">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formAddProcess">
                            <div class="gridx6pp">
                                <label for="">Proceso</label>
                                <label for="">Maquina</label>
                                <label for="" class="text-center">t.alistamiento (min)</label>
                                <label for="" class="text-center">t.operacion (min)</label>
                                <label for="" class="text-center">t.total (min)</label>
                                <label for=""></label>
                                <select class="form-control" name="idProcess" id="idProcess"></select>
                                <select class="form-control" name="idMachine" id="idMachine"></select>
                                <input class="form-control text-center" type="text" name="enlistmentTime" id="enlistmentTime">
                                <input class="form-control text-center" type="text" name="operationTime" id="operationTime">
                                <input class="form-control text-center" type="text" name="totalTime" id="totalTime" disabled>
                                <button class="btn btn-success" id="btnAddProcess">Adicionar</button>
                            </div>
                        </form>
                        <div class="alert alert-warning mt-3" role="alert">
                            Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardImportProductsProcess">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportProductProcess" enctype="multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="fileProductsProcess" accept=".xls,.xlsx">
                                    <label for="formFile" class="form-label"> Importar Productos*Procesos</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportProductsProcess">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsProductsProcess">Descarga Formato</button>
                                </div>
                            </div>
                            <div class="alert alert-warning" role="alert">
                                Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto.
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
                            <table class="table table-striped text-center" id="tblConfigProcess" name="tblConfigProcess">
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Total:</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/cost/js/config/productProcess/tblConfigProcess.js"></script>
<script src="/cost/js/basic/products/configProducts.js"></script>
<script src="/cost/js/general/payroll/configProcessPayroll.js"></script>
<script src="/cost/js/basic/machines/configMachines.js"></script>
<script src="/cost/js/config/productProcess/productProcess.js"></script>
<script src="../global/js/import/import.js"></script>
<script src="/cost/js/config/productProcess/importProductProcess.js"></script>
<script src="../global/js/import/file.js"></script>