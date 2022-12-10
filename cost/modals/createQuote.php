<div class="modal fade" id="modalCreateQuote" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleNewContact"><i class="lni lni-notepad"></i> Nueva Cotización</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formNewQuote">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Cliente</b></label>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select class="form-control" id="company" name="company"> </select>
                                                <label for="company" class="form-label">Empresa <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select class="form-control" id="contacts" name="contacts"></select>
                                                <label for="contacts" class="form-label">Contacto <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Condiciones Comerciales</b></label>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select class="form-control" name="idPaymentMethod" id="idPayment"></select>
                                                <label for="idPayment" class="form-label">Condiciones de Pago <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input type="text" class="form-control" name="offerValidity" id="offerValidity">
                                                <label for="offerValidity" class="form-label">Validez de la Oferta <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input type="text" class="form-control" name="warranty" id="warranty">
                                                <label for="warranty" class="form-label">Garantia del Producto <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-2">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input type="date" class="form-control" name="deliveryDate" id="deliveryDate" min="<?php echo date("Y-m-d"); ?>">
                                                <label for="deliveryDate" class="form-label">Fecha de Entrega</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <textarea class="form-control" id="observation" rows="4"></textarea>
                                                <label for="observation" class="form-label">Observaciones</label>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Productos</b></label>
                                        </div>
                                        <div class="col-12 col-lg-12">
                                            <button class="btn btn-warning mb-4" id="btnAddNewProduct">Seleccionar Productos</button>
                                        </div>

                                        <div class="addProd row px-3">
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group floating-label enable-floating-label show-label">
                                                    <select class="form-control" id="refProduct" name="idProduct"></select>
                                                    <label for="refProduct" class="form-label">Referencia <span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-8">
                                                <div class="form-group floating-label enable-floating-label show-label">
                                                    <select class="form-control" id="selectNameProduct" name="idProduct"></select>
                                                    <label for="selectNameProduct" class="form-label">Producto <span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-3">
                                                <div class="form-group floating-label enable-floating-label show-label">
                                                    <input class="form-control number text-center calcPrice" type="text" name="quantity" id="quantity">
                                                    <label for="quantity" class="form-label">Cantidad <span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-3">
                                                <div class="form-group floating-label enable-floating-label show-label">
                                                    <input class="form-control number text-center calcPrice" type="text" name="price" id="price">
                                                    <label for="prices" class="form-label">Precio Unitario <span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-3">
                                                <div class="form-group floating-label enable-floating-label show-label calcPrice">
                                                    <select name="discount" id="discount" class="form-control">
                                                        <option value="0">0%</option>
                                                        <option value="1">1%</option>
                                                        <option value="2">2%</option>
                                                        <option value="3">3%</option>
                                                        <option value="4">4%</option>
                                                        <option value="5">5%</option>
                                                        <option value="6">6%</option>
                                                        <option value="7">7%</option>
                                                        <option value="8">8%</option>
                                                        <option value="9">9%</option>
                                                        <option value="10">10%</option>
                                                    </select>
                                                    <label for="prices" class="form-label">Descuento <span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-3">
                                                <div class="form-group floating-label enable-floating-label show-label">
                                                    <input class="form-control text-center" type="text" name="totalPrice" id="totalPrice" readonly>
                                                    <label for="prices" class="form-label">Precio Total <span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-6">
                                                <img src="" id="imgProduct" style="width:20%">
                                            </div>
                                            <div class="col-12 col-lg-12">
                                                <button class="btn btn-warning mb-4" id="btnAddProduct">Adicionar producto</button>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-12 col-lg-12">
                                            <div class="card mt-4">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Referencia</th>
                                                                    <th class="text-center">Producto</th>
                                                                    <th class="text-center">Cantidad</th>
                                                                    <th class="text-center">Valor Unitario</th>
                                                                    <th class="text-center">Descuento</th>
                                                                    <th class="text-center">Valor Total</th>
                                                                    <th class="text-center">Acciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tableProductsQuoteBody">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseQuote">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnSaveQuote">Crear</button>
            </div>
        </div>
    </div>
</div>
</div>