<div class="modal fade" id="modalCreateQuote" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleNewContact"><i class="lni lni-notepad"></i> Nueva Cotización</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="p-4 border rounded">
                    <form class="row g-3 needs-validation" id="formNewQuote" novalidate>
                        <!-- <div class="col-md-4" hidden>
                            <label for="id_quote" class="form-label">id_quote</label>
                            <input type="text" class="form-control" id="id_quote" name="refProduct">
                        </div> -->
                        <h5 style="margin-bottom: 0px;"><b>Cliente</b></h5>
                        <hr style="margin-bottom:0px">
                        <div class="col-md-4">
                            <label for="company" class="form-label">Empresa</label>
                            <select class="form-select" id="company" name="company" required> </select>
                        </div>
                        <div class="col-md-4">
                            <label for="contacts" class="form-label">Contacto</label>
                            <select class="form-select" id="contacts" name="contacts" required></select>
                        </div>

                        <hr style="margin-bottom:0px">
                        <h5 style="margin-bottom: 0px;"><b>Condiciones Comerciales</b></h5>
                        <hr style="margin-bottom:0px">
                        <div class="col-md-3">
                            <label for="idPayment" class="form-label">Condiciones de Pago</label>
                            <select class="form-select" name="idPaymentMethod" id="idPayment"></select>
                        </div>
                        <div class="col-md-3">
                            <label for="time_quote" class="form-label">Validez de la Oferta</label>
                            <input type="text" class="form-control" name="time_quote" id="time_quote">
                        </div>
                        <div class="col-md-3">
                            <label for="guarantee" class="form-label">Garantia del Producto</label>
                            <input type="text" class="form-control" name="guarantee" id="guarantee">
                        </div>
                        <div class="col-md-3">
                            <label for="delivery_date" class="form-label">Fecha de Entrega</label>
                            <input type="date" class="form-control" name="delivery_date" id="delivery_date">
                        </div>

                        <hr style="margin-bottom:0px">
                        <h5 style="margin-bottom: 0px;"><b>Productos</b></h5>
                        <!-- <hr style="margin-bottom:0px"> -->
                        <div>
                            <button class="btn btn-primary" id="btnAddNewProduct">Seleccionar Productos</button>
                        </div>

                        <div class="addProd row">
                            <div class="col-md-4 mt-2">
                                <label for="refProduct" class="form-label">Referencia</label>
                                <select class="form-select" id="refProduct" name="reference" required></select>
                            </div>
                            <div class="col-md-12 mt-1">
                                <label for="selectNameProduct" class="form-label">Producto</label>
                                <select class="form-select" id="selectNameProduct" name="idProduct" required></select>
                            </div>
                            <div class="col-md-2 mt-1">
                                <label for="quantity" class="form-label">Cantidad</label>
                                <input class="form-control text-center" type="number" name="quantity" id="quantity">
                            </div>
                            <div class="col-md-3 mt-1">
                                <label for="prices" class="form-label">Precio Unitario</label>
                                <input class="form-control text-center" type="text" name="price" id="price">
                            </div>
                            <div class="col-md-2 mt-1">
                                <label for="prices" class="form-label">Descuento</label>
                                <select name="discount" id="discount" class="form-select">
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
                            </div>
                            <div class="col-md-3 mt-1">
                                <label for="prices" class="form-label">Precio Total</label>
                                <input class="form-control text-center" type="text" name="totalPrice" id="totalPrice" readonly>
                            </div>
                            <div class="col-md-3 mt-1">
                                <img src="" width="30%" id="imgProduct">
                            </div>
                            <div class="col-md-12 mt-1">
                                <label for="selectContact" class="form-label">Descripción</label>
                                <textarea class="form-control" name="descriptionProduct" id="descriptionProduct" cols="30" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary mt-3" id="btnAddProduct">Adicionar producto</button>
                            </div>
                            <hr />
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tableProductsQuote" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Referencia</th>
                                                <th class="text-center">Producto</th>
                                                <th class="text-center">Descripción</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-center">Valor Unitario</th>
                                                <th class="text-center">Descuento</th>
                                                <th class="text-center">Valor Total</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3" style="display: flex;justify-content:end">
                            <button class="btn btn-primary" type="submit" id="btnSaveQuote">Crear Cotización</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>