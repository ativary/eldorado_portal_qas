<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                

                <div class="card-body">

                    <div class="progress mb-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-pink" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%;">25%</div>
                    </div>

                    <div id="smart_wizard_arrows">
                        <ul>
                            <li><a href="#step-1"><i class="far fa-user-circle"></i> DADOS PESSOAIS</a></li>
                            <li><a href="#step-2"><i class="dripicons-location"></i> ENDEREÇO</a></li>
                            <li><a href="#step-3"><i class="mdi mdi-account-card-details"></i> DOCUMENTOS</a></li>
                        </ul>
                        
                        <div class="p-3 sw-arrows-content mb-3">
                            <div id="step-1" class="">
                               <div class="row">
                            <div class="form-group col-lg-9">
                    <label for="nome"><strong>Nome Completo:</strong></label>
                    <input maxlength="100" data-validate="required" type="text" name="nome" id="nome" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                    <label for="datanascimento"><strong>Data de Nascimento:</strong></label>
                    <input placeholder="DD/MM/AAAA" data-validate="required" type="date" name="datanascimento" id="datanascimento" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                    <label for="rg"><strong>RG:</strong></label>
                    <input maxlength="15" data-validate="required" type="text" name="rg" id="rg" class="form-control form-control-sm">
                </div>
                <div class="form-group col-lg-3">
                    <label for="cpf"><strong>CPF:</strong></label>
                    <input autocomplete="off" placeholder="*somente números" onkeypress="return somenteNumeros(event);" maxlength="11" data-validate="required" type="text" name="cpf" id="cpf" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-12">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="validatedCustomFile" required="">
                    <label class="custom-file-label" for="validatedCustomFile">Anexar CPF...</label>
                    <div class="invalid-feedback">Example invalid custom file feedback</div>
                </div>
