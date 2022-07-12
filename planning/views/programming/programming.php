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
                    <h3 class="mb-1 font-weight-bold text-dark">Programa de Producción</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active"></li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnNewMaterial" name="btnNewMaterial">Programar</button>
                    <!-- <button class="btn btn-info ml-3" id="btnImportNewMaterials" name="btnNewImportMaterials">Importar Materias Primas</button> -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardRawMaterials">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formCreateMaterial">
                            <div class="form-row">
                                <!-- <div class="col-md-3 mb-3">
                                    <label for="refRawMaterial">Maquina</label>
                                    <input type="text" class="form-control" id="refRawMaterial" name="refRawMaterial">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nameRawMaterial">Nombre Materia Prima</label>
                                    <input type="text" class="form-control" id="nameRawMaterial" name="nameRawMaterial">
                                </div> -->
                                <div class="col-md-3 mb-3">
                                    <label for="validationCustom02">Maquina</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <option value="1">Iny 90f02</option>
                                        <option value="2">SKINPACK</option>
                                    </select>
                                </div>
                                <!-- </div>
                            <div class="form-row"> -->
                                <div class="col-md-2 mb-3">
                                    <label for="validationCustom02">Pedido</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <option value="1">100014</option>
                                        <option value="2">100015</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="validationCustom02">Producto</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <option value="1">Porta papel cocina con rollo</option>
                                        <option value="2">Porta papel cocina 2</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="validationCustom02">Cantidad</label>
                                    <input class="form-control" id="category" name="category">
                                </div>

                                <button class="btn btn-info" type="submit" id="btnCreateMaterial" name="btnCreateMaterial" style="width: 100px;height:50%; margin-top: 34px; margin-left: 20px">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardImportMaterials">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportMaterials" enctype="multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="fileMaterials" accept=".xls,.xlsx">
                                    <label for="formFile" class="form-label">Importar Materia Prima</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportMaterials">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsMaterials">Descarga Formato</button>
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
                        <h5 class="card-title">Materias Primas</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblRawMaterials">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Pedido</th>
                                        <th scope="col">Referencia</th>
                                        <th scope="col">Producto</th>
                                        <th scope="col">Cant.Pedido</th>
                                        <th scope="col">Cant.Pendiente</th>
                                        <th scope="col">Cant.Realizar</th>
                                        <th scope="col">Cliente</th>
                                        <th scope="col">Lote Economico</th>
                                        <th scope="col">F.Inicio</th>
                                        <th scope="col">F.Final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">1</th>
                                        <td>100014</td>
                                        <td>PPCR</td>
                                        <td>Porta papel cocina con rollo</td>
                                        <td>3.000</td>
                                        <td>2.000</td>
                                        <td>500</td>
                                        <td>Alkosto</td>
                                        <td>1.700</td>
                                        <td>11/07/2020</td>
                                        <td>12/07/2020</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">2</th>
                                        <td>100015</td>
                                        <td>PPC2</td>
                                        <td>Porta papel cocina 2</td>
                                        <td>5.300</td>
                                        <td>2.000</td>
                                        <td>1.300</td>
                                        <td>Easy</td>
                                        <td>1.000</td>
                                        <td>12/07/2020</td>
                                        <td>13/07/2020</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <script src="/global/js/global/number.js"></script> -->
<script src="../planning/js/programming/tblProgramming.js"></script>
<script src="../planning/js/rawMaterials/rawMaterials.js"></script>
<script src="../global/js/import/import.js"></script>
<script src="../planning/js/rawMaterials/importRawMaterials.js"></script>
<script src="../global/js/import/file.js"></script>
<script src="../global/js/global/validateExt.js"></script>