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
                    <h3 class="mb-1 font-weight-bold text-dark">Productos</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Creación de Productos</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-4 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnNewProduct">Nuevo Producto</button>
                    <button class="btn btn-info ml-3" id="btnImportNewProducts">Importar Productos</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardCreateProduct">
    <div class="container-fluid">
        <div class="row">
            <form id="formCreateProduct">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="gridx2">
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <!-- <input type="text" class="form-control" name="idProduct" id="idProduct" hidden> -->
                                    <input type="text" class="form-control" name="referenceProduct" id="referenceProduct">
                                    <label for="">Referencia</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <input type="text" class="form-control" name="product" id="product">
                                    <label for="">Nombre Producto</label>
                                </div>
                            </div>
                            <!-- </form> -->
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <!-- <form id="formCreateProduct"> -->
                            <div class="gridx4rp">
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <select class="form-control" name="idMold" id="idMold"></select>
                                    <label for="">Molde</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label " style="margin-bottom:0px">
                                    <input type="text" class="form-control text-center number" id="quantity" name="quantity">
                                    <label for="">Cantidad en inventario</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <!-- <input class="form-control form-control-sm" id="formFile" type="file" style="padding:10px;width:40%"> -->
                                    <input class="form-control" type="file" id="formFile">
                                    <!-- <button class="btn btn-warning" disabled>Seleccionar Imagen</button> -->
                                    <label for="formFile" class="form-label"> Cargar imagen producto</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnCreateProduct">Crear Producto</button>
                                </div>
                            </div>

                            <div id="preview" class="col-md-6 offset-md-8"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardImportProducts">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportProduct" enctype="multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="fileProducts" accept=".xls,.xlsx">
                                    <label for="formFile" class="form-label"> Importar Productos</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportProducts">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsProducts">Descarga Formato</button>
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
                        <h5 class="card-title">Productos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblProducts">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/global/js/global/number.js"></script>
<script src="../planning/js/products/tblProducts.js"></script>
<script src="/planning/js/invMold/configInvMold.js"></script>
<script src="../planning/js/products/products.js"></script>
<script src="../global/js/import/import.js"></script>
<script src="../planning/js/products/importProducts.js"></script>
<script src="../global/js/import/file.js"></script>
<script src="../global/js/global/validateImgExt.js"></script>