</div>

                <div class="form-group col-lg-6">
                    <label for="nomemae"><strong>Nome da Mãe:</strong></label>
                    <input maxlength="100" data-validate="required" type="text" name="nomemae" id="nomemae" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-6">
                    <label for="endereco"><strong>Endereço:</strong></label>
                    <input maxlength="130" data-validate="required" type="text" name="endereco" id="endereco" class="form-control form-control-sm">
                </div>
                <div class="form-group col-lg-2">
                    <label for="numero"><strong>Nº:</strong></label>
                    <input maxlength="8" data-validate="required" type="text" name="numero" id="numero" class="form-control form-control-sm">
                </div>
                <div class="form-group col-lg-4">
                    <label for="estado"><strong>Estado:</strong></label>
                    <select data-validate="required" name="estado" id="estado" class="form-control form-control-sm" onchange="CarregaCidades(this.value);">
                        <option value="">...</option>
                                                                                    <option value="AC">Acre</option>
                                                            <option value="AL">Alagoas</option>
                                                            <option value="AP">Amapa</option>
                                                            <option value="AM">Amazonas</option>
                                                            <option value="BA">Bahia</option>
                                                            <option value="CE">Ceara</option>
                                                            <option value="DF">Distrito Federal</option>
                                                            <option value="ES">Espirito Santo</option>
                                                            <option value="US">Estados Unidos</option>
                                                            <option value="GO">Goias</option>
                                                            <option value="JP">Japão</option>
                                                            <option value="MA">Maranhao</option>
                                                            <option value="MT">Mato Grosso</option>
                                                            <option value="MS">Mato Grosso do Sul</option>
                                                            <option value="MG">Minas Gerais</option>
                                                            <option value="PA">Para</option>
                                                            <option value="PB">Paraiba</option>
                                                            <option value="PR">Parana</option>
                                                            <option value="PE">Pernambuco</option>
                                                            <option value="PI">Piaui</option>
                                                            <option value="RN">Rio Grande do Norte</option>
                                                            <option value="RS">Rio Grande do Sul</option>
                                                            <option value="RJ">Rio de Janeiro</option>
                                                            <option value="RO">Rondonia</option>
                                                            <option value="RR">Roraima</option>
                                                            <option value="SC">Santa Catarina</option>
                                                            <option value="SP">Sao Paulo</option>
                                                            <option value="SE">Sergipe</option>
                                                            <option value="TO">Tocantins</option>
                                                                        </select>
                </div>

                <div class="form-group col-lg-3">
                    <label for="cidade"><strong>Cidade:</strong></label>
                    <select data-validate="required" name="cidade" id="cidade" class="form-control form-control-sm">
                        <option value="">...</option>
                    </select>
                </div>
                <div class="form-group col-lg-3">
                    <label for="bairro"><strong>Bairro:</strong></label>
                    <input maxlength="75" data-validate="required" type="text" name="bairro" id="bairro" class="form-control form-control-sm">
                </div>
                <div class="form-group col-lg-6">
                    <label for="email"><strong>Email:</strong></label>
                    <input maxlength="60" data-validate="required, email" type="text" name="email" id="email" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                    <label for="celular"><strong>Celular:</strong></label>
                    <input data-telefone data-validate="required" type="text" name="celular" id="celular" class="form-control form-control-sm">
                </div>
                

                <div class="form-group col-lg-3 text-success">
                    <label for="senha"><strong>Senha de Acesso:</strong></label>
                    <input maxlength="12" data-validate="required" type="password" name="senha" id="senha" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3 text-success">
                    <label for="csenha"><strong>Confirma Senha de Acesso:</strong></label>
                    <input maxlength="12" data-validate="required" type="password" name="csenha" id="csenha" class="form-control form-control-sm">
                </div>
                
                <div class="form-group col-lg-12">
                    <label for="jatrabalhou"><strong>Já trabalhou no HFC (unidades HFC), Coplacana, Afocapi, Sindirpi, Cocrefocapi, HFC Saúde:</strong></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jatrabalhou" id="jatrabalhou1" value="S">
                            <label class="form-check-label" for="jatrabalhou1">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jatrabalhou" id="jatrabalhou2" value="N">
                            <label class="form-check-label" for="jatrabalhou2">Não</label>
                        </div>
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label for="temparente"><strong>Tem parentes que trabalham aqui?</strong></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="temparente" id="temparente1" value="S">
                            <label class="form-check-label" for="temparente1">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="temparente" id="temparente2" value="N">
                            <label class="form-check-label" for="temparente2">Não</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-control form-control-sm" type="text" name="grauparentesco" id="grauparentesco" placeholder="Grau Parentesco">
                        </div>
                    </div>
                    <label for="qualsetor">Se sim, qual nome e em que setor trabalha:</label>
                    <input class="form-control form-control-sm" type="text" name="qualsetor" id="qualsetor">
                </div>

                <div class="form-group col-lg-12">
                    <label><strong>Qual a sua disponibilidade para o trabalho?</strong></label>
                    <div class="form-group">
                        <div class="row">
                            <div class="input-group col-sm-4">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><input type="checkbox" name="manha" value="manha"></div>
                                </div>
                                <input style="background: #ffffff;" readonly type="text" class="form-control" placeholder="Manhã">
                            </div>
                            <div class="input-group col-sm-4">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><input type="checkbox" name="tarde" value="tarde"></div>
                                </div>
                                <input style="background: #ffffff;" readonly type="text" class="form-control" placeholder="Tarde">
                            </div>
                            <div class="input-group col-sm-4">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><input type="checkbox" name="noite" value="noite"></div>
                                </div>
                                <input style="background: #ffffff;" readonly type="text" class="form-control" placeholder="Noite">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label><strong>É portador(a) de deficiência?</strong></label>
                    <div class="form-group">
                        <div class="row mb-2">
                            <div class="input-group col-lg-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><input type="checkbox" name="def_fisica" value="1"></div>
                                </div>
                                <input style="background: #ffffff;" readonly type="text" class="form-control" placeholder="DEF. FÍSICA">
                            </div>
                            <div class="input-group col-lg-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><input type="checkbox" name="def_auditiva" value="1"></div>
                                </div>
                                <input style="background: #ffffff;" readonly type="text" class="form-control" placeholder="DEF. AUDITIVA">
                            </div>
                            <div class="input-group col-lg-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><input type="checkbox" name="def_fala" value="1"></div>
                                </div>
                                <input style="background: #ffffff;" readonly type="text" class="form-control" placeholder="DEF. FALA">
                            </div>
                            <div class="input-group col-lg-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><input type="checkbox" name="def_visual" value="1"></div>
                                </div>
                                <input style="background: #ffffff;" readonly type="text" class="form-control" placeholder="DEF. VISUAL">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="input-group col-lg-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><input type="checkbox" name="def_mental" value="1"></div>
                                </div>
                                <input style="background: #ffffff;" readonly type="text" class="form-control" placeholder="DEF. MENTAL">
                            </div>
                            <div class="input-group col-lg-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><input type="checkbox" name="reabilitado" value="1"></div>
                                </div>
                                <input style="background: #ffffff;" readonly type="text" class="form-control" placeholder="REABILITADO (BR)">
                            </div>
                            <div class="input-group col-lg-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><input type="checkbox" name="intelectual" value="1"></div>
                                </div>
                                <input style="background: #ffffff;" readonly type="text" class="form-control" placeholder="INTELECTUAL">
                            </div>
                        </div>
                    </div>
                </div>
</div>
                
            </div>

                            </div>
                         
                        </div><!--end /div-->
                    </div> <!--end smartwizard-->  

                </div>
            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<link href="/public/assets/plugins/form-wizard/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css" />
<script src="/public/assets/plugins/form-wizard/js/jquery.smartWizard.min.js"></script>
<script>
$(document).ready(function(){

    // Toolbar extra buttons
  var btnFinish = $('<button></button>').text('Finalizar')
      .addClass('btn btn-info')
      .on('click', function(){ alert('Finish Clicked'); 
  });
  var btnCancel = $('<button></button>').text('Cancelar')
      .addClass('btn btn-dark')
      .on('click', function(){ $('#smart_wizard').smartWizard("reset"); 
  });

    // Smart Wizard Arrows
    $('#smart_wizard_arrows').smartWizard({
        selected: 0,
        theme: 'arrows',
        transitionEffect:'fade',
        toolbarSettings: {
            toolbarPosition: 'bottom'
        }
    });
});
</script>
