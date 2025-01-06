        <!-- Log In page -->
        <div class="row vh-100">
            <div class="col-lg-3  pr-0">
                <div class="card mb-0 shadow-none">
                    <div class="card-body">
                        
                        <div class="px-3">
                            <div class="text-center">
                                <a href="<?= base_url(); ?>" class="logo logo-admin"><img src="<?= base_url('public/assets/images/logo-dark.png'); ?>" height="55" alt="logo" class="my-3"></a>
                            </div>                            
                            
                            <form class="form-horizontal my-4" action="" onsubmit="return logar()">

                                <?= exibeMensagem(); ?>

                                <div class="form-group">
                                    <label for="username">Usuário</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="mdi mdi-account-outline font-16"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="username" placeholder="CPF" maxlength="25">
                                    </div>                                    
                                </div>
    
                                <div class="form-group">
                                    <label for="userpassword">Senha de acesso</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon2"><i class="mdi mdi-key font-16"></i></span>
                                        </div>
                                        <input type="password" class="form-control" id="userpassword" placeholder="Senha de acesso" maxlength="25">
                                    </div>                                
                                </div>
    
                                <div class="form-group row mt-4">
                                    <div class="col-sm-6">
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        <a href="<?= base_url('acesso/lembrar_senha'); ?>" class="text-muted font-13"><i class="mdi mdi-lock"></i> Esqueceu a senha?</a>                                    
                                    </div>
                                </div>
    
                                <div class="form-group mb-0 row">
                                    <div class="col-12 mt-2">
                                        <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" onclick="return logar()">Entrar <i class="fas fa-sign-in-alt ml-1"></i></button>
                                    </div>
                                </div>                            
                            </form>
                        </div>                      
                    </div>
                </div>
            </div>
            <div class="col-lg-9 p-0 d-flex justify-content-center">
                <div class="accountbg d-flex align-items-center"> 
                    <div class="account-title text-white text-center">
                        <div class="border w-25 mx-auto border-primary"></div>
                        <h1 class="">PortalRH</h1>
                        <p class="font-14 mt-3">Seja bem vindo!</p>
                       
                    </div>
                </div>
            </div>
        </div>
        <!-- End Log In page -->

<script type="text/javascript">
enviando = false;
const logar = function(){

    if(enviando == true) return false;

    let dados = {
        u: $("[id=username]").val(),
        p: $("[id=userpassword]").val(),
    }
    
    if(dados.u == ""){ exibeAlerta("error", "<b>CPF</b> não informado."); return false;}
    if(dados.p == ""){ exibeAlerta("error", "<b>Senha</b> não informada."); return false;}

    enviando = true;

    $.ajax({
		url: baseurl+'acesso/action/logar',
		type:'POST',
		data:{dados:dados},		
		success:function(result){
            
            try {
            
                var response = JSON.parse(result);
                if(response.tipo == "error"){
                    enviando = false;
                    exibeAlerta(response.tipo, response.msg);
                    openLoading(true);
                }
                if(response.tipo == "success"){
                    exibeAlerta('success', 'Sucesso', 1, '<?= base_url(); ?>');
                }

            } catch (e) {
                enviando = false;
                exibeAlerta('error', 'Não foi possivel logar no sistema.');
                openLoading(true);
                console.log("#### LOG DE ERRO:");
                console.log(e);
                return false;
            }

		}
	});

}
document.querySelector("[id=username]").addEventListener("input", function(){
    var text = this.value;
    var chars = this.value.length;
    if(chars > 25) this.value = text.substr(0, 25);
});
document.querySelector("[id=userpassword]").addEventListener("input", function(){
    var text = this.value;
    var chars = this.value.length;
    if(chars > 25) this.value = text.substr(0, 25);
});
</script>