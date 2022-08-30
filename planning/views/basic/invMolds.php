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
                    <h3 class="mb-1 font-weight-bold text-dark">Moldes</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Inventario de Moldes</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnNewInvMold" name="btnNewInvMold">Nuevo Molde</button>
                    <button class="btn btn-info ml-3" id="btnImportNewInvMold">Importar Moldes</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardCreateInvMold">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <form id="formCreateInvMold">
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col">
                                    <label for="">Referencia</label>
                                    <input type="text" class="form-control" id="referenceMold" name="referenceMold">
                                </div>
                                <div class="col-6">
                                    <label for="">Nombre Molde</label>
                                    <input type="text" class="form-control" id="mold" name="mold">
                                </div>
                                <div class="col">
                                    <label for="">T. Montaje Producción (min)</label>
                                    <input type="text" class="form-control number text-center" id="assemblyProduction" name="assemblyProduction">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="">T. Montaje (hrs)</label>
                                    <input type="text" class="form-control number text-center" id="assemblyTime" name="assemblyTime">
                                </div>
                                <div class="col">
                                    <label for="">Numero de cavidades</label>
                                    <input type="text" class="form-control number text-center" id="cavity" name="cavity">
                                </div>
                                <div class="col">
                                    <label for="">Numero de cavidades disponibles</label>
                                    <input type="text" class="form-control number text-center" id="cavityAvailable" name="cavityAvailable">
                                </div>
                                <div class="col.lg-2">
                                    <button class="btn btn-success" id="btnCreateInvMold" style="width: 100px;height:50%; margin-top: 32px; margin-left: 9px; margin-right: 9px">Crear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardImportInvMold">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportInvMold" enctype="multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="fileInvMold" accept=".xls,.xlsx">
                                    <label for="formFile" class="form-label"> Importar Molde</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportInvMold">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsInvMold">Descarga Formato</button>
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
                        <h5 class="card-title">Moldes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblInvMold">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/global/js/global/number.js"></script>
<script src="/planning/js/basic/invMold/tblInvMold.js"></script>
<script src="/planning/js/basic/invMold/invMold.js"></script>
<script src="../global/js/import/import.js"></script>
<script src="/planning/js/basic/invMold/importInvMold.js"></script>
<script src="../global/js/import/file.js"></script>