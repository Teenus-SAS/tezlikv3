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
                    <h3 class="mb-1 font-weight-bold text-dark">Clientes</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Creación de Clientes</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnNewClient" name="btnNewClient">Nuevo Cliente</button>
                    <button class="btn btn-info ml-3" id="btnImportNewClient">Importar Clientes</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardCreateClient">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formCreateClient">
                            <div class="row">
                                <div class="form-group floating-label enable-floating-label show-label col" style="margin-bottom:0px">
                                    <label for="">Ean Cliente</label>
                                    <input type="text" class="form-control text-center number" id="ean" name="ean">
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label col" style="margin-bottom:0px">
                                    <label for="">Nit Cliente</label>
                                    <input type="text" class="form-control text-center number" id="nit" name="nit">
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label col-lg-6" style="margin-bottom:0px">
                                    <label for="">Nombre Cliente</label>
                                    <input type="text" class="form-control text-center" id="client" name="client">
                                </div>
                                <div style="margin-bottom:0px;margin-top:5px;" class="col">
                                    <button class="btn btn-success" id="btnCreateClient">Crear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardImportClients">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportClients" enctype="multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="fileClients" accept=".xls,.xlsx">
                                    <label for="formFile" class="form-label">Importar Clientes</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportClients">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsClients">Descarga Formato</button>
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
                        <h5 class="card-title">Clientes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblClients">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/global/js/global/number.js"></script>
<script src="/planning/js/admin/clients/tblClients.js"></script>
<script src="/planning/js/admin/clients/clients.js"></script>
<script src="../global/js/import/import.js"></script>
<script src="/planning/js/admin/clients/importClients.js"></script>
<script src="../global/js/import/file.js"></script>