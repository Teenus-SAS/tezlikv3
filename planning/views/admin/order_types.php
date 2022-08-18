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
                    <h3 class="mb-1 font-weight-bold text-dark">Tipos de Pedidos Maquina</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Planeación de Tipos de Pedidos Máquinas</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnNewOrderType" name="btnNewOrderType">Nuevo Tipo Pedido</button>
                    <button class="btn btn-info ml-3" id="btnImportNewOrderTypes" name="btnImportNewOrderTypes">Importar Tipos de Pedidos</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardCreateOrderType">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formCreateOrderType">
                            <div class="gridx2p">
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <label for="">Tipo de Pedido</label>
                                    <input type="text" class="form-control" name="orderType" id="orderType">
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:4px">
                                    <button class="btn btn-success" id="btnCreateOrderType">Crear Tipo Pedido</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardImportOrderTypes">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportOrderTypes" enctype="multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="fileOrderTypes" accept=".xls,.xlsx">
                                    <label for="formFile" class="form-label"> Importar Tipos de Pedidos</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportOrderTypes">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsOrderTypes">Descarga Formato</button>
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
                        <h5 class="card-title">Tipos de Pedidos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblOrderTypes">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/planning/js/admin/orderTypes/tblOrderTypes.js"></script>
<script src="/planning/js/admin/orderTypes/orderTypes.js"></script>
<script src="/global/js/import/file.js"></script>
<script src="/global/js/import/import.js"></script>
<script src="/planning/js/admin/orderTypes/importOrderTypes.js"></script>