<div class="container">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-12">
                    <!-- Page title -->
                    <div class="my-4">
                        <h3>Mi Perfil</h3>
                        <hr>
                    </div>
                    <div class="row mb-5 gx-5">
                        <form id="formSaveProfile">
                            <div class="col-xxl-12 mb-5 mb-xxl-0">
                                <div class="bg-secondary-soft px-4 py-2 rounded">
                                    <div class="row g-3">
                                        <input type="" id="idUser" name="idUser" hidden>
                                        <div class="col-md-4">
                                            <label class="form-label">Nombres *</label>
                                            <input type="text" class="form-control" placeholder="" aria-label="First name" id="firstname" name="nameUser">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Apellidos *</label>
                                            <input type="text" class="form-control" placeholder="" aria-label="Last name" id="lastname" name="lastnameUser">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Cargo *</label>
                                            <input type="text" class="form-control" placeholder="" aria-label="Position" id="position" name="position" disabled>
                                        </div>
                                        <div class="col-md-4 mt-4">
                                            <label for="email" class="form-label">Email *</label>
                                            <input type="email" class="form-control" id="email" name="emailUser">
                                        </div>
                                        <!-- <div class="col-md-4 mt-4">
                                            <label class="form-label">Contraseña</label>
                                            <input type="password" class="form-control" placeholder="" aria-label="Password" id="password" name="password">
                                        </div>
                                        <div class="col-md-4 mt-4">
                                            <label class="form-label">Confirmar Contraseña</label>
                                            <input type="password" class="form-control" placeholder="" aria-label="Confirm Password" id="conPassword" name="conPassword">
                                        </div> -->
                                    </div> <!-- Row END -->
                                </div>
                            </div>
                            <hr>
                            <!-- Upload profile -->
                            <div class="col-xxl-4">
                                <div class="bg-secondary-soft px-4 py-2 rounded">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="Image" class="form-label">Ingrese su foto</label>
                                            <input class="form-control" type="file" id="formFile">
                                        </div>
                                        <div class="col-2">
                                            <button class="btn btn-light" style="margin-top:33px" id="clearImg">Limpiar</button>
                                        </div>
                                        <div class="col-4">
                                            <img id="avatar" src="" class="img-fluid" style="width: 100px;" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div> <!-- Row END -->

                    <!-- Social media detail
                <div class="row mb-5 gx-5">
                    <div class="col-xxl-6">
                        <div class="bg-secondary-soft px-4 py-5 rounded">
                            <div class="row g-3">
                                <h4 class="my-4">Change Password</h4>
                                <div class="col-md-6">
                                    <label for="exampleInputPassword1" class="form-label">Old password *</label>
                                    <input type="password" class="form-control" id="exampleInputPassword1">
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputPassword2" class="form-label">New password *</label>
                                    <input type="password" class="form-control" id="exampleInputPassword2">
                                </div>
                                <div class="col-md-12">
                                    <label for="exampleInputPassword3" class="form-label">Confirm Password *</label>
                                    <input type="password" class="form-control" id="exampleInputPassword3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div> Row END -->
                    <!-- button -->
                    <div class="gap-3 d-md-flex justify-content-md-end text-center">
                        <button type="button" class="btn btn-primary btn-lg" id="btnSaveProfile">Actualizar Usuario</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/global/js/profile/profile.js"></script>