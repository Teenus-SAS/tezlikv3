<?php

use tezlikv2\dao\UserInactiveTimeDao;

require_once(dirname(dirname(dirname(dirname(__DIR__)))) . "../../api/src/dao/app/cost/login/UserInactiveTimeDao.php");
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>
<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Carga Fabril</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Asignación de costos directos relacionados a una máquina</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnNewFactoryLoad">Nueva Carga Fabril Máquina</button>
                    <button class="btn btn-info ml-3" id="btnImportNewFactoryLoad">Importar Carga Fabril Máquinas</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardFactoryLoad">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formNewFactoryLoad">
                            <div class="gridx4cm">
                                <label for="">Máquina</label>
                                <label for="">Descripción Carga fabril</label>
                                <label for="">Costo</label>
                                <!-- <label for="">Valor Minuto</label> -->
                                <label for=""></label>
                                <select class="form-control" name="idMachine" id="idMachine"></select>
                                <input class="form-control" name="descriptionFactoryLoad" id="descriptionFactoryLoad" />
                                <input class="form-control text-center number" type="text" name="costFactory" id="costFactory" />
                                <!-- <input class="form-control text-center number" type="text" name="costMinute" id="costMinute" /> -->
                                <button class="btn btn-primary" id="btnCreateFactoryLoad">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardImportFactoryLoad">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportFactoryLoad" enctype="multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="fileFactoryLoad" accept=".xls,.xlsx">
                                    <label for="formFile" class="form-label"> Importar Carga Fabril Máquinas</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportFactoryLoad">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsFactoryLoad">Descarga Formato</button>
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
                            <table class="table table-striped" id="tblFactoryLoad">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../app/cost/js/global/number.js"></script>
<script src="../../app/cost/js/machines/configMachines.js"></script>
<script src="../../app/cost/js/factoryLoad/tblFactoryLoad.js"></script>
<script src="../../app/cost/js/factoryLoad/factoryLoad.js"></script>
<script src="../../app/cost/js/import/import.js"></script>
<script src="../../app/cost/js/factoryLoad/importFactoryLoad.js"></script>
<script src="../../app/cost/js/import/file.js"></script>