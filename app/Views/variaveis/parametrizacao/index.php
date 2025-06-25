
<style>
    .nav-tabs .nav-link {
        font-size: 12px; /* Reduz o tamanho da fonte */
        padding: 5px 10px; /* Ajusta o espaçamento interno dos botões */
        margin-right: 2px; /* Espaçamento entre os botões */
    }
</style>
<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
					
				<div class="card-header mt-0">
					<div class="row">
						<h4 class="col-12 mb-1 mt-1"><i class="fa fa-cogs"></i> <?= $_titulo; ?></h4>
					</div>
				</div>
				
				<div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <ul class="nav nav-tabs" role="tablist">
                     
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#auxilio_aluguel" role="tab"><i class="fa fa-key"></i> Auxilio Aluguel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#auxilio_creche" role="tab"><i class="fa fa-child"></i> Auxilio Creche</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#reembolso_cpd" role="tab"><i class="fa fa-wheelchair"></i> Auxilio Excepcional</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " data-toggle="tab" href="#auxilio_moradia" role="tab"><i class="fa fa-home"></i> Auxílio Moradia</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#salario" role="tab"><i class="fa fa-dollar-sign"></i> Antecipação 13º Salário</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#coparticipacao" role="tab"><i class="fa fa-hospital"></i> Coparticipação</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#auxilio_desconto" role="tab"><i class="fa fa-dollar-sign"></i> Desconto Autorizado</a>
                        </li>
                       
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#substituicao" role="tab"><i class="fa fa-dollar-sign"></i> Salário Substituição</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#sobreaviso" role="tab"><i class="fa fa-dollar-sign"></i> Sobreaviso</a>
                        </li>
                    </ul>


                    <div class="tab-content">
                        <!-- auxilio moradia -->
                        <div class="tab-pane  p-3" id="auxilio_moradia" role="tabpanel">
                        
                            <div class="form-group row">
                                <label for="auxilio_moradia_evento" class="col-sm-12 text-primary">Regra de cálculo:</label>
                                <div class="col-sm-2" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="auxilio_moradia_porcentagem" name="auxilio_moradia_porcentagem" class="form-control" placeholder="00" value="<?= $param6->auxilio_moradia_porcentagem ?? ''; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong>%</strong></span>
                                        </div>
                                    </div> 
                                </div>
                                <label for="auxilio_moradia_porcentagem" class="col-sm-10 pt-2 col-form-label text-left">do salário base do colaborador.</label>
                            </div>

                            <div class="form-group">
                                <label for="auxilio_moradia_evento" class="text-primary">Evento:</label>
                                <select class="select2 custom-select form-control" name="auxilio_moradia_evento" id="auxilio_moradia_evento">
                                    <option value="">:: Evento ::</option>
                                    <?php if($listaEventos): ?>
                                        <?php foreach($listaEventos as $Evento): ?>
                                            <option value="<?= $Evento->CODIGO; ?>" <?= ($Evento->CODIGO == $param6->auxilio_moradia_evento) ? 'selected' : ''; ?>><?= $Evento->CODIGO.' - '.$Evento->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group row">
                                <label for="periodo_moradia" class="col-sm-7 text-primary">Periodo de abertura:</label>
                                <label for="periodo_creche" class="col-sm-5 text-primary">Complementar no Mês:</label>
                                <div class="col-sm-7" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number"  onchange="validateField(this)" id="periodo_moradia" name="periodo_moradia" class="form-control" placeholder="00" value="<?= $param6->periodo_moradia ?? ''; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong>Até</strong></span>
                                        </div>
                                    <input type="number"  onchange="validateField(this)" id="periodo_moradia_fim" name="periodo_moradia_fim" class="form-control" placeholder="00" value="<?= $param6->periodo_moradia_fim ?? ''; ?>">
                                    <label for="auxilio_moradia_porcentagem" class="col-sm-8 pt-2 col-form-label text-left">Periodo permitido para abertura no mês atual.</label>
                                    </div> 
                                </div>
                                <div class="col-sm-5" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="dia_limite_compl6"  onchange="validateField(this)" name="dia_limite_compl6" class="form-control" placeholder="00" value="<?= $param6->dia_limite_compl6 ?? ''; ?>">
                                    <label for="dia_limite_compl6" class="col-sm-10 pt-2 col-form-label text-left">Dia limite para complementar entrar na folha do mês.</label>
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                    <label for="gestor_pcd" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Exceção Gestor:</label>
                                    <div class="col-sm-8">
                                        <select  class="select2 custom-select form-control form-control-sm" name="gestor_moradia" id="gestor_moradia">
                                            <option value=""> ... </option>
                                            <?php if ($resFuncionarioSecao) : ?>
                                                <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                    <option value="<?= $DadosFunc['CHAPA'] ?>" ><?= $DadosFunc['NOME'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                   
                                    </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="btnadd3" ><i class="fas fa-plus"></i> Adicionar</button>
                                   
                                </div>
                               
                            </div>

                            <div class="form-group row mb-2" id="gestorMoradiaTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="gestorMoradiaTable">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Chapa</th>
                                                <th>Tipo</th>
                                                <th>Data inicio</th>
                                                <th>Data fim</th> 
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($param6->gestor)) : ?>
                                            <?php foreach ($param6->gestor as $key2 => $dados6) : ?>
                                                <tr>
                                                    <td class="n-mobile-cell"><?= $dados6->nome ?></td>
                                                    <td class="n-mobile-cell" ><?= $dados6->chapa ?></td>
                                                    <td class="n-mobile-cell" >Auxílio Moradia</td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados6->dt_ini) ? $dados6->dt_ini : '' ?>" name="data_inicio_moradia">
                                                    </td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados6->dt_fim) ? $dados6->dt_fim : '' ?>" name="data_fim_moradia">
                                                    </td>

                                                    <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="removeRow3(this)">Remover</button></td>
                                        
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-center">
                                <hr>
                                <button class="btn btn-success bteldorado_1" onclick="return save(6)"><i class="fa fa-save"></i> Salvar Parametrização</button>
                            </div>
                            

                        </div>
                        
                        <!--/ auxilio moradia -->
                        <div class="tab-pane  p-3" id="sobreaviso" role="tabpanel">
                        
                        

                            <div class="form-group">
                                <label for="sobreaviso_evento" class="text-primary">Evento:</label>
                                <select class="select2 custom-select form-control" name="sobreaviso_evento" id="sobreaviso_evento">
                                    <option value="">:: Evento ::</option>
                                    <?php if($listaEventos): ?>
                                        <?php foreach($listaEventos as $Evento): ?>
                                            <option value="<?= $Evento->CODIGO; ?>" <?= (isset($param3->sobreaviso_evento) && $Evento->CODIGO == $param3->sobreaviso_evento) ? 'selected' : ''; ?>><?= $Evento->CODIGO.' - '.$Evento->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group row">
                                <label for="dias_ponto" class="col-sm-7 text-primary">Dias de Ponto:</label>
                                <label for="limite_sobreaviso" class="col-sm-5 text-primary">Limite de Horas:</label>
                                <div class="col-sm-7" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number"  onchange="validateField(this)" id="dia_ponto_ini" name="dia_ponto_ini" class="form-control" placeholder="00" value="<?= $param3->dia_ponto_ini ?? ''; ?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong>Até</strong></span>
                                        </div>
                                    <input type="number"  onchange="validateField(this)" id="dia_ponto_fim" name="dia_ponto_fim" class="form-control" placeholder="00" value="<?= $param3->dia_ponto_fim ?? ''; ?>">
                                    <label for="dia_ponto_aviso" class="col-sm-8 pt-2 col-form-label text-left">Dias de início e fim do ponto.</label>
                                    </div> 
                                </div>

                                <div class="col-sm-5" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number"  id="limite_sobreaviso" name="limite_sobreaviso" class="form-control" placeholder="00" value="<?= $param3->limite_sobreaviso ?? ''; ?>">
                                    <label for="dia_limite_compl3" class="col-sm-10 pt-2 col-form-label text-left">Limite de horas de sobreaviso no mês</label>
                                    </div>
                                </div>
                            
                            </div>
                            <div class="form-group row">
                                <label for="periodo_sobreaviso" class="col-sm-7 text-primary">Periodo de abertura:</label>
                                <label for="periodo_creche" class="col-sm-5 text-primary">Complementar no Mês:</label>
                                <div class="col-sm-7" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number"  onchange="validateField(this)" id="periodo_sobreaviso" name="periodo_sobreaviso" class="form-control" placeholder="00" value="<?= $param3->periodo_sobreaviso ?? ''; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong>Até</strong></span>
                                        </div>
                                    <input type="number"  onchange="validateField(this)" id="periodo_sobreaviso_fim" name="periodo_sobreaviso_fim" class="form-control" placeholder="00" value="<?= $param3->periodo_sobreaviso_fim ?? ''; ?>">
                                    <label for="sobreaviso_porcentagem" class="col-sm-8 pt-2 col-form-label text-left">Periodo permitido para abertura no mês atual.</label>
                                    </div> 
                                </div>
                                <div class="col-sm-5" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="dia_limite_compl3"  onchange="validateField(this)" name="dia_limite_compl3" class="form-control" placeholder="00" value="<?= $param3->dia_limite_compl3 ?? ''; ?>">
                                    <label for="dia_limite_compl3" class="col-sm-10 pt-2 col-form-label text-left">Dia limite para complementar entrar na folha do mês.</label>
                                    </div> 
                                </div>
                            
                            </div>
                            <div class="form-group row mb-2">
                                    <label for="gestor_pcd" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Exceção Gestor:</label>
                                    <div class="col-sm-8">
                                        <select  class="select2 custom-select form-control form-control-sm" name="gestor_sobreaviso" id="gestor_sobreaviso">
                                            <option value=""> ... </option>
                                            <?php if ($resFuncionarioSecao) : ?>
                                                <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                    <option value="<?= $DadosFunc['CHAPA'] ?>" ><?= $DadosFunc['NOME'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                
                                    </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="btnaddSobreaviso" ><i class="fas fa-plus"></i> Adicionar</button>
                                
                                </div>
                            
                            </div>

                            <div class="form-group row mb-2" id="gestorsobreavisoTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="gestorsobreavisoTable">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Chapa</th>
                                                <th>Tipo</th>
                                                <th>Data inicio</th>
                                                <th>Data fim</th> 
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($param3->gestor)) : ?>
                                            <?php foreach ($param3->gestor as $key2 => $dados6) : ?>
                                                <tr>
                                                    <td class="n-mobile-cell"><?= $dados6->nome ?></td>
                                                    <td class="n-mobile-cell" ><?= $dados6->chapa ?></td>
                                                    <td class="n-mobile-cell" > Sobreaviso</td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados6->dt_ini) ? $dados6->dt_ini : '' ?>" name="data_inicio_moradia">
                                                    </td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados6->dt_fim) ? $dados6->dt_fim : '' ?>" name="data_fim_moradia">
                                                    </td>

                                                    <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="removeRowSobreaviso(this)">Remover</button></td>
                                        
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-center">
                                <hr>
                                <button class="btn btn-success bteldorado_1" onclick="return save(3)"><i class="fa fa-save"></i> Salvar Parametrização</button>
                            </div>
                            

                        </div>
                    <!--/ auxilio moradia -->
                        <!--/ auxilio moradia -->
                        <div class="tab-pane  p-3" id="substituicao" role="tabpanel">
                        <div class="form-group row">
                                <label for="subistiuicao_min_dias" class="col-sm-12 text-primary">Dias minimos para Substituição:</label>
                                <div class="col-sm-2" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="subistiuicao_min_dias" name="subistiuicao_min_dias" class="form-control" placeholder="00" value="<?= $param1->subistiuicao_min_dias ?? ''; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong></strong></span>
                                        </div>
                                    </div> 
                                </div>
                              
                            </div>
                            <div class="form-group row">
                                <label for="subistiuicao_max_dias" class="col-sm-12 text-primary">Dias Maximos para Substituição:</label>
                                <div class="col-sm-2" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="subistiuicao_max_dias" name="subistiuicao_max_dias" class="form-control" placeholder="00" value="<?= $param1->subistiuicao_max_dias ?? ''; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong></strong></span>
                                        </div>
                                    </div> 
                                </div>
                                
                            </div>
                          
                           
                            <!-- <div class="form-group">
                                <label for="substituicao_func" class="text-primary">Funcionários exceções:</label>
                                <select class="select2 custom-select form-control" multiple="multiple" name="substituicao_func[]" id="substituicao_func">
                                    <option value="">...</option>
                                    <?php if ($resFuncionarioSecao) : ?>
                                            <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>" <?= (isset($param1->substituicao_func) &&  $DadosFunc['CHAPA'] == $param1->substituicao_func) ? 'selected' : ''; ?> ><?= $DadosFunc['CHAPA'] ?> - <?= $DadosFunc['NOME'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                </select>
                                
                            </div> -->

                        <div class="form-group">
                            <label for="substituicao_evento" class="text-primary">Evento:</label>
                            <select class="select2 custom-select form-control" name="substituicao_evento" id="substituicao_evento">
                                <option value="">:: Evento ::</option>
                                <?php if($listaEventos): ?>
                                    <?php foreach($listaEventos as $Evento): ?>
                                        <option value="<?= $Evento->CODIGO; ?>" <?= (isset($param1->substituicao_evento) && $Evento->CODIGO == $param1->substituicao_evento) ? 'selected' : ''; ?>><?= $Evento->CODIGO.' - '.$Evento->DESCRICAO; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group row">
                            <label for="periodo_substituicao" class="col-sm-12 text-primary">Periodo de abertura:</label>
                            <div class="col-sm-7" style="width:100px !important;">
                                <div class="input-group">
                                <input type="number"  onchange="validateField(this)" id="periodo_substituicao" name="periodo_substituicao" class="form-control" placeholder="00" value="<?= $param1->periodo_substituicao ?? ''; ?>">

                                    <div class="input-group-append">
                                        <span class="input-group-text"><strong>Até</strong></span>
                                    </div>
                                <input type="number"  onchange="validateField(this)" id="periodo_substituicao_fim" name="periodo_substituicao_fim" class="form-control" placeholder="00" value="<?= $param1->periodo_substituicao_fim ?? ''; ?>">
                                <label for="substituicao_porcentagem" class="col-sm-8 pt-2 col-form-label text-left">Periodo permitido para abertura no mês atual.</label>
                                </div> 
                            </div>
                           
                        </div>
                          
                        <div class="form-group row mb-2">
                                <label for="gestor_pcd" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Exceção Gestor:</label>
                                <div class="col-sm-8">
                                    <select  class="select2 custom-select form-control form-control-sm" name="gestor_substituicao" id="gestor_substituicao">
                                        <option value=""> ... </option>
                                        <?php if ($resFuncionarioSecao) : ?>
                                            <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>" ><?= $DadosFunc['NOME'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                               
                                </div>
                            <div class="col-sm-2">
                                <button class="btn btn-success" id="btnaddsubstituicao" ><i class="fas fa-plus"></i> Adicionar</button>
                               
                            </div>
                           
                        </div>

                        <div class="form-group row mb-2" id="gestorsubstituicaoTableContainer" >
                            <div class="col-sm-12">
                                <table class="table table-bordered" id="gestorsubstituicaoTable">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Chapa</th>
                                            <th>Tipo</th>
                                            <th>Data inicio</th>
                                            <th>Data fim</th> 
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (isset($param1->gestor)) : ?>
                                        <?php foreach ($param1->gestor as $key2 => $dados6) : ?>
                                            <tr>
                                                <td class="n-mobile-cell"><?= $dados6->nome ?></td>
                                                <td class="n-mobile-cell" ><?= $dados6->chapa ?></td>
                                                <td class="n-mobile-cell" > Substituição</td>
                                                <td class="n-mobile-cell">
                                                    <input class="form-control datepicker m_data" type="date" value="<?= isset($dados6->dt_ini) ? $dados6->dt_ini : '' ?>" name="data_inicio_moradia">
                                                </td>
                                                <td class="n-mobile-cell">
                                                    <input class="form-control datepicker m_data" type="date" value="<?= isset($dados6->dt_fim) ? $dados6->dt_fim : '' ?>" name="data_fim_moradia">
                                                </td>

                                                <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="removeRowsubstituicao(this)">Remover</button></td>
                                    
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="text-center">
                            <hr>
                            <button class="btn btn-success bteldorado_1" onclick="return save(1)"><i class="fa fa-save"></i> Salvar Parametrização</button>
                        </div>
                        

                    </div>

                    <!--/ auxilio moradia -->
                    <div class="tab-pane  p-3" id="auxilio_desconto" role="tabpanel">
                        <div class="form-group row">
                                <label for="desconto_evento" class="col-sm-12 text-primary">Limite de desconto:</label>
                                <div class="col-sm-2" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="desconto_porcentagem" name="desconto_porcentagem" class="form-control" placeholder="00" value="<?= $param5->desconto_porcentagem ?? ''; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong>%</strong></span>
                                        </div>
                                    </div> 
                                </div>
                                <label for="desconto_porcentagem" class="col-sm-10 pt-2 col-form-label text-left">do salário base do colaborador.</label>
                            </div>
        

                            <div class="form-group">
                                <label for="reembolso_desconto_evento" class="text-primary">Evento Folha: Desconto Autorizado</label>
                                <select class="select2 custom-select form-control" name="reembolso_desconto_evento" id="reembolso_desconto_evento">
                                    <option value="">:: Evento ::</option>
                                    <?php if($listaEventos): ?>
                                        <?php foreach($listaEventos as $Evento): ?>
                                            <option value="<?= $Evento->CODIGO; ?>" <?= (isset($param5->reembolso_desconto_evento) && $Evento->CODIGO == $param5->reembolso_desconto_evento ) ? 'selected' : ''; ?>><?= $Evento->CODIGO.' - '.$Evento->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="reembolso_desconto_evento" class="text-primary">Evento Folha: Desconto de EPIs:</label>
                                <select class="select2 custom-select form-control" name="reembolso_desconto_evento2" id="reembolso_desconto_evento2">
                                    <option value="">:: Evento ::</option>
                                    <?php if($listaEventos): ?>
                                        <?php foreach($listaEventos as $Evento): ?>
                                            <option value="<?= $Evento->CODIGO; ?>" <?= (isset($param5->reembolso_desconto_evento2) && $Evento->CODIGO == $param5->reembolso_desconto_evento2 ) ? 'selected' : ''; ?>><?= $Evento->CODIGO.' - '.$Evento->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="reembolso_desconto_evento" class="text-primary"> Evento Folha: Multa de Trânsito:</label>
                                <select class="select2 custom-select form-control" name="reembolso_desconto_evento3" id="reembolso_desconto_evento3">
                                    <option value="">:: Evento ::</option>
                                    <?php if($listaEventos): ?>
                                        <?php foreach($listaEventos as $Evento): ?>
                                            <option value="<?= $Evento->CODIGO; ?>" <?= (isset($param5->reembolso_desconto_evento3) && $Evento->CODIGO == $param5->reembolso_desconto_evento3 ) ? 'selected' : ''; ?>><?= $Evento->CODIGO.' - '.$Evento->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group row">
                                <label for="periodo_desconto" class="col-sm-12 text-primary">Periodo de abertura:</label>
                                <div class="col-sm-7" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="periodo_desconto"  onchange="validateField(this)" name="periodo_desconto" class="form-control" placeholder="00" value="<?= $param5->periodo_desconto ?? ''; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong>Até</strong></span>
                                        </div>
                                    <input type="number" onchange="validateField(this)" id="periodo_desconto_fim" name="periodo_desconto_fim" class="form-control" placeholder="00" value="<?= $param5->periodo_desconto_fim ?? ''; ?>">
                                    <label for="periodo_desconto_fim" class="col-sm-8 pt-2 col-form-label text-left">Periodo permitido para abertura no mês atual.</label>
                                    </div> 
                                </div>
                               
                            </div>

                            <div class="form-group "> 
                                <div class="col-sm-12">
                                    <label for="reembolso_cpd_valor_demais_filiais" class="pt-2 col-form-label text-left" style="display:inline-block;">Demais Filiais:</label>
                                    <div class="input-group" style="display:inline-flex; width: 200px;">
                                        
                                        <input  type="number" id="reembolso_desconto_valor_demais_filiais" name="reembolso_desconto_valor_demais_filiais" class="form-control" placeholder="3" value="<?= (isset($param5->reembolso_desconto_valor_demais_filiais)) ? $param5->reembolso_desconto_valor_demais_filiais : ''; ?>">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><strong>Meses</strong></span>
                                        </div>
                                    </div> 
                                </div>
                            </div>

                           

                            
                            <div class="form-group row mb-2">

                                    <label for="FilialDesconto" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Meses por Filial:</label>
                                    <div class="col-sm-8">
                                        <select  class="select2 custom-select form-control form-control-sm" name="FilialDesconto" id="FilialDesconto">
                                            <option value=""> ... </option>
                                            <?php if ($listaFiliais) : ?>
                                                <?php foreach ($listaFiliais as $key2 => $filial) : ?>
                                                    <option value='<?= $filial->CODFILIAL ?>' ><?= $filial->CODFILIAL ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                   
                                    </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="DescontoFilial" ><i class="fas fa-plus"></i> Adicionar</button>
                                   
                                </div>
                               
                            </div>

                            <div class="form-group row mb-2" id="descontoTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="descontoTable">
                                        <thead>
                                            <tr>
                                                <th>Filial</th>
                                                <th>Meses</th> <!-- Nova coluna para o valor -->
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php    if(isset($param5->desconto))  : ?>
                                        <?php foreach ($param5->desconto as $key2 => $dados3) : ?>
                                            <tr>
                                                <td class="n-mobile-cell"><?= $dados3->nome ?></td>
                                                <td class="n-mobile-cell" ><input type="number"  class=" form-control form-control-sm valorDependente" value="<?= $dados3->valor ?>" placeholder="Quantidade de Meses"></td>
                                                <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="removeRowDesconto2(this)">Remover</button></td>
                                    
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                         

    

           

                            <div class="form-group row mb-2">
                                    <label for="gestor_desconto" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Exceção Gestor:</label>
                                    <div class="col-sm-8">
                                        <select  class="select2 custom-select form-control form-control-sm" name="gestor_desconto" id="gestor_desconto">
                                            <option value=""> ... </option>
                                            <?php if ($resFuncionarioSecao) : ?>
                                                <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                    <option value="<?= $DadosFunc['CHAPA'] ?>" ><?= $DadosFunc['NOME'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                   
                                    </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="DescontoAdd2" ><i class="fas fa-plus"></i> Adicionar</button>
                                   
                                </div>
                               
                            </div>

                            <div class="form-group row mb-2" id="gestordescontoTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="gestordescontoTable">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Chapa</th>
                                                <th>Tipo</th>
                                                <th>Data inicio</th>
                                                <th>Data fim</th> 
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($param5->gestor)) : ?>
                                            <?php foreach ($param5->gestor as $key2 => $dados4) : ?>
                                                <tr>
                                                    <td class="n-mobile-cell"><?= $dados4->nome ?></td>
                                                    <td class="n-mobile-cell" ><?= $dados4->chapa ?></td>
                                                    <td class="n-mobile-cell" >Desconto Autorizado</td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados4->dt_ini) ? $dados4->dt_ini : '' ?>" name="data_inicio_aluguel">
                                                    </td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados4->dt_fim) ? $dados4->dt_fim : '' ?>" name="data_fim_aluguel">
                                                    </td>
                                                    <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="removeRowDesconto(this)">Remover</button></td>
                                        
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-center">
                                <hr>
                                <button class="btn btn-success bteldorado_1" onclick="return save(5)"><i class="fa fa-save"></i> Salvar Parametrização</button>
                            </div>

                        </div>
                        <div class="tab-pane  p-3" id="reembolso_cpd" role="tabpanel">
                            
                            <!-- <div class="form-group">
                                <label for="reembolso_cpd_secao" class="text-primary">Seções:</label>
                                <select id="reembolso_cpd_secao" class="select2 mb-3 select2-multiple form-control-sm" name="reembolso_cpd_secao[]" style="width: 100%" multiple="multiple" data-placeholder="- Todos -">
                                    <?php if($listaSecao): ?>
                                        <?php foreach($listaSecao as $Secao): ?>
                                            <option value="<?= $Secao->CODIGO; ?>" <?= (in_array($Secao->CODIGO, $param4->reembolso_cpd_secao ?? [])) ? 'selected' : ''; ?>><?= $Secao->CODIGO.' - '.$Secao->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div> -->
                            <div class="form-group">
                                <label for="reembolso_cpd_parente" class="text-primary">Parentesco:</label>
                                <select id="reembolso_cpd_parente" class="select2 mb-3 select2-multiple form-control-sm" name="reembolso_cpd_parente[]" style="width: 100%" multiple="multiple" data-placeholder="- Todos -">
                                    <?php if($listaParentesco): ?>
                                        <?php foreach($listaParentesco as $Parentesco): ?>
                                            <option value="<?= $Parentesco->CODCLIENTE; ?>" <?= (in_array($Parentesco->CODCLIENTE, $param4->reembolso_cpd_parente ?? [])) ? 'selected' : ''; ?>><?= $Parentesco->CODCLIENTE.' - '.$Parentesco->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                           

                            <div class="form-group">
                                <label for="reembolso_cpd_evento" class="text-primary">Evento:</label>
                                <select class="select2 custom-select form-control" name="reembolso_cpd_evento" id="reembolso_cpd_evento">
                                    <option value="">:: Evento ::</option>
                                    <?php if($listaEventos): ?>
                                        <?php foreach($listaEventos as $Evento): ?>
                                            <option value="<?= $Evento->CODIGO; ?>" <?= ($Evento->CODIGO == $param4->reembolso_cpd_evento) ? 'selected' : ''; ?>><?= $Evento->CODIGO.' - '.$Evento->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group row">
                                <label for="periodo_pcd" class="col-sm-7 text-primary">Periodo de abertura:</label>
                                <label for="periodo_creche" class="col-sm-5 text-primary">Complementar no Mês:</label>
                                <div class="col-sm-7" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="periodo_pcd"  onchange="validateField(this)" name="periodo_pcd" class="form-control" placeholder="00" value="<?= $param4->periodo_pcd ?? ''; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong>Até</strong></span>
                                        </div>
                                    <input type="number" onchange="validateField(this)" id="periodo_pcd_fim" name="periodo_pcd_fim" class="form-control" placeholder="00" value="<?= $param4->periodo_pcd_fim ?? ''; ?>">
                                    <label for="periodo_pcd_fim" class="col-sm-8 pt-2 col-form-label text-left">Periodo permitido para abertura no mês atual.</label>
                                    </div> 
                                </div>
                                <div class="col-sm-5" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="dia_limite_compl4"  onchange="validateField(this)" name="dia_limite_compl4" class="form-control" placeholder="00" value="<?= $param4->dia_limite_compl4 ?? ''; ?>">
                                    <label for="dia_limite_compl4" class="col-sm-10 pt-2 col-form-label text-left">Dia limite para complementar entrar na folha do mês.</label>
                                    </div> 
                                </div>
                            </div>

                            <div class="form-group "> 
                                <div class="col-sm-12">
                                    <label for="reembolso_cpd_valor_demais_filiais" class="pt-2 col-form-label text-left" style="display:inline-block;">Demais Filiais: Valor máximo por dependente limitado a</label>
                                    <div class="input-group" style="display:inline-flex; width: 200px;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><strong>R$</strong></span>
                                        </div>
                                        <input data-money type="text" id="reembolso_cpd_valor_demais_filiais" name="reembolso_cpd_valor_demais_filiais" class="form-control" placeholder="0.000,00" value="<?= ($param4->reembolso_cpd_valor_demais_filiais) ? moeda($param4->reembolso_cpd_valor_demais_filiais) : ''; ?>">
                                    </div> 
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                    <label for="Filial" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Filial:</label>
                                    <div class="col-sm-8">
                                        <select  class="select2 custom-select form-control form-control-sm" name="Filial" id="Filial">
                                            <option value=""> ... </option>
                                            <?php if ($listaFiliais) : ?>
                                                <?php foreach ($listaFiliais as $key2 => $filial) : ?>
                                                    <option value='<?= $filial->CODFILIAL ?>' ><?= $filial->CODFILIAL ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                   
                                    </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="btnadd" ><i class="fas fa-plus"></i> Adicionar</button>
                                   
                                </div>
                               
                            </div>

                            <div class="form-group row mb-2" id="dependentesTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="dependentesTable">
                                        <thead>
                                            <tr>
                                                <th>Filial</th>
                                                <th>Valor</th> <!-- Nova coluna para o valor -->
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($param4->dependentes)) : ?>
                                        <?php foreach ($param4->dependentes as $key2 => $dados3) : ?>
                                            <tr>
                                                <td class="n-mobile-cell"><?= $dados3->nome ?></td>
                                                <td class="n-mobile-cell" ><input type="text" data-money class=" form-control form-control-sm valorDependente" value="<?= $dados3->valor ?>" placeholder="Valor"></td>
                                                <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="removeRow(this)">Remover</button></td>
                                    
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                    <label for="gestor_pcd" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Exceção:</label>
                                    <div class="col-sm-8">
                                        <select  class="select2 custom-select form-control form-control-sm" name="gestor_pcd" id="gestor_pcd">
                                            <option value=""> ... </option>
                                            <?php if ($resFuncionarioSecao) : ?>
                                                <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                    <option value="<?= $DadosFunc['CHAPA'] ?>" ><?= $DadosFunc['NOME'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                   
                                    </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="btnadd2" ><i class="fas fa-plus"></i> Adicionar</button>
                                   
                                </div>
                               
                            </div>

                            <div class="form-group row mb-2" id="gestorPCDTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="gestorPCDTable">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Chapa</th>
                                                <th>Tipo</th>
                                                <th>Data inicio</th>
                                                <th>Data fim</th> 
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($param4->gestor)) : ?>
                                            <?php foreach ($param4->gestor as $key2 => $dados4) : ?>
                                                <tr>
                                                    <td class="n-mobile-cell"><?= $dados4->nome ?></td>
                                                    <td class="n-mobile-cell" ><?= $dados4->chapa ?></td>
                                                    <td class="n-mobile-cell" >Auxilio Excepcional</td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados4->dt_ini) ? $dados4->dt_ini : '' ?>" name="data_inicio_pcd">
                                                    </td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados4->dt_fim) ? $dados4->dt_fim : '' ?>" name="data_fim_pcd">
                                                    </td>
                                                    <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="removeRow2(this)">Remover</button></td>
                                        
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-center">
                                <hr>
                                <button class="btn btn-success bteldorado_1" onclick="return save(4)"><i class="fa fa-save"></i> Salvar Parametrização</button>
                            </div>

                        </div>
                        <div class="tab-pane  p-3" id="auxilio_creche" role="tabpanel">
                            
                        
                            <div class="form-group">
                                <label for="reembolso_creche_parente" class="text-primary">Parentesco:</label>
                                <select id="reembolso_creche_parente" class="select2 mb-3 select2-multiple form-control-sm" name="reembolso_creche_parente[]" style="width: 100%" multiple="multiple" data-placeholder="- Todos -">
                                    <?php if($listaParentesco): ?>
                                        <?php foreach($listaParentesco as $Parentesco): ?>
                                            <option value="<?= $Parentesco->CODCLIENTE; ?>" <?= (in_array($Parentesco->CODCLIENTE, $param2->reembolso_creche_parente ?? [])) ? 'selected' : ''; ?>><?= $Parentesco->CODCLIENTE.' - '.$Parentesco->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                           

                            <div class="form-group">
                                <label for="reembolso_creche_evento" class="text-primary">Evento:</label>
                                <select class="select2 custom-select form-control" name="reembolso_creche_evento" id="reembolso_creche_evento">
                                    <option value="">:: Evento ::</option>
                                    <?php if($listaEventos): ?>
                                        <?php foreach($listaEventos as $Evento): ?>
                                            <option value="<?= $Evento->CODIGO; ?>" <?= ( isset($param2->reembolso_creche_evento) && $Evento->CODIGO == $param2->reembolso_creche_evento ) ? 'selected' : ''; ?>><?= $Evento->CODIGO.' - '.$Evento->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
          
                            
                            <div class="form-group row">
                                <label for="periodo_creche" class="col-sm-7 text-primary">Periodo de abertura:</label>
                                <label for="periodo_creche" class="col-sm-5 text-primary">Complementar no Mês:</label>
                                <div class="col-sm-7" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="periodo_creche"  onchange="validateField(this)" name="periodo_creche" class="form-control" placeholder="00" value="<?= $param2->periodo_creche ?? ''; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong>Até</strong></span>
                                        </div>
                                    <input type="number" onchange="validateField(this)" id="periodo_creche_fim" name="periodo_creche_fim" class="form-control" placeholder="00" value="<?= $param2->periodo_creche_fim ?? ''; ?>">
                                    <label for="periodo_creche_fim" class="col-sm-8 pt-2 col-form-label text-left">Periodo permitido para abertura no mês atual.</label>
                                    </div> 
                                </div>
                                <div class="col-sm-5" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="dia_limite_compl2"  onchange="validateField(this)" name="dia_limite_compl2" class="form-control" placeholder="00" value="<?= $param2->dia_limite_compl2 ?? ''; ?>">
                                    <label for="dia_limite_compl2" class="col-sm-10 pt-2 col-form-label text-left">Dia limite para complementar entrar na folha do mês.</label>
                                    </div> 
                                </div>
                               
                            </div>
                            <div class="form-group row">
                                <label for="idade_creche" class="col-sm-12 text-primary"> Demais filiais, Regra de idade:</label>
                                <div class="col-sm-2" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="idade_creche"   name="idade_creche" class="form-control" placeholder="00" value="<?= $param2->idade_creche ?? ''; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong>Meses</strong></span>
                                        </div>
                                    </div> 
                                </div>
                               
                            </div>

                            

                            <div class="form-group "> 
                                <div class="col-sm-12">
                                    <label for="reembolso_creche_valor_demais_filiais" class="pt-2 col-form-label text-left" style="display:inline-block;">Demais Filiais: Valor máximo por dependente limitado a</label>
                                    <div class="input-group" style="display:inline-flex; width: 200px;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><strong>R$</strong></span>
                                        </div>
                                        <input data-money type="text" id="reembolso_creche_valor_demais_filiais" name="reembolso_creche_valor_demais_filiais" class="form-control" placeholder="0.000,00" value="<?= (isset($param2->reembolso_creche_valor_demais_filiais)) ? moeda($param2->reembolso_creche_valor_demais_filiais) : ''; ?>">
                                    </div> 
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                    <label for="Filial" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Filial:</label>
                                    <div class="col-sm-8">
                                        <select  class="select2 custom-select form-control form-control-sm" name="FilialCreche" id="FilialCreche">
                                            <option value=""> ... </option>
                                            <?php if ($listaFiliais) : ?>
                                                <?php foreach ($listaFiliais as $key2 => $filial) : ?>
                                                    <option value='<?= $filial->CODFILIAL ?>' ><?= $filial->CODFILIAL ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                   
                                    </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="CrecheAdd" ><i class="fas fa-plus"></i> Adicionar</button>
                                   
                                </div>
                               
                            </div>

                            <div class="form-group row mb-2" id="dependentesCrecheTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="dependentesCrecheTable">
                                        <thead>
                                            <tr>
                                                <th>Filial</th>
                                                <th>Valor</th> <!-- Nova coluna para o valor -->
                                                <th>Idade Máxima em Meses</th> <!-- Nova coluna para o valor -->
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if(isset($param2->dependentes)) : ?>
                                        <?php foreach ($param2->dependentes as $key2 => $dados3) : ?>
                                            <tr>
                                                <td class="n-mobile-cell"><?= $dados3->nome ?></td>
                                                <td class="n-mobile-cell" ><input type="text" data-money class=" form-control form-control-sm valorDependenteCreche" value="<?= $dados3->valor ?>" placeholder="Valor"></td>
                                                <td class="n-mobile-cell" ><input type="number" class=" form-control form-control-sm " value="<?= (isset($dados3->idade)) ? $dados3->idade : ''; ?>" ></td>
                                               
                                                <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="RemoveCrechDepend(this)">Remover</button></td>
                                    
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                    <label for="gestor_creche" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Exceção:</label>
                                    <div class="col-sm-8">
                                        <select  class="select2 custom-select form-control form-control-sm" name="gestor_creche" id="gestor_creche">
                                            <option value=""> ... </option>
                                            <?php if ($resFuncionarioSecao) : ?>
                                                <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                    <option value="<?= $DadosFunc['CHAPA'] ?>" ><?= $DadosFunc['NOME'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                   
                                    </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="CrecheAdd2" ><i class="fas fa-plus"></i> Adicionar</button>
                                   
                                </div>
                               
                            </div>

                            <div class="form-group row mb-2" id="gestorcrecheTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="gestorcrecheTable">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Chapa</th>
                                                <th>Tipo</th>
                                                <th>Data inicio</th>
                                                <th>Data fim</th> 
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($param2->gestor)) : ?>
                                            <?php foreach ($param2->gestor as $key2 => $dados4) : ?>
                                                <tr>
                                                    <td class="n-mobile-cell"><?= $dados4->nome ?></td>
                                                    <td class="n-mobile-cell" ><?= $dados4->chapa ?></td>
                                                    <td class="n-mobile-cell" >Auxilio Creche</td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados4->dt_ini) ? $dados4->dt_ini : '' ?>" name="data_inicio_creche">
                                                    </td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados4->dt_fim) ? $dados4->dt_fim : '' ?>" name="data_fim_creche">
                                                    </td>
                                                    <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="removeRowCreche(this)">Remover</button></td>
                                        
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-center">
                                <hr>
                                <button class="btn btn-success bteldorado_1" onclick="return save(2)"><i class="fa fa-save"></i> Salvar Parametrização</button>
                            </div>

                        </div>
                        <div class="tab-pane active  p-3" id="auxilio_aluguel" role="tabpanel">
                            
        

                            <div class="form-group">
                                <label for="reembolso_aluguel_evento" class="text-primary">Evento:</label>
                                <select class="select2 custom-select form-control" name="reembolso_aluguel_evento" id="reembolso_aluguel_evento">
                                    <option value="">:: Evento ::</option>
                                    <?php if($listaEventos): ?>
                                        <?php foreach($listaEventos as $Evento): ?>
                                            <option value="<?= $Evento->CODIGO; ?>" <?= (isset($param7->reembolso_aluguel_evento) && $Evento->CODIGO == $param7->reembolso_aluguel_evento ) ? 'selected' : ''; ?>><?= $Evento->CODIGO.' - '.$Evento->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group row">
                                <label for="periodo_aluguel" class="col-sm-12 text-primary">Periodo de abertura:</label>
                                <div class="col-sm-7" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="periodo_aluguel"  onchange="validateField(this)" name="periodo_aluguel" class="form-control" placeholder="00" value="<?= $param7->periodo_aluguel ?? ''; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong>Até</strong></span>
                                        </div>
                                    <input type="number" onchange="validateField(this)" id="periodo_aluguel_fim" name="periodo_aluguel_fim" class="form-control" placeholder="00" value="<?= $param7->periodo_aluguel_fim ?? ''; ?>">
                                    <label for="periodo_aluguel_fim" class="col-sm-8 pt-2 col-form-label text-left">Periodo permitido para abertura no mês atual.</label>
                                    </div> 
                                </div>
                               
                            </div>

                            <div class="form-group "> 
                                <div class="col-sm-12">
                                    <label for="reembolso_cpd_valor_demais_filiais" class="pt-2 col-form-label text-left" style="display:inline-block;">Demais Filiais:</label>
                                    <div class="input-group" style="display:inline-flex; width: 200px;">
                                        
                                        <input  type="number" id="reembolso_aluguel_valor_demais_filiais" name="reembolso_aluguel_valor_demais_filiais" class="form-control" placeholder="3" value="<?= (isset($param7->reembolso_aluguel_valor_demais_filiais)) ? $param7->reembolso_aluguel_valor_demais_filiais : ''; ?>">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><strong>Meses</strong></span>
                                        </div>
                                    </div> 
                                </div>
                            </div>

                           

                            
                            <div class="form-group row mb-2">

                                    <label for="FilialAlugue" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Meses por Filial:</label>
                                    <div class="col-sm-8">
                                        <select  class="select2 custom-select form-control form-control-sm" name="FilialAlugue" id="FilialAlugue">
                                            <option value=""> ... </option>
                                            <?php if ($listaFiliais) : ?>
                                                <?php foreach ($listaFiliais as $key2 => $filial) : ?>
                                                    <option value='<?= $filial->CODFILIAL ?>' ><?= $filial->CODFILIAL ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                   
                                    </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="AluguelFilial" ><i class="fas fa-plus"></i> Adicionar</button>
                                   
                                </div>
                               
                            </div>

                            <div class="form-group row mb-2" id="aluguelTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="aluguelTable">
                                        <thead>
                                            <tr>
                                                <th>Filial</th>
                                                <th>Meses</th> <!-- Nova coluna para o valor -->
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php    if(isset($param7->aluguel))  : ?>
                                        <?php foreach ($param7->aluguel as $key2 => $dados3) : ?>
                                            <tr>
                                                <td class="n-mobile-cell"><?= $dados3->nome ?></td>
                                                <td class="n-mobile-cell" ><input type="number"  class=" form-control form-control-sm valorDependente" value="<?= $dados3->valor ?>" placeholder="Quantidade de Meses"></td>
                                                <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="removeRowAluguel2(this)">Remover</button></td>
                                    
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                         

                            <div class="form-group row mb-2">
                                    <label for="CargoAluguel" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Valores por Filial:</label>
                                    <div class="col-sm-8">
                                        <select  class="select2 custom-select form-control form-control-sm" name="CargoAluguel" id="CargoAluguel">
                                            <option value=""> ... </option>
                                            <?php if ($listaFiliais) : ?>
                                                <?php foreach ($listaFiliais as $key2 => $filial) : ?>
                                                    <option value='<?= $filial->CODFILIAL ?>' ><?= $filial->CODFILIAL ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                   
                                    </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="AluguelAdd" ><i class="fas fa-plus"></i> Adicionar</button>
                                   
                                </div>
                               
                            </div>

                            <div class="form-group row mb-2" id="dependentesAluguelTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="dependentesAluguelTable">
                                        <thead>
                                            <tr>
                                                <th>Filial</th>
                                                <th>Valor Minimo</th> <!-- Nova coluna para o valor -->
                                                <th>Valor Maximo</th> <!-- Nova coluna para o valor -->
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if(isset($param7->dependentes)) : ?>
                                        <?php foreach ($param7->dependentes as $key2 => $dados3) : ?>
                                            <tr>
                                                <td class="n-mobile-cell"><?= $dados3->codigo ?></td>
                                                <td class="n-mobile-cell" ><input type="text" data-money class=" form-control form-control-sm valorDependenteAluguel" value="<?= $dados3->valor_min ?>" placeholder="Valor"></td>
                                                <td class="n-mobile-cell" ><input type="text" data-money class=" form-control form-control-sm valorDependenteAluguel" value="<?= $dados3->valor_max ?>" placeholder="Valor"></td>
                                                <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="RemoveAluguelDepend(this)">Remover</button></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                    <label for="gestor_aluguel" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Exceção Gestor:</label>
                                    <div class="col-sm-8">
                                        <select  class="select2 custom-select form-control form-control-sm" name="gestor_aluguel" id="gestor_aluguel">
                                            <option value=""> ... </option>
                                            <?php if ($resFuncionarioSecao) : ?>
                                                <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                    <option value="<?= $DadosFunc['CHAPA'] ?>" ><?= $DadosFunc['NOME'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                   
                                    </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="AluguelAdd2" ><i class="fas fa-plus"></i> Adicionar</button>
                                   
                                </div>
                               
                            </div>

                            <div class="form-group row mb-2" id="gestoraluguelTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="gestoraluguelTable">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Chapa</th>
                                                <th>Tipo</th>
                                                <th>Data inicio</th>
                                                <th>Data fim</th> 
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($param7->gestor)) : ?>
                                            <?php foreach ($param7->gestor as $key2 => $dados4) : ?>
                                                <tr>
                                                    <td class="n-mobile-cell"><?= $dados4->nome ?></td>
                                                    <td class="n-mobile-cell" ><?= $dados4->chapa ?></td>
                                                    <td class="n-mobile-cell" >Auxilio Aluguel</td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados4->dt_ini) ? $dados4->dt_ini : '' ?>" name="data_inicio_aluguel">
                                                    </td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados4->dt_fim) ? $dados4->dt_fim : '' ?>" name="data_fim_aluguel">
                                                    </td>
                                                    <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="removeRowAluguel(this)">Remover</button></td>
                                        
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-center">
                                <hr>
                                <button class="btn btn-success bteldorado_1" onclick="return save(7)"><i class="fa fa-save"></i> Salvar Parametrização</button>
                            </div>

                        </div>
                        <div class="tab-pane  p-3" id="coparticipacao" role="tabpanel">
                        
                          

                            <div class="form-group">
                                <label for="auxilio_coparticipacao_evento" class="text-primary">Evento Unimed:</label>
                                <select class="select2 custom-select form-control" name="auxilio_coparticipacao_evento" id="auxilio_coparticipacao_evento">
                                    <option value="">:: Evento ::</option>
                                    <?php if($listaEventos): ?>
                                        <?php foreach($listaEventos as $Evento): ?>
                                            <option value="<?= $Evento->CODIGO; ?>" <?= (isset($param8->auxilio_coparticipacao_evento) && $Evento->CODIGO == $param8->auxilio_coparticipacao_evento) ? 'selected' : ''; ?>><?= $Evento->CODIGO.' - '.$Evento->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="auxilio_coparticipacao2_evento" class="text-primary">Evento Bradesco:</label>
                                <select class="select2 custom-select form-control" name="auxilio_coparticipacao2_evento" id="auxilio_coparticipacao2_evento">
                                    <option value="">:: Evento ::</option>
                                    <?php if($listaEventos): ?>
                                        <?php foreach($listaEventos as $Evento): ?>
                                            <option value="<?= $Evento->CODIGO; ?>" <?= (isset($param8->auxilio_coparticipacao2_evento) && $Evento->CODIGO == $param8->auxilio_coparticipacao2_evento) ? 'selected' : ''; ?>><?= $Evento->CODIGO.' - '.$Evento->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="form-group row">
                                <label for="periodo_coparticipacao" class="col-sm-12 text-primary">Periodo de abertura:</label>
                                <div class="col-sm-7" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number"  onchange="validateField(this)" id="periodo_coparticipacao" name="periodo_coparticipacao" class="form-control" placeholder="00" value="<?= $param8->periodo_coparticipacao ?? ''; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong>Até</strong></span>
                                        </div>
                                    <input type="number"  onchange="validateField(this)" id="periodo_coparticipacao_fim" name="periodo_coparticipacao_fim" class="form-control" placeholder="00" value="<?= $param8->periodo_coparticipacao_fim ?? ''; ?>">
                                    <label for="auxilio_coparticipacao_porcentagem" class="col-sm-8 pt-2 col-form-label text-left">Periodo permitido para abertura no mês atual.</label>
                                    </div> 
                                </div>
                               
                            </div>
                            <div class="form-group row mb-2">
                                    <label for="gestor_pcd" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Exceção:</label>
                                    <div class="col-sm-8">
                                        <select  class="select2 custom-select form-control form-control-sm" name="gestor_coparticipacao" id="gestor_coparticipacao">
                                            <option value=""> ... </option>
                                            <?php if ($resFuncionarioSecao) : ?>
                                                <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                    <option value="<?= $DadosFunc['CHAPA'] ?>" ><?= $DadosFunc['NOME'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                   
                                    </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="btnaddCopart" ><i class="fas fa-plus"></i> Adicionar</button>
                                   
                                </div>
                               
                            </div>

                            <div class="form-group row mb-2" id="gestorcoparticipacaoTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="gestorcoparticipacaoTable">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Chapa</th>
                                                <th>Tipo</th>
                                                <th>Data inicio</th>
                                                <th>Data fim</th> 
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($param8->gestor)) : ?>
                                            <?php foreach ($param8->gestor as $key2 => $dados6) : ?>
                                                <tr>
                                                    <td class="n-mobile-cell"><?= $dados6->nome ?></td>
                                                    <td class="n-mobile-cell" ><?= $dados6->chapa ?></td>
                                                    <td class="n-mobile-cell" >Coparticipação</td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados6->dt_ini) ? $dados6->dt_ini : '' ?>" name="data_inicio_copart">
                                                    </td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados6->dt_fim) ? $dados6->dt_fim : '' ?>" name="data_fim_copart">
                                                    </td>

                                                    <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="removeRowCopart(this)">Remover</button></td>
                                        
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-center">
                                <hr>
                                <button class="btn btn-success bteldorado_1" onclick="return save(8)"><i class="fa fa-save"></i> Salvar Parametrização</button>
                            </div>
                            

                        </div>
                        <!--/ auxilio moradia -->

                        <div class="tab-pane p-3" id="salario" role="tabpanel">
                        
                            <div class="form-group row">
                                <label for="auxilio_13salario_evento" class="col-sm-12 text-primary">Regra de cálculo:</label>
                                <div class="col-sm-2" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="number" id="auxilio_13salario_porcentagem" disabled name="auxilio_13salario_porcentagem" class="form-control" placeholder="00" value="<?= $param9->auxilio_13salario_porcentagem ?? '50'; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong>%</strong></span>
                                        </div>
                                    </div> 
                                </div>
                                <label for="auxilio_13salario_porcentagem" class="col-sm-10 pt-2 col-form-label text-left">do salário base do colaborador.</label>
                            </div>

                            <div class="form-group">
                                <label for="auxilio_13salario_evento" class="text-primary">Evento:</label>
                                <select class="select2 custom-select form-control" name="auxilio_13salario_evento" id="auxilio_13salario_evento">
                                    <option value="">:: Evento ::</option>
                                    <?php if($listaEventos): ?>
                                        <?php foreach($listaEventos as $Evento): ?>
                                            <option value="<?= $Evento->CODIGO; ?>" <?= (isset($param9->auxilio_13salario_evento) && $Evento->CODIGO == $param9->auxilio_13salario_evento) ? 'selected' : ''; ?>><?= $Evento->CODIGO.' - '.$Evento->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group row">
                                <label for="periodo_13salario" class="col-sm-12 text-primary">Periodo de abertura:</label>
                                <div class="col-sm-12" style="width:100px !important;">
                                    <div class="input-group">
                                    <input type="date"   id="periodo_13salario" name="periodo_13salario" class="form-control" placeholder="00" value="<?= $param9->periodo_13salario ?? ''; ?>">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><strong>Até</strong></span>
                                        </div>
                                    <input type="date" id="periodo_13salario_fim" name="periodo_13salario_fim" class="form-control" placeholder="00" value="<?= $param9->periodo_13salario_fim ?? ''; ?>">
                                    <label for="auxilio_13salario_porcentagem" class="col-sm-5 pt-2 col-form-label text-left">Periodo permitido para abertura.</label>
                                    </div> 
                                </div>
                            
                            </div>
                            <div class="form-group row mb-2">
                                    <label for="gestor_pcd" class="col-sm-2 col-form-label text-right text-primary text-left-sm">Exceção:</label>
                                    <div class="col-sm-8">
                                        <select  class="select2 custom-select form-control form-control-sm" name="gestor_13salario" id="gestor_13salario">
                                            <option value=""> ... </option>
                                            <?php if ($resFuncionarioSecao) : ?>
                                                <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                    <option value="<?= $DadosFunc['CHAPA'] ?>" ><?= $DadosFunc['NOME'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                
                                    </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="btnaddSalario" ><i class="fas fa-plus"></i> Adicionar</button>
                                
                                </div>
                            
                            </div>

                            <div class="form-group row mb-2" id="gestor13salarioTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="gestor13salarioTable">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Chapa</th>
                                                <th>Tipo</th>
                                                <th>Data inicio</th>
                                                <th>Data fim</th> 
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($param9->gestor)) : ?>
                                            <?php foreach ($param9->gestor as $key2 => $dados6) : ?>
                                                <tr>
                                                    <td class="n-mobile-cell"><?= $dados6->nome ?></td>
                                                    <td class="n-mobile-cell" ><?= $dados6->chapa ?></td>
                                                    <td class="n-mobile-cell" > 13 salario</td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados6->dt_ini) ? $dados6->dt_ini : '' ?>" name="data_inicio_moradia">
                                                    </td>
                                                    <td class="n-mobile-cell">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= isset($dados6->dt_fim) ? $dados6->dt_fim : '' ?>" name="data_fim_moradia">
                                                    </td>

                                                    <td class="n-mobile-cell"><button class="btn btn-danger btn-sm" onclick="removeRow13salario(this)">Remover</button></td>
                                        
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-center">
                                <hr>
                                <button class="btn btn-success bteldorado_1" onclick="return save(9)"><i class="fa fa-save"></i> Salvar Parametrização</button>
                            </div>
                            

                        </div>
                   
                    </div>

                    

                    
                </div>
			</div><!-- end card -->
		</div><!-- end col-12 -->
		
	</div><!-- end row -->
</div><!-- end container -->

<style>
.tab-pane {
    border-left: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    border-right: 1px solid #dee2e6;
}
.select2-container--default .select2-selection--single {
    border: 1px solid #d8d8d8 !important;
    padding: 5px 0;
    height: 39px !important;
}
div:where(.swal2-icon).swal2-error {
    color: #fd7e14 !important;
    border-color: #fd7e14 !important;
}
div:where(.swal2-icon).swal2-error [class^=swal2-x-mark-line] {
    background-color: #fd7e14 !important;
}
.swal2-popup .swal2-styled.swal2-confirm {
    background-color: #225c30 !important;
    padding: 6px 20px;
}
</style>
<script>
    const save = (tipo) => {

        switch(tipo){
            case 1: 
                var dados = validaTipo1();
                if(!dados) return;
            break;
            case 2: 
                var dados = validaTipo2();
                if(!dados) return;
            break;
            case 3: 
                var dados = validaTipo3();
                if(!dados) return;
            break;
            case 4: 
                var dados = validaTipo4();
                if(!dados) return;
            break;
            case 5: 
                var dados = validaTipo5();
                if(!dados) return;
            break;
            case 6: 
                var dados = validaTipo6();
                if(!dados) return;
            break;
            case 7: 
                var dados = validaTipo7();
                if(!dados) return;
            break;
            case 8: 
                var dados = validaTipo8();
                if(!dados) return;
            break;
            case 9: 
                var dados = validaTipo9();
                if(!dados) return;
            break;
        }
        console.log(dados);
        $.ajax({
            url: base_url+'/variaveis/parametrizacao/save',
            type: "POST",
            data: dados,
            beforeSend: function () {
                Swal.fire({
                title            : 'Salvando dados',
                html             : 'Por favor, aguarde...',
                allowEscapeKey   : false,
                allowOutsideClick: false,
                didOpen          : () => {
                    Swal.showLoading();
                }
                })
            },
            success: function (response) {
                console.log(response);
                response=JSON.parse(response);
                Swal.fire( 
                    '',
                    response.msg, 
                    response.tipo
                );
            }    
        });

    }

    function validateField(inputElement) {
        const value = parseInt($(inputElement).val());

        // Verifica se o valor está entre 1 e 31
        if (value < 1 || value > 31) {
            // Exibe um alerta usando SweetAlert2
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'O valor não é um dia valido',
            });
            $(inputElement).val(''); // Limpa o valor do campo inválido
            return false; // Retorna falso se a validação falhar
        }
        return true; // Retorna verdadeiro se a validação passar
    }
    document.getElementById('btnadd').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('Filial');
       
    
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {

            var tableBody = document.getElementById('dependentesTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[0].innerText; 
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Filial já existe nessa tabela',
                });
                return false;
            }
            dependenteValue = JSON.parse(dependenteValue);
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('dependentesTableContainer');
            tableContainer.style.display = "block";
            
            // Adiciona uma nova linha na tabela
           
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
           
            // Dados de exemplo (você pode substituir pelos dados reais)
            cell1.innerHTML = dependenteText;  // Nome do dependente 
            cell2.innerHTML = '<input type="text" data-money class=" form-control form-control-sm valorDependente" placeholder="Valor">';  // Input para valor
            cell3.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRow(this)">Remover</button>';
        
            $(cell2).find('input[data-money]').maskMoney({
                prefix: '',
                allowNegative: false,
                allowZero: true,
                thousands: '.',
                decimal: ',',
                affixesStay: false
            });
        }
    });
    
    document.getElementById('DescontoFilial').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('FilialDesconto');
       
    
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {

            var tableBody = document.getElementById('descontoTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[0].innerText; 
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Filial já existe nessa tabela',
                });
                return false;
            }
            dependenteValue = JSON.parse(dependenteValue);
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('descontoTableContainer');
            tableContainer.style.display = "block";
            
            // Adiciona uma nova linha na tabela
           
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
           
            // Dados de exemplo (você pode substituir pelos dados reais)
            cell1.innerHTML = dependenteText;  // Nome do dependente 
            cell2.innerHTML = '<input type="number"  class=" form-control form-control-sm " placeholder="Quantidade de Meses">';  // Input para valor
            cell3.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRowDesconto2(this)">Remover</button>';
        
            $(cell2).find('input[data-money]').maskMoney({
                prefix: '',
                allowNegative: false,
                allowZero: true,
                thousands: '.',
                decimal: ',',
                affixesStay: false
            });
        }
    });

    document.getElementById('AluguelFilial').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('FilialAlugue');
       
    
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {

            var tableBody = document.getElementById('aluguelTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[0].innerText; 
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Filial já existe nessa tabela',
                });
                return false;
            }
            dependenteValue = JSON.parse(dependenteValue);
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('aluguelTableContainer');
            tableContainer.style.display = "block";
            
            // Adiciona uma nova linha na tabela
           
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
           
            // Dados de exemplo (você pode substituir pelos dados reais)
            cell1.innerHTML = dependenteText;  // Nome do dependente 
            cell2.innerHTML = '<input type="number"  class=" form-control form-control-sm " placeholder="Quantidade de Meses">';  // Input para valor
            cell3.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRowAluguel2(this)">Remover</button>';
        
            $(cell2).find('input[data-money]').maskMoney({
                prefix: '',
                allowNegative: false,
                allowZero: true,
                thousands: '.',
                decimal: ',',
                affixesStay: false
            });
        }
    });

    document.getElementById('CrecheAdd').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('FilialCreche');
       
    
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {

            var tableBody = document.getElementById('dependentesCrecheTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[0].innerText; 
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Filial já existe nessa tabela',
                });
                return false;
            }
            dependenteValue = JSON.parse(dependenteValue);
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('dependentesCrecheTableContainer');
            tableContainer.style.display = "block";
            
            // Adiciona uma nova linha na tabela
          
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
           
            // Dados de exemplo (você pode substituir pelos dados reais)
            cell1.innerHTML = dependenteText;  // Nome do dependente 
            cell2.innerHTML = '<input type="text" data-money class=" form-control form-control-sm valorDependente" placeholder="Valor">';  // Input para valor
            cell3.innerHTML = '<input type="number"  class="form-control form-control-sm " >';  // Input para val
            cell4.innerHTML = '<button class="btn btn-danger btn-sm" onclick="RemoveCrechDepend(this)">Remover</button>';
        
            $(cell2).find('input[data-money]').maskMoney({
                prefix: '',
                allowNegative: false,
                allowZero: true,
                thousands: '.',
                decimal: ',',
                affixesStay: false
            });
        }
    });

    document.getElementById('AluguelAdd').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('CargoAluguel');
       
    
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {

            var tableBody = document.getElementById('dependentesAluguelTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[0].innerText; 
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Filial já existe nessa tabela',
                });
                return false;
            }
           
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('dependentesAluguelTableContainer');
            tableContainer.style.display = "block";
            
            // Adiciona uma nova linha na tabela
          
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
           
            cell1.innerHTML = dependenteValue;  // Nome do dependente
            cell2.innerHTML = '<input type="text" data-money class=" form-control form-control-sm valorDependente" placeholder="Valor">';  // Input para valor
            cell3.innerHTML = '<input type="text" data-money class=" form-control form-control-sm valorDependente" placeholder="Valor">';  // Input para valor
            cell4.innerHTML = '<button class="btn btn-danger btn-sm" onclick="RemoveAluguelDepend(this)">Remover</button>';
        
            $(cell2).find('input[data-money]').maskMoney({
                prefix: '',
                allowNegative: false,
                allowZero: true,
                thousands: '.',
                decimal: ',',
                affixesStay: false
            });
            $(cell3).find('input[data-money]').maskMoney({
                prefix: '',
                allowNegative: false,
                allowZero: true,
                thousands: '.',
                decimal: ',',
                affixesStay: false
            });
        }
    });
    
    document.getElementById('CrecheAdd2').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('gestor_creche');
       
       
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {
            var tableBody = document.getElementById('gestorcrecheTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[1].innerText; // CPF está na célula 2 (índice 1)
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Usuário já existe nessa excessão',
                });
                return false;
            }
        
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('gestorcrecheTableContainer');
            tableContainer.style.display = "block";
            
            // Adiciona uma nova linha na tabela
           
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
            var cell5 = newRow.insertCell(4);
            var cell6 = newRow.insertCell(5);
           
            // Dados de exemplo (você pode substituir pelos dados reais)
            cell1.innerHTML = dependenteText;  // Nome do dependente 
            cell2.innerHTML = dependenteValue;  // Nome do dependente
            cell3.innerHTML = 'Auxílio Creche';   
            cell4.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_inicio_creche">';  // Input para valor
            cell5.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_fim_creche">';  // Input para valor
            cell6.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRowCreche(this)">Remover</button>';
        
         
        }
        $('#gestor_creche').val('').trigger('change');
    });
    
    document.getElementById('DescontoAdd2').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('gestor_desconto');
       
       
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {
            var tableBody = document.getElementById('gestordescontoTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[1].innerText; // CPF está na célula 2 (índice 1)
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Usuário já existe nessa excessão',
                });
                return false;
            }
        
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('gestordescontoTableContainer');
            tableContainer.style.display = "block";
         
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
            var cell5 = newRow.insertCell(4);
            var cell6 = newRow.insertCell(5);
           
            // Dados de exemplo (você pode substituir pelos dados reais)
            cell1.innerHTML = dependenteText;  // Nome do dependente 
            cell2.innerHTML = dependenteValue;  // Nome do dependente
            cell3.innerHTML = 'Desconto Autorizado';   
            cell4.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_inicio_desconto">';  // Input para valor
            cell5.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_fim_desconto">';  // Input para valor
            cell6.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRowDesconto(this)">Remover</button>';
        
         
        }
        $('#gestor_desconto').val('').trigger('change');
    });
    

    document.getElementById('AluguelAdd2').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('gestor_aluguel');
       
       
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {
            var tableBody = document.getElementById('gestoraluguelTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[1].innerText; // CPF está na célula 2 (índice 1)
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Usuário já existe nessa excessão',
                });
                return false;
            }
        
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('gestoraluguelTableContainer');
            tableContainer.style.display = "block";
         
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
            var cell5 = newRow.insertCell(4);
            var cell6 = newRow.insertCell(5);
           
            // Dados de exemplo (você pode substituir pelos dados reais)
            cell1.innerHTML = dependenteText;  // Nome do dependente 
            cell2.innerHTML = dependenteValue;  // Nome do dependente
            cell3.innerHTML = 'Auxílio Aluguel';   
            cell4.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_inicio_aluguel">';  // Input para valor
            cell5.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_fim_aluguel">';  // Input para valor
            cell6.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRowAluguel(this)">Remover</button>';
        
         
        }
        $('#gestor_aluguel').val('').trigger('change');
    });
    
    document.getElementById('btnadd2').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('gestor_pcd');
       
       
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {
            var tableBody = document.getElementById('gestorPCDTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[1].innerText; 
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Usuário já existe nessa excessão',
                });
                return false;
            }
        
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('gestorPCDTableContainer');
            tableContainer.style.display = "block";
            
            // Adiciona uma nova linha na tabela
            var tableBody = document.getElementById('gestorPCDTable').getElementsByTagName('tbody')[0];
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
            var cell5 = newRow.insertCell(4);
            var cell6 = newRow.insertCell(5);
           
            // Dados de exemplo (você pode substituir pelos dados reais)
            cell1.innerHTML = dependenteText;  // Nome do dependente 
            cell2.innerHTML = dependenteValue;  // Nome do dependente
            cell3.innerHTML = 'Auxílio Excepicional';   
            cell4.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_inicio_pcd">';  // Input para valor
            cell5.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_fim_pcd">';  // Input para valor
            cell6.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRow2(this)">Remover</button>';
        
         
        }
        $('#gestor_pcd').val('').trigger('change');
    });
    document.getElementById('btnaddsubstituicao').addEventListener('click', function(e) {
        e.preventDefault();
       
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('gestor_substituicao');
       
    
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {
            var tableBody = document.getElementById('gestorsubstituicaoTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[1].innerText; // CPF está na célula 2 (índice 1)
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Usuário já existe nessa excessão',
                });
                return false;
            }
            
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('gestorsubstituicaoTableContainer');
            tableContainer.style.display = "block";
            
            // Adiciona uma nova linha na tabela
            var tableBody = document.getElementById('gestorsubstituicaoTable').getElementsByTagName('tbody')[0];
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
            var cell5 = newRow.insertCell(4);
            var cell6 = newRow.insertCell(5);
           
           
            cell1.innerHTML = dependenteText; 
            cell2.innerHTML = dependenteValue;   
            cell3.innerHTML = 'Substituição';  
            cell4.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_inicio_pcd">';  // Input para valor
            cell5.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_fim_pcd">';  // Input para valor
            cell6.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRowsubstituicao(this)">Remover</button>';
        
         
        }
        $('#gestor_substituicao').val('').trigger('change');
    });
    document.getElementById('btnaddSobreaviso').addEventListener('click', function(e) {
        e.preventDefault();
       
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('gestor_sobreaviso');
       
    
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {
            var tableBody = document.getElementById('gestorsobreavisoTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[1].innerText; // CPF está na célula 2 (índice 1)
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Usuário já existe nessa excessão',
                });
                return false;
            }
            
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('gestorsobreavisoTableContainer');
            tableContainer.style.display = "block";
            
            // Adiciona uma nova linha na tabela
            var tableBody = document.getElementById('gestorsobreavisoTable').getElementsByTagName('tbody')[0];
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
            var cell5 = newRow.insertCell(4);
            var cell6 = newRow.insertCell(5);
           
           
            cell1.innerHTML = dependenteText; 
            cell2.innerHTML = dependenteValue;   
            cell3.innerHTML = 'Sobreaviso';  
            cell4.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_inicio_pcd">';  // Input para valor
            cell5.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_fim_pcd">';  // Input para valor
            cell6.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRowSobreaviso(this)">Remover</button>';
        
         
        }
        $('#gestor_sobreaviso').val('').trigger('change');
    });

    document.getElementById('btnadd3').addEventListener('click', function(e) {
        e.preventDefault();
       
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('gestor_moradia');
       
    
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {
            var tableBody = document.getElementById('gestorMoradiaTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[1].innerText; // CPF está na célula 2 (índice 1)
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Usuário já existe nessa excessão',
                });
                return false;
            }
            
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('gestorMoradiaTableContainer');
            tableContainer.style.display = "block";
            
            // Adiciona uma nova linha na tabela
            var tableBody = document.getElementById('gestorMoradiaTable').getElementsByTagName('tbody')[0];
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
            var cell5 = newRow.insertCell(4);
            var cell6 = newRow.insertCell(5);
           
           
            cell1.innerHTML = dependenteText; 
            cell2.innerHTML = dependenteValue;   
            cell3.innerHTML = 'Auxílio Moradia';  
            cell4.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_inicio_pcd">';  // Input para valor
            cell5.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_fim_pcd">';  // Input para valor
            cell6.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRow3(this)">Remover</button>';
        
         
        }
        $('#gestor_moradia').val('').trigger('change');
    });

    document.getElementById('btnaddSalario').addEventListener('click', function(e) {
        e.preventDefault();
       
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('gestor_13salario');
       
    
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {
            var tableBody = document.getElementById('gestor13salarioTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[1].innerText; // CPF está na célula 2 (índice 1)
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Usuário já existe nessa excessão',
                });
                return false;
            }
            
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('gestor13salarioTableContainer');
            tableContainer.style.display = "block";
            
            // Adiciona uma nova linha na tabela
            var tableBody = document.getElementById('gestor13salarioTable').getElementsByTagName('tbody')[0];
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
            var cell5 = newRow.insertCell(4);
            var cell6 = newRow.insertCell(5);
           
           
            cell1.innerHTML = dependenteText; 
            cell2.innerHTML = dependenteValue;   
            cell3.innerHTML = 'Auxílio 13salario';  
            cell4.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_inicio_salario">';  // Input para valor
            cell5.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_fim_salario">';  // Input para valor
            cell6.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRow13salario(this)">Remover</button>';
        
         
        }
        $('#gestor_13salario').val('').trigger('change');
    });

    document.getElementById('btnaddCopart').addEventListener('click', function(e) {
        e.preventDefault();
       
        // Obtém o dependente selecionado
        var dependenteSelect = document.getElementById('gestor_coparticipacao');
       
    
        var dependenteValue = dependenteSelect.value;
        
        var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
    
        // Verifica se um dependente foi selecionado
        if (dependenteValue !== "") {
            var tableBody = document.getElementById('gestorcoparticipacaoTable').getElementsByTagName('tbody')[0];
            var rows = tableBody.getElementsByTagName('tr');
            var exists = false;
            
            for (var i = 0; i < rows.length; i++) {
                var cellValue = rows[i].getElementsByTagName('td')[1].innerText; // CPF está na célula 2 (índice 1)
                if (cellValue === dependenteValue) {
                    exists = true;
                    break;
                }
            }
            
            if (exists) {
                Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Usuário já existe nessa excessão',
                });
                return false;
            }
            
            // Exibe a tabela se estiver oculta
            var tableContainer = document.getElementById('gestorcoparticipacaoTableContainer');
            tableContainer.style.display = "block";
            
            // Adiciona uma nova linha na tabela
            var tableBody = document.getElementById('gestorcoparticipacaoTable').getElementsByTagName('tbody')[0];
            var newRow = tableBody.insertRow();

            // Adicione os dados nas células
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
            var cell5 = newRow.insertCell(4);
            var cell6 = newRow.insertCell(5);
           
           
            cell1.innerHTML = dependenteText; 
            cell2.innerHTML = dependenteValue;   
            cell3.innerHTML = 'Coparticipação';  
            cell4.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_inicio_pcd">';  // Input para valor
            cell5.innerHTML = '<input class="form-control datepicker m_data" type="date" value="" name="data_fim_pcd">';  // Input para valor
            cell6.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRow3(this)">Remover</button>';
        
         
        }
        $('#gestor_coparticipacao').val('').trigger('change');
    });

    const validaTipo4 = () => {

        var dados = {
            'tipo': 4,
            'reembolso_cpd_valor_demais_filiais': $('#reembolso_cpd_valor_demais_filiais').val().replaceAll('.', '').replaceAll(',', '.'),
            'reembolso_cpd_evento': $('#reembolso_cpd_evento').val(),
            'reembolso_cpd_secao': $('#reembolso_cpd_secao').val(),
            'reembolso_cpd_parente': $('#reembolso_cpd_parente').val(),
            'periodo_pcd_fim': $('#periodo_pcd_fim').val(),
            'periodo_pcd': $('#periodo_pcd').val(),
            'dia_limite_compl4': $('#dia_limite_compl4').val(),
            "dependentes": [], // Adicionando o array de dependentes
            "gestor": [] // Adicionando o array de dependentes
        }

        $("#dependentesTable tbody tr").each(function() {
            let dependente = {
                "nome": $(this).find("td:eq(0)").text(),
                "valor": $(this).find("td:eq(1) input").val() // Captura o valor do input
            };
            dados.dependentes.push(dependente);
        });
        $("#gestorPCDTable tbody tr").each(function() {
            let gestor = {
                "nome": $(this).find("td:eq(0)").text(),
                "chapa": $(this).find("td:eq(1)").text(),
                "dt_ini": $(this).find("td:eq(3) input").val(), // Captura o valor do input
                "dt_fim": $(this).find("td:eq(4) input").val() // Captura o valor do input
            };
            dados.gestor.push(gestor);
        });

       
        if(dados.reembolso_cpd_valor_demais_filiais == ''){exibeAlerta('error', 'Valor das demais filiais não informado.'); return false;}
        if(dados.reembolso_cpd_evento == ''){exibeAlerta('error', 'Evento do reembolso pcd não informado.'); return false;}

        return dados;
        
    }

    const validaTipo2 = () => {

        var dados = {
            'tipo': 2,
            'reembolso_creche_valor_demais_filiais': $('#reembolso_creche_valor_demais_filiais').val().replaceAll('.', '').replaceAll(',', '.'),
            'reembolso_creche_evento': $('#reembolso_creche_evento').val(),
            'reembolso_creche_secao': $('#reembolso_creche_secao').val(),
            'reembolso_creche_parente': $('#reembolso_creche_parente').val(),
            'periodo_creche_fim': $('#periodo_creche_fim').val(),
            'periodo_creche': $('#periodo_creche').val(),
            'dia_limite_compl2': $('#dia_limite_compl2').val(),
            'idade_creche': $('#idade_creche').val(),
            "dependentes": [], // Adicionando o array de dependentes
            "gestor": [] // Adicionando o array de dependentes
        }

        $("#dependentesCrecheTable tbody tr").each(function() {
            let dependente = {
                "nome": $(this).find("td:eq(0)").text(),
                "valor": $(this).find("td:eq(1) input").val(), // Captura o valor do input
                "idade": $(this).find("td:eq(2) input").val() // Captura o valor do input
            };
            dados.dependentes.push(dependente);
        });
        $("#gestorcrecheTable tbody tr").each(function() {
            let gestor = {
                "nome": $(this).find("td:eq(0)").text(),
                "chapa": $(this).find("td:eq(1)").text(),
                "dt_ini": $(this).find("td:eq(3) input").val(), // Captura o valor do input
                "dt_fim": $(this).find("td:eq(4) input").val() // Captura o valor do input
            };
            dados.gestor.push(gestor);
        });

      
        if(dados.reembolso_creche_valor_demais_filiais == ''){exibeAlerta('error', 'Valor das demais filiais não informado.'); return false;}
        if(dados.reembolso_creche_evento == ''){exibeAlerta('error', 'Evento do reembolso creche não informado.'); return false;}

        return dados;

    }
    const validaTipo9 = () => {
        var dados = {
            'tipo': 9,
            'auxilio_13salario_porcentagem': $('#auxilio_13salario_porcentagem').val(),
            'periodo_13salario_fim': $('#periodo_13salario_fim').val(),
            'periodo_13salario': $('#periodo_13salario').val(),
            'auxilio_13salario_evento': $('#auxilio_13salario_evento').val(),
            "gestor": [], // Adicionando o array de dependentes
        }

        $("#gestor13salarioTable tbody tr").each(function() {
            let gestor = {
                "nome": $(this).find("td:eq(0)").text(),
                "chapa": $(this).find("td:eq(1)").text(),
                "dt_ini": $(this).find("td:eq(3) input").val(), // Captura o valor do input
                "dt_fim": $(this).find("td:eq(4) input").val() // Captura o valor do input
            };
            dados.gestor.push(gestor);
        });

        if(dados.auxilio_13salario_porcentagem == ''){exibeAlerta('error', 'Porcentagem do salário base do colaborador não informado.'); return false;}
        if(dados.auxilio_13salario_evento == ''){exibeAlerta('error', 'Evento do beneficio não informado.'); return false;}

        return dados;

    }

    const validaTipo7 = () => {

    var dados = {
        'tipo': 7,
        'reembolso_aluguel_evento': $('#reembolso_aluguel_evento').val(),
        
       
        'periodo_aluguel_fim': $('#periodo_aluguel_fim').val(),
        'periodo_aluguel': $('#periodo_aluguel').val(),
        'reembolso_aluguel_valor_demais_filiais': $('#reembolso_aluguel_valor_demais_filiais').val(),
        "aluguel": [], // Adicionando o array de dependentes
        "dependentes": [], // Adicionando o array de dependentes
        "gestor": [] // Adicionando o array de dependentes
    }

    $("#aluguelTable tbody tr").each(function() {
        let aluguel = {
                "nome": $(this).find("td:eq(0)").text(),
                "valor": $(this).find("td:eq(1) input").val() // Captura o valor do input
            };
            dados.aluguel.push(aluguel);
    });

    $("#dependentesAluguelTable tbody tr").each(function() {
        let dependente = {
            "codigo": $(this).find("td:eq(0)").text(),
            "valor_min": $(this).find("td:eq(1) input").val(), // Captura o valor do input
            "valor_max": $(this).find("td:eq(2) input").val() // Captura o valor do input
        };
        dados.dependentes.push(dependente);
    });
    $("#gestoraluguelTable tbody tr").each(function() {
        let gestor = {
            "nome": $(this).find("td:eq(0)").text(),
            "chapa": $(this).find("td:eq(1)").text(),
            "dt_ini": $(this).find("td:eq(3) input").val(), // Captura o valor do input
            "dt_fim": $(this).find("td:eq(4) input").val() // Captura o valor do input
        };
        dados.gestor.push(gestor);
    });


    
    if(dados.reembolso_aluguel_evento == ''){exibeAlerta('error', 'Evento do reembolso aluguel não informado.'); return false;}

    return dados;

    }
    
    const validaTipo5 = () => {

    var dados = {
        'tipo': 5,
        'reembolso_desconto_evento': $('#reembolso_desconto_evento').val(),
        'reembolso_desconto_evento2': $('#reembolso_desconto_evento2').val(),
        'reembolso_desconto_evento3': $('#reembolso_desconto_evento3').val(),
        'desconto_porcentagem': $('#desconto_porcentagem').val(),

        'periodo_desconto_fim': $('#periodo_desconto_fim').val(),
        'periodo_desconto': $('#periodo_desconto').val(),
        'reembolso_desconto_valor_demais_filiais': $('#reembolso_desconto_valor_demais_filiais').val(),
        "desconto": [], // Adicionando o array de dependentes
        "dependentes": [], // Adicionando o array de dependentes
        "gestor": [] // Adicionando o array de dependentes
    }

    $("#descontoTable tbody tr").each(function() {
        let desconto = {
                "nome": $(this).find("td:eq(0)").text(),
                "valor": $(this).find("td:eq(1) input").val() // Captura o valor do input
            };
            dados.desconto.push(desconto);
    });

 
    $("#gestordescontoTable tbody tr").each(function() {
        let gestor = {
            "nome": $(this).find("td:eq(0)").text(),
            "chapa": $(this).find("td:eq(1)").text(),
            "dt_ini": $(this).find("td:eq(3) input").val(), // Captura o valor do input
            "dt_fim": $(this).find("td:eq(4) input").val() // Captura o valor do input
        };
        dados.gestor.push(gestor);
    });


    
    if(dados.reembolso_desconto_evento == ''){exibeAlerta('error', 'Evento do reembolso desconto não informado.'); return false;}

    return dados;

    }
    // Função para remover a linha da tabela
    function removeRow(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('dependentesTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('dependentesTableContainer').style.display = "none";
        }
    }
    function RemoveCrechDepend(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('dependentesCrecheTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('dependentesCrecheTableContainer').style.display = "none";
        }
    }
    function RemoveAluguelDepend(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('dependentesAluguelTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('dependentesAluguelTableContainer').style.display = "none";
        }
    }

    function removeRow2(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('gestorPCDTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('gestorPCDTableContainer').style.display = "none";
        }
    }
    function removeRowCreche(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('gestorcrecheTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('gestorcrecheTableContainer').style.display = "none";
        }
    }
    function removeRowAluguel(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('gestoraluguelTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('gestoraluguelTableContainer').style.display = "none";
        }
    }

    function removeRowAluguel2(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('aluguelTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('aluguelTableContainer').style.display = "none";
        }
    }
    function removeRowDesconto(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('gestordescontoTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('gestordescontoTableContainer').style.display = "none";
        }
    }
    function removeRowDesconto2(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('descontoTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('descontoTableContainer').style.display = "none";
        }
    }
    function removeRow13salario(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('gestora13salarioTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('gestor13salarioTableContainer').style.display = "none";
        }
    }


    function removeRowCopart(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('gestorcoparticipacaoTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('gestorcoparticipacaoTableContainer').style.display = "none";
        }
    }
    function removeRow3(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('gestorMoradiaTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('gestorMoradiaTableContainer').style.display = "none";
        }
    }
    function removeRowsubstituicao(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('gestorsubstituicaoTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('gestorsubstituicaoTableContainer').style.display = "none";
        }
    }

    function removeRowSobreaviso(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        
        // Verifica se a tabela está vazia e oculta se necessário
        var tableBody = document.getElementById('gestorsobreavisoTable').getElementsByTagName('tbody')[0];
        if (tableBody.rows.length === 0) {
            document.getElementById('gestorsobreavisoTableContainer').style.display = "none";
        }
    }
    const validaTipo6 = () => {

        var dados = {
            'tipo': 6,
            'auxilio_moradia_porcentagem': $('#auxilio_moradia_porcentagem').val(),
            'periodo_moradia_fim': $('#periodo_moradia_fim').val(),
            'periodo_moradia': $('#periodo_moradia').val(),
            'dia_limite_compl6': $('#dia_limite_compl6').val(),
            'auxilio_moradia_evento': $('#auxilio_moradia_evento').val(),
            "gestor": [], // Adicionando o array de dependentes
        }

        $("#gestorMoradiaTable tbody tr").each(function() {
            let gestor = {
                "nome": $(this).find("td:eq(0)").text(),
                "chapa": $(this).find("td:eq(1)").text(),
                "dt_ini": $(this).find("td:eq(3) input").val(), // Captura o valor do input
                "dt_fim": $(this).find("td:eq(4) input").val() // Captura o valor do input
            };
            dados.gestor.push(gestor);
        });

        if(dados.auxilio_moradia_porcentagem == ''){exibeAlerta('error', 'Porcentagem do salário base do colaborador não informado.'); return false;}
        if(dados.auxilio_moradia_evento == ''){exibeAlerta('error', 'Evento do auxílio moradia não informado.'); return false;}

        return dados;
        
    }
    
    const validaTipo3 = () => {

        var dados = {
            'tipo': 3,
          
            'periodo_sobreaviso_fim': $('#periodo_sobreaviso_fim').val(),
            'periodo_sobreaviso': $('#periodo_sobreaviso').val(),
            'dia_ponto_ini': $('#dia_ponto_ini').val(),
            'dia_ponto_fim': $('#dia_ponto_fim').val(),
            'dia_limite_compl3': $('#dia_limite_compl3').val(),
            'limite_sobreaviso': $('#limite_sobreaviso').val(),
            'sobreaviso_evento': $('#sobreaviso_evento').val(),
            "gestor": [], // Adicionando o array de dependentes
        }

        $("#gestorsobreavisoTable tbody tr").each(function() {
            let gestor = {
                "nome": $(this).find("td:eq(0)").text(),
                "chapa": $(this).find("td:eq(1)").text(),
                "dt_ini": $(this).find("td:eq(3) input").val(), // Captura o valor do input
                "dt_fim": $(this).find("td:eq(4) input").val() // Captura o valor do input
            };
            dados.gestor.push(gestor);
        });

      
        if(dados.sobreaviso_evento == ''){exibeAlerta('error', 'Evento do Sobreaviso não informado.'); return false;}

        return dados;

    }
    const validaTipo1 = () => {

        var dados = {
            'tipo': 1,
            'substituicao_func': $('#substituicao_func').val(),
            'subistiuicao_min_dias': $('#subistiuicao_min_dias').val(),
            'subistiuicao_max_dias': $('#subistiuicao_max_dias').val(),
            'periodo_substituicao_fim': $('#periodo_substituicao_fim').val(),
            'periodo_substituicao': $('#periodo_substituicao').val(),
            'substituicao_evento': $('#substituicao_evento').val(),
            "gestor": [], // Adicionando o array de dependentes
        }

        $("#gestorsubstituicaoTable tbody tr").each(function() {
            let gestor = {
                "nome": $(this).find("td:eq(0)").text(),
                "chapa": $(this).find("td:eq(1)").text(),
                "dt_ini": $(this).find("td:eq(3) input").val(), // Captura o valor do input
                "dt_fim": $(this).find("td:eq(4) input").val() // Captura o valor do input
            };
            dados.gestor.push(gestor);
        });

      
        if(dados.substituicao_evento == ''){exibeAlerta('error', 'Evento da Substituição não informado.'); return false;}

        return dados;

    }

     const validaTipo8 = () => {

        var dados = {
            'tipo': 8,
            'auxilio_coparticipacao_porcentagem': $('#auxilio_coparticipacao_porcentagem').val(),
            'periodo_coparticipacao_fim': $('#periodo_coparticipacao_fim').val(),
            'periodo_coparticipacao': $('#periodo_coparticipacao').val(),
            'auxilio_coparticipacao_evento': $('#auxilio_coparticipacao_evento').val(),
            'auxilio_coparticipacao2_evento': $('#auxilio_coparticipacao2_evento').val(),
            "gestor": [], // Adicionando o array de dependentes
        }

        $("#gestorcoparticipacaoTable tbody tr").each(function() {
            let gestor = {
                "nome": $(this).find("td:eq(0)").text(),
                "chapa": $(this).find("td:eq(1)").text(),
                "dt_ini": $(this).find("td:eq(3) input").val(), // Captura o valor do input
                "dt_fim": $(this).find("td:eq(4) input").val() // Captura o valor do input
            };
            dados.gestor.push(gestor);
        });

        if(dados.auxilio_coparticipacao_porcentagem == ''){exibeAlerta('error', 'Porcentagem do salário base do colaborador não informado.'); return false;}
        if(dados.auxilio_coparticipacao_evento == ''){exibeAlerta('error', 'Evento do auxílio Coparticipação  não informado.'); return false;}
        if(dados.auxilio_coparticipacao2_evento == ''){exibeAlerta('error', 'Evento do auxílio Coparticipação não informado.'); return false;}


        return dados;
        
    }
    $(document).ready(function(e){
        $("[data-money]").maskMoney({
            prefix: '',
            allowNegative: false,
            allowZero: true,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        });
        $('#substituicao_func').select2({
            placeholder: 'Selecione os funcionários', // Texto de placeholder
            allowClear: true, // Permite limpar a seleção
            multiple: true,
            width: '100%' // Ajusta a largura do select2
        });
    });
</script>
<?php loadPlugin(['select2','maskmoney']); ?>
