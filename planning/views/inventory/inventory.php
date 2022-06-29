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
                    <select class="form-control" name="category" id="category">
                        <option value="" selected disabled>Categorias</option>
                        <option value="1">Productos</option>
                        <option value="2">Materiales</option>
                        <option value="3">Insumos</option>
                        <option value="4">Todos</option>
                    </select>
                    <!-- <button class="btn btn-warning" id="btnNewInventory" name="btnNewInventory">Nuevo Proceso</button> -->
                    <button class="btn btn-info ml-3" id="btnImportNewInventory">Importar Inventarios</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <div class=" page-content-wrapper mt--45 mb-5 cardCreateInventory">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <form id="formCreateInventory">
                        <div class=" card-body">
                            <div class="gridx2p">
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <label for="">Proceso</label>
                                    <input type="text" class="form-control" id="Inventory" name=" Inventory">
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:4px">
                                    <button class="btn btn-success" id="btnCreateInventory">Crear Proceso</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> -->

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
                                <div class=" form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
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
<div class=" page-content-wrapper mt--45">
    <div class="container-fluid">
        <!-- <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"></h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- Row 5 -->
        <div class="row">
            <div class="col-12 cardTableInvProducts">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Inventario Productos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblInvProducts">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 cardTableInvMaterials">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Inventario Materiales</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblInvMaterials">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 cardTableInvSupplies">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Inventario Insumos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblInvSupplies">

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