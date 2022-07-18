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
                    <button class="btn btn-warning" id="btnNewProgramming" name="btnNewProgramming">Programar</button>
                    <button class="btn btn-info ml-3" id="btnImportNewProgramming" name="btnNewImportProgramming">Importar Programación</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardCreateProgramming">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formCreateProgramming">
                            <div class="form-row">
                                <!-- <div class="col-md-3 mb-3">
                                    <label for="refRawProgramming">Maquina</label>
                                    <input type="text" class="form-control" id="refRawProgramming" name="refRawProgramming">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nameRawProgramming">Nombre Materia Prima</label>
                                    <input type="text" class="form-control" id="nameRawProgramming" name="nameRawProgramming">
                                </div> -->
                                <div class="col-md-3 mb-3">
                                    <label for="">Maquina</label>
                                    <select class="form-control" id="idMachine" name="idMachine">
                                        <!-- <option value="" selected disabled>Seleccionar</option>
                                        <option value="1">Iny 90f02</option>
                                        <option value="2">SKINPACK</option> -->
                                    </select>
                                </div>
                                <!-- </div>
                            <div class="form-row"> -->
                                <div class="col-md-2 mb-3">
                                    <label for="">Pedido</label>
                                    <select class="form-control" id="order" name="order">
                                        <!-- <option value="" selected disabled>Seleccionar</option>
                                        <option value="1">100014</option>
                                        <option value="2">100015</option> -->
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="">Producto</label>
                                    <select class="form-control" id="selectNameProduct" name="idProduct"></select>
                                    <select class="form-control" id="refProduct" style="display:none"></select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="">Cantidad</label>
                                    <input type="text" class="form-control text-center number" id="quantity" name="quantity">
                                </div>

                                <button class="btn btn-info" type="submit" id="btnCreateProgramming" name="btnCreateProgramming" style="width: 100px;height:50%; margin-top: 34px; margin-left: 20px">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardImportProgramming">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportProgramming" enctype="multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="fileProgramming" accept=".xls,.xlsx">
                                    <label for="formFile" class="form-label">Importar Programación</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportProgramming">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsProgramming">Descarga Formato</button>
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
                        <h5 class="card-title">Programación</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblProgramming">
                                <thead>
                                    <tr>
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
                                <tbody class="colProgramming">
                                    <!-- <tr draggable="true" ondragstart="dragit(event)" ondragover="dragover(event)">
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
                                    <tr draggable="true" ondragstart="dragit(event)" ondragover="dragover(event)">
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
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- <style>
    td {
        cursor: move;
    }
</style> -->

<script src="/global/js/global/number.js"></script>
<script src="../planning/js/programming/tblProgramming.js"></script>
<script src="/planning/js/basic/machines/configMachines.js"></script>
<script src="/planning/js/orders/configOrders.js"></script>
<script src="/planning/js/basic/products/configProducts.js"></script>
<script src="/planning/js/programming/programming.js"></script>
<script src="../global/js/import/import.js"></script>
<script src="../planning/js/programming/importProgramming.js"></script>
<script src="../global/js/import/file.js"></script>
<script src="../global/js/global/validateExt.js"></script>