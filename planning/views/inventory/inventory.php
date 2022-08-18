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
                    <h3 class="mb-1 font-weight-bold text-dark">Inventarios</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Análisis de inventario</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <select class="form-control" name="category" id="category" disabled>
                        <option value="" selected="" disabled="">Categorias</option>
                        <option value="1">Productos</option>
                        <option value="2">Materiales</option>
                        <option value="3">Insumos</option>
                        <option value="4">Todos</option>
                    </select>
                    <button class="btn btn-info ml-3" id="btnImportNewInventory">Importar Inventarios</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardAddMonths">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <form id="formAddMonths">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-md-3" style="margin-bottom:0px">
                                    <label for="">Meses a Analizar:</label>
                                    <input type="number" class="form-control text-center" id="cantMonths" name="cantMonths" style="width:200px">
                                </div>
                                <div class="col" style="margin-bottom:0px;margin-top:33px">
                                    <button class="btn btn-success" id="btnAddMonths">Calcular</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class=" page-content-wrapper mt--45 mb-5 cardImportInventory">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportInventory" enctype=" multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="fileInventory" accept=" .xls,.xlsx">
                                    <label for="formFile" class="form-label"> Importar Inventario</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportInventory">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsInventory">Descarga Formato</button>
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
                    <div class="card-header">
                        <div class="row justify-content-around">
                            <div class="col">
                                <h5 class="card-title">Inventarios</h5>
                            </div>
                            <div class="col-lg-2 cardBtnAddMonths">
                                <button class="btn btn-warning" id="btnInvetoryABC" name="btnInvetoryABC">Reclasificación Inventarios</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblInventories">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../planning/js/inventory/tblInventory.js"></script>
<script src="../planning/js/inventory/inventory.js"></script>
<script src="../global/js/import/import.js"></script>
<script src="../planning/js/inventory/importInventory.js"></script>
<script src="../global/js/import/file.js"></script>