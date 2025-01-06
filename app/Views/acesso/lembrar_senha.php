        <!-- Log In page -->
        <div class="row vh-100">
            <div class="col-lg-3  pr-0">
                <div class="card mb-0 shadow-none">
                    <div class="card-body">
                        
                        <div class="px-3">
                            <div class="text-center">
                                <a href="<?= base_url(); ?>" class="logo logo-admin"><img src="<?= base_url('public/assets/images/logo-dark.png'); ?>" height="55" alt="logo" class="my-3"></a>
                            </div>                            
                            
                            <form class="form-horizontal my-4" action="">

                                <div class="form-group">
                                    <label for="username">Usuário</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="mdi mdi-account-outline font-16"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="username" placeholder="CPF">
                                    </div>                                    
                                </div>
    
                                <div class="form-group">
                                    <label for="userpassword">Nova senha de acesso</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon2"><i class="mdi mdi-key font-16"></i></span>
                                        </div>
                                        <input type="password" class="form-control" id="userpassword" placeholder="Nova senha de acesso">
                                    </div>                                
                                </div>

                                <div class="form-group">
                                    <label for="userpassword">Repita a nova senha</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon2"><i class="mdi mdi-key font-16"></i></span>
                                        </div>
                                        <input type="password" class="form-control" id="userpasswordc" placeholder="Repita a nova senha">
                                    </div>                                
                                </div>
    
                                <div class="form-group row mt-4">
                                    <div class="col-sm-12 text-left">
                                        <a href="<?= base_url('acesso/login'); ?>" class="text-muted font-13"><i class="mdi mdi-lock"></i> Entrar</a>                                    
                                    </div>
                                </div>
    
                                <div class="form-group mb-0 row">
                                    <div class="col-12 mt-2">
                                        <button class="btn btn-info btn-block waves-effect waves-light" type="button" onclick="return lembrarsenha()">Alterar Senha <i class="fas fa-sign-in-alt ml-1"></i></button>
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
const lembrarsenha = function(){

    if(enviando == true) return false;

    let dados = {
        u: $("[id=username]").val(),
        p: $("[id=userpassword]").val(),
        c: $("[id=userpasswordc]").val(),
    }
    
    if(dados.u == ""){ exibeAlerta("error", "<b>CPF</b> não informado."); return false;}
    if(dados.p == ""){ exibeAlerta("error", "<b>Nova senha</b> não informada."); return false;}
    if(dados.c == ""){ exibeAlerta("error", "<b>Confirmação da nova senha</b> não informada."); return false;}
    if(dados.c != dados.p){ exibeAlerta("error", "<b>Confirmação da nova senha</b> inválida."); return false;}

    enviando = true;
    openLoading();

    $.ajax({
		url: baseurl+'acesso/action/lembrar_senha',
		type:'POST',
		data:{dados:dados},		
		success:function(result){
            
            try {
            
                var response = JSON.parse(result);

                enviando = false;
                exibeAlerta(response.tipo, response.msg);
                openLoading(true);

            } catch (e) {
                enviando = false;
                exibeAlerta('error', 'Falha interna do sistema.');
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
document.querySelector("[id=userpasswordc]").addEventListener("input", function(){
    var text = this.value;
    var chars = this.value.length;
    if(chars > 25) this.value = text.substr(0, 25);
});
</script>