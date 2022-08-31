<div class="container rounded bg-white mt-5 mb-5">
    <div class="row">
        <div class="col-md-4 border-right">
            <div class="d-flex flex-column align-items-center text-center p-3 py-5"><img class="rounded-circle mt-5" width="150px" src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg"><span class="font-weight-bold" id="name">Edogaru</span><span class="text-black-50" id="mail">edogaru@mail.com.my</span><span> </span></div>
        </div>
        <div class="col-md-8 border-right">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-right">Configuración Usuario</h4>
                </div>
                <form id="formModifyProfileUser">
                    <div class="row mt-2">
                        <div class="col-md-6"><label class="labels">Nombre</label><input type="text" class="form-control" placeholder="first name" name="nameUser" id="firstname"></div>
                        <div class="col-md-6"><label class="labels">Apellido</label><input type="text" class="form-control" placeholder="lastname" name="lastnameUser" id="lastname"></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12"><label class="labels">Correo Electronico</label><input type="email" class="form-control" placeholder="email" name="email" id="email"></div>
                        <div class="col-md-6 mt-3"><label class="labels">Nombre De Usuario</label><input type="text" class="form-control" placeholder="username" name="username" id="username"></div>
                        <div class="col-md-6 mt-3"><label class="labels">Contraseña</label><input type="password" class="form-control" placeholder="password" name="pass" id="pass"></div>
                        <!-- <div class="col-md-12"><label class="labels">Address Line 2</label><input type="text" class="form-control" placeholder="enter address line 2"></div> -->
                    </div>
                    <!-- <div class="row mt-3">
                        <div class="col-md-6"><label class="labels">Country</label><input type="text" class="form-control" placeholder="country"></div>
                        <div class="col-md-6"><label class="labels">State/Region</label><input type="text" class="form-control" placeholder="state"></div>
                    </div> -->
                    <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="button" id="btnSaveProfile">Guardar Perfil</button></div>
                </form>
            </div>
        </div>
        <!-- <div class="col-md-4">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center experience"><span>Edit Experience</span><span class="border px-3 p-1 add-experience"><i class="fa fa-plus"></i>&nbsp;Experience</span></div><br>
                <div class="col-md-12"><label class="labels">Experience in Designing</label><input type="text" class="form-control" placeholder="experience"></div> <br>
                <div class="col-md-12"><label class="labels">Additional Details</label><input type="text" class="form-control" placeholder="additional details"></div>
            </div>
        </div> -->
    </div>
</div>

<script src="/global/js/profile/profile.js"></script>