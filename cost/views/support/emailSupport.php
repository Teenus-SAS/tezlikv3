<?php

use tezlikv3\dao\UserInactiveTimeDao;

require_once dirname(dirname(dirname(__DIR__))) . "../api/src/dao/app/global/login/UserInactiveTimeDao.php";
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>

<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Soporte</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Escribanos, que estaremos muy atentos de atender sus requerimientos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45">
    <div class="container-fluid">
        <div class="row align-items-stretch">
            <div class="col-md-8 col-lg-12">
                <div class="inbox-rightbar card">
                    <div class="card-body">
                        <form id="formSendSupport">
                            <div class="form-group mb-2">
                                <input type="email" class="form-control" placeholder="Para" value="soporte@teenus.com.co" readonly />
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

                            <div class="form-group pt-2">
                                <div class="text-right">
                                    <button class="btn btn-success mr-2" data-effect="wave" id="btnDraft">
                                        <i class="bx bxs-edit-alt fs-sm align-middle"></i>
                                        <span class="d-none d-sm-inline-block mr-2 align-middle">Borrador</span>
                                    </button>
                                    <button class="btn btn-primary chat-send-btn" data-effect="wave" id="btnSend">
                                        <span class="d-none d-sm-inline-block mr-2 align-middle">Enviar</span>
                                        <i class="bx bxs-send fs-sm align-middle"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/libs/ckeditor/js/ckeditor.min.js"></script>
<script src="/assets/js/pages/compose-mail.init.js"></script>
<script src="/assets/js/app.js"></script>
<script src="../cost/js/support/support.js"></script>