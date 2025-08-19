<div class="modal fade" id="modalImportMultiproducts" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Importar Productos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12">
                                <div class="d-flex flex-row-reverse">
                                    <div class="p-2 floating-label enable-floating-label show-label form-inline">
                                        <input type="number" class="form-control text-center" id="importExpense">
                                        <label>Gastos</label>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped text-center" id="tblImportMultiproducts">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Referencia</th>
                                                <th>Producto</th>
                                                <th>Unidades Vendidas</th>
                                            </tr>
                                        </thead>
                                        <tbody id="multiproductsImportBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseImportProducts">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnImportProducts"><i class="fas fa-sync-alt"></i> Importar</button>
            </div>
        </div>
    </div>
</div>