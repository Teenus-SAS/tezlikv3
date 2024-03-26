<div class="modal fade" id="createInactivesProducts" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Productos Inactivos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tblInactiveProducts">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Referencia</th>
                                                <th>Producto</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tblInactiveProductsBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseInactivesProducts">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnActivesProducts">Activar</button>
            </div>
        </div>
    </div>
</div>