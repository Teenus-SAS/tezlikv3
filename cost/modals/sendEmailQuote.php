<div class="modal fade" id="modalSendEmail" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Enviar Cotización</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12">
                                <form id="formSendMail">
                                    <div class="form-group mb-2">
                                        <input type="email" class="form-control" placeholder="Para" id="toHeader" name="toHeader" />
                                    </div>
                                    <div class="form-group mb-2">
                                        <input type="email" class="form-control" placeholder="CC" id="ccHeader" name="ccHeader" />
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Asunto" id="subject" name="subject" />
                                    </div>
                                    <div class="form-group">
                                        <div class="message" id="compose-editor" name="message">Hey</div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseSendEmail">Cerrar</button>
                <button class="btn btn-primary chat-send-btn" data-effect="wave" id="btnSend">
                    <span class="d-none d-sm-inline-block mr-2 align-middle">Enviar</span>
                    <i class="bx bxs-send fs-sm align-middle"></i>
                </button>
            </div>
        </div>
    </div>
</div>