<?php

use tezlikv3\dao\UserInactiveTimeDao;

require_once(dirname(dirname(dirname(dirname(__DIR__)))) . "../api/src/dao/app/global/login/UserInactiveTimeDao.php");
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
                                <div class="col-md-3 mb-3 programmingSelect" id="machines">
                                    <label for="">Maquina</label>
                                    <select class="form-control" id="idMachine" name="idMachine">
                                    </select>
                                </div>
                                <!-- </div>
                            <div class="form-row"> -->
                                <div class="col-md-2 mb-3 programmingSelect" id="orders">
                                    <label for="">Pedido</label>
                                    <select class="form-control" id="order" name="order">
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3 programmingSelect" id="products">
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
<script src="../planning/js/program/programming/tblProgramming.js"></script>
<!-- <script src="/planning/js/orders/configOrders.js"></script>
<script src="/planning/js/basic/machines/configMachines.js"></script>
<script src="/planning/js/basic/products/configProducts.js"></script> -->
<script src="/planning/js/program/programming/programming.js"></script>
<script src="/planning/js/program/programming/configProgramming.js"></script>
<script src="../global/js/global/validateExt.js"></script>