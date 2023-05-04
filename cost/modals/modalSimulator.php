<div class="modal fade come-from-modal right" id="modalSimulator" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Simulador</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt-2">
                    <div class="container-fluid">
                        <div class="form-row cardGeneralBtnsSimulator">
                            <div class="pos-f-t">
                                <div class="collapse" id="navbarToggleExternalContent">
                                    <div class="bg-light p-4">
                                        <!-- <div class="col-sm-4"> -->
                                        <button type="button" class="btn btn-outline-secondary" value="1"> Productos</button>
                                        <!-- </div> -->
                                        <!-- <div class="col-sm-4"> -->
                                        <button type="button" class="btn btn-outline-secondary" value="2"> Maquinas</button>
                                        <!-- </div> -->
                                        <!-- <div class="col-sm-4"> -->
                                        <button type="button" class="btn btn-outline-secondary" value="3"> Materias Prima</button>
                                        <!-- </div> -->
                                        <!-- <div class="col-sm-6"> -->
                                        <button type="button" class="btn btn-outline-secondary" value="4"> F. Tecnica Materia Prima</button>
                                        <!-- </div> -->
                                        <!-- <div class="col-sm-6 mb-4"> -->
                                        <button type="button" class="btn btn-outline-secondary" value="5"> F. Tecnica Procesos</button>
                                        <!-- </div> -->
                                        <!-- <div class="col-sm-6"> -->
                                        <button type="button" class="btn btn-outline-secondary" value="6"> Carga Fabril</button>
                                        <!-- </div> -->
                                        <!-- <div class="col-sm-6 mb-4"> -->
                                        <button type="button" class="btn btn-outline-secondary" value="7"> Servicios Externos</button>
                                        <!-- </div> -->
                                        <!-- <div class="col-sm-6"> -->
                                        <button type="button" class="btn btn-outline-secondary" value="8"> Nomina</button>
                                        <!-- </div> -->
                                        <!-- <div class="col-sm-6 mb-4"> -->
                                        <button type="button" class="btn btn-outline-secondary" value="9"> Distribucion de Gastos</button>
                                        <!-- </div> -->
                                        <!-- <div class="col-sm"> -->
                                        <button type="button" class="btn btn-outline-secondary" value="10"> Recuperacion Gastos</button>
                                        <!-- </div> -->
                                    </div>
                                </div>
                                <nav class="navbar navbar-light bg-light">
                                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
                                        <span class="navbar-toggler-icon"></span>
                                    </button>
                                </nav>
                            </div>
                        </div>

                        <div class="col-md-12 col-lg-12 cardTableSimulator">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Referencia</th>
                                            <th>Costo</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tblInactiveProductsBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>