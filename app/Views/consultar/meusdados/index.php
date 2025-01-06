<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            
                

                    <?= exibeMensagem(); ?>

                    <?php if($MeusDados): ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card noprint">
                                    <div class="card-body border-bottom">
                                        <div class="fro_profile">
                                            <div class="row">
                                                <div class="col-lg-12 mb-3 mb-lg-0">
                                                    <div class="fro_profile-main">
                                                        <div class="fro_profile-main-pic">
                                                            <?php
                                                            $imagemPadrao =base_url('public/assets/images/users/user.jpg');
                                                            if(strlen(trim($MeusDados[0]['IMAGEM'])) > 0){
                                                                $IMAGEM_FUNC = $MeusDados[0]['IMAGEM'];
                                                                $info = getimagesizefromstring($IMAGEM_FUNC);
                                                                $imagemPadrao = "data:{$info['mime']};base64,".base64_encode($IMAGEM_FUNC);
                                                            }
                                                            ?>
                                                            <img src="<?= $imagemPadrao; ?>" alt="" class="rounded-circle" width="128" height="128">
                                                        </div>
                                                        <div class="fro_profile_user-detail">
                                                            <h5 class="fro_user-name"><?= $MeusDados[0]['NOME']; ?></h5>
                                                            <p class="mb-0 fro_user-name-post"><?= $MeusDados[0]['NOMEFUNCAO']; ?></p>
                                                        </div>
                                                    </div>
                                                </div><!--end col-->
                                            </div><!--end row-->
                                        </div><!--end f_profile-->
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-12">
                                <div class="card noprint">
                                    <div class="card-body">


                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#informacoes_pessoais" role="tab"><i class="fas fa-user-alt"></i> Informações Pessoais</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#endereco" role="tab"><i class="fas fa-map-marker-alt"></i> Endereço</a>
                                            </li>                                                
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#documentos" role="tab"><i class="far fa-id-card"></i> Documentos</a>
                                            </li>
                                        </ul>
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <div class="tab-pane active p-3" id="informacoes_pessoais" role="tabpanel">

                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        
                                                        <div class="form-group row mb-1">
                                                            <label for="data_de_nascimento" class="col-sm-2 col-form-label text-right  col-form-label-sm">Data nascimento</label>
                                                            <div class="col-sm-4">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= dtBr($MeusDados[0]['DTNASCIMENTO']); ?>" id="data_de_nascimento" name="data_de_nascimento">
                                                            </div>
                                                            <label for="sexo" class="col-sm-2 col-form-label text-right col-form-label-sm">Sexo</label>
                                                            <div class="col-sm-4">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['SEXO']; ?>" id="sexo" name="sexo">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-1">
                                                            <label for="email" class="col-sm-2 col-form-label text-right col-form-label-sm">Email</label>
                                                            <div class="col-sm-10">
                                                                <input disabled class="form-control form-control-sm" type="email" value="<?= $MeusDados[0]['EMAIL']; ?>" id="email" name="email">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-1">
                                                            <label for="telefone1" class="col-sm-2 col-form-label text-right  col-form-label-sm">Telefone1</label>
                                                            <div class="col-sm-4">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['TELEFONE1']; ?>" id="telefone1" name="telefone1">
                                                            </div>
                                                            <label for="telefone2" class="col-sm-2 col-form-label text-right col-form-label-sm">Telefone2</label>
                                                            <div class="col-sm-4">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['TELEFONE2']; ?>" id="telefone2" name="telefone2">
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                            <div class="tab-pane p-3" id="endereco" role="tabpanel">
                                                
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        
                                                        <div class="form-group row mb-1">
                                                            <label for="endereco" class="col-sm-2 col-form-label text-right  col-form-label-sm">Endereço</label>
                                                            <div class="col-sm-6">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['RUA']; ?>" id="endereco" name="endereco">
                                                            </div>
                                                            <label for="numero" class="col-sm-1 col-form-label text-right col-form-label-sm">Nº</label>
                                                            <div class="col-sm-3">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['NUMERO']; ?>" id="numero" name="numero">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-1">
                                                            <label for="bairro" class="col-sm-2 col-form-label text-right  col-form-label-sm">Bairro</label>
                                                            <div class="col-sm-3">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['BAIRRO']; ?>" id="bairro" name="bairro">
                                                            </div>
                                                            <label for="cep" class="col-sm-1 col-form-label text-right col-form-label-sm">CEP</label>
                                                            <div class="col-sm-2">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['CEP']; ?>" id="cep" name="cep">
                                                            </div>
                                                            <label for="complemento" class="col-sm-1 col-form-label text-right  col-form-label-sm">Compl.</label>
                                                            <div class="col-sm-3">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['COMPLEMENTO']; ?>" id="complemento" name="complemento">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-1">
                                                            <label for="cidade" class="col-sm-2 col-form-label text-right  col-form-label-sm">Cidade</label>
                                                            <div class="col-sm-4">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['CIDADE']; ?>" id="cidade" name="cidade">
                                                            </div>
                                                            <label for="estado" class="col-sm-2 col-form-label text-right col-form-label-sm">Estado</label>
                                                            <div class="col-sm-4">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['ESTADO']; ?>" id="estado" name="estado">
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                
                                            </div>                                                
                                            <div class="tab-pane p-3" id="documentos" role="tabpanel">
                                                
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        
                                                        <div class="form-group row mb-1">
                                                            <label for="cpf" class="col-sm-2 col-form-label text-right  col-form-label-sm">CPF</label>
                                                            <div class="col-sm-4">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['CPF']; ?>" id="cpf" name="cpf">
                                                            </div>
                                                            <label for="pis" class="col-sm-2 col-form-label text-right col-form-label-sm">PIS</label>
                                                            <div class="col-sm-4">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['PIS']; ?>" id="pis" name="pis">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-1">
                                                            <label for="rg" class="col-sm-2 col-form-label text-right  col-form-label-sm">RG</label>
                                                            <div class="col-sm-2">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['RG']; ?>" id="rg" name="rg">
                                                            </div>
                                                            <label for="rg_emissao" class="col-sm-2 col-form-label text-right col-form-label-sm">Emissão</label>
                                                            <div class="col-sm-2">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= dtBr($MeusDados[0]['DTEMISSAOIDENT']); ?>" id="rg_emissao" name="rg_emissao">
                                                            </div>
                                                            <label for="rg_emissor" class="col-sm-2 col-form-label text-right col-form-label-sm">Órg. Expedidor</label>
                                                            <div class="col-sm-2">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['ORGEMISSORIDENT']; ?>" id="rg_emissor" name="rg_emissor">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-1">
                                                            <label for="ctps" class="col-sm-2 col-form-label text-right  col-form-label-sm">Cart. Prof.</label>
                                                            <div class="col-sm-2">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['CARTEIRATRAB']; ?>" id="ctps" name="ctps">
                                                            </div>
                                                            <label for="ctps_serie" class="col-sm-1 col-form-label text-right col-form-label-sm">Série</label>
                                                            <div class="col-sm-2">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['SERIECARTTRAB']; ?>" id="ctps_serie" name="ctps_serie">
                                                            </div>
                                                            <label for="ctps_data" class="col-sm-1 col-form-label text-right col-form-label-sm">Data</label>
                                                            <div class="col-sm-2">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= dtBr($MeusDados[0]['DTCARTTRAB']); ?>" id="ctps_data" name="ctps_data">
                                                            </div>
                                                            <label for="ctps_uf" class="col-sm-1 col-form-label text-right col-form-label-sm">UF</label>
                                                            <div class="col-sm-1">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['UFCARTTRAB']; ?>" id="ctps_uf" name="ctps_uf">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-1">
                                                            <label for="cnh" class="col-sm-2 col-form-label text-right  col-form-label-sm">CNH</label>
                                                            <div class="col-sm-3">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['CARTMOTORISTA']; ?>" id="cnh" name="cnh">
                                                            </div>
                                                            <label for="cnh_tipo" class="col-sm-1 col-form-label text-right col-form-label-sm">Tipo</label>
                                                            <div class="col-sm-2">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= $MeusDados[0]['TIPOCARTHABILIT']; ?>" id="cnh_tipo" name="cnh_tipo">
                                                            </div>
                                                            <label for="cnh_data" class="col-sm-1 col-form-label text-right col-form-label-sm">Data</label>
                                                            <div class="col-sm-3">
                                                                <input disabled class="form-control form-control-sm" type="text" value="<?= dtBr($MeusDados[0]['DTVENCHABILIT']); ?>" id="cnh_data" name="cnh_data">
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                            

                                    </div>
                                </div>
                            </div>
                            
                            
                        </div>

                    <?php else: ?>
                        <div class="card-body border-bottom">
                            <div class="alert alert-warning2 border-0" role="alert">
                                <i class="fas fa-info-circle"></i> Nenhum registro encontrado.
                            </div>
                        </div>
                    <?php endif; ?>
                
            </div>
        </div>
        
        
    </div>
</div><!-- container -->
<style>
    .tab-pane {
        border-left: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
    }
</style>