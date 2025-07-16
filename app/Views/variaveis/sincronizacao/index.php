<!-- <script src="<?= base_url('public/DataTables/datatables.min.js') ?>"></script>
<link href="<?= base_url('public/DataTables/datatables.min.css') ?>" rel="stylesheet"> -->

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
                <h4 class="col-6 mb-1 mt-1"><?= $_titulo; ?></h4>
                
                </div>
            </div>
            
            <div class="card-body">

                <?= exibeMensagem(true); ?>

                <div class="tab-content">
                <form action="" method="post" name="form_filtro" id="form_filtro">


                    <div class="form-group row mb-2">
                        <label for="situacao" class="col-sm-2 col-form-label text-right text-left-sm">Situação:</label>
                        <div class="col-sm-10">
                            <select class="select2 custom-select form-control form-control-sm" name="situacao" id="situacao">
                                <option value="" > Todas </option>
                              
                                <?php if ($rh): ?>
                                <option value="1"<?= ($situacao == '1'  ) ? 'selected' : ''; ?>>  Criada </option>
                                <?php endif; ?>
                                <option value="2"<?= ($situacao == '2'  ) ? 'selected' : ''; ?>>  Pendente Ação gestor </option>
                                <option value="3"<?= ($situacao == '3'  ) ? 'selected' : ''; ?>>  Pendente Ação RH </option>
                                <option value="4"<?= ($situacao == '4'  ) ? 'selected' : ''; ?>>  Sincronizado </option>
                                <option value="7"<?= ($situacao == '7'  ) ? 'selected' : ''; ?>>  Pendente Sincronização </option>
                                <?php if ($rh): ?>
                                <option value="8"<?= ($situacao == '8'  ) ? 'selected' : ''; ?>>  Preenchimento RH </option>
                                <option value="6" <?= ($situacao == '6'  ) ? 'selected' : ''; ?>>  Reprovação RH </option>
                                <option value="5"<?= ($situacao == '5'  ) ? 'selected' : ''; ?>>  Reprovação Gestor </option>
                                <?php else: ?>
                                <option value="5"<?= ($situacao == '5'  ) ? 'selected' : ''; ?>>  Reprovação Gestor </option>
                                <?php endif;?>
                                
                            </select>
                        </div>
                    </div>

                    
                    <div class="form-group row mb-2">
                        <label for="data" class="col-sm-2 col-form-label text-right text-left-sm"> Período:</label>
                        <div class="input-group col-sm-10">
                            <input class="form-control datepicker m_data" type="date" value="<?php echo isset($_POST['data_inicio']) ? $_POST['data_inicio'] : (isset($data_inicio) ? $data_inicio : ''); ?>" name="data_inicio" id="data_inicio" required>
                            <div class="input-group-prepend input-group-append">
                                <span class="input-group-text">até</span>
                            </div>
                            <input class="form-control datepicker m_data" type="date" value="<?php echo isset($_POST['data_fim']) ? $_POST['data_fim'] : (isset($data_fim) ? $data_fim : ''); ?>" name="data_fim" id="data_fim" require>
                        </div>
                    </div>

                    

                    
                </form>

                
            </div>
            </div><!-- end card -->
            <div class="card-footer text-muted mt-0 d-flex justify-content-center">
                <button class="btn btn-primary btn-xxs mb-0" id="btnsave" onclick="return Filtro()"><i class="fas fa-search"></i> Filtrar</button>
            </div>
                        
        </div><!-- end card -->
    </div><!-- end col-12 -->

    </div><!-- end row -->
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
			
				
				<div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <ul class="nav nav-tabs mb-2" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link <?= $activeTab == '7' || !$activeTab   ? 'active' : '' ?>" data-toggle="tab" href="#auxilio_aluguel" role="tab"><i class="fa fa-key"></i> Auxilio Aluguel</a>
                        </li>
                        <?php if ($rh): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $activeTab == '2'   ? 'active' : '' ?>" data-toggle="tab" href="#auxilio_creche" role="tab"><i class="fa fa-child"></i> Auxilio Creche</a>
                        </li>
                        <?php endif;?>
                        <?php if ($rh): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $activeTab == '4'   ? 'active' : '' ?>" data-toggle="tab" href="#reembolso_cpd" role="tab"><i class="fa fa-wheelchair"></i> Auxilio Excepcional</a>
                        </li>
                        <?php endif;?>
                        <li class="nav-item">
                            <a class="nav-link <?= $activeTab == '6'   ? 'active' : '' ?>" data-toggle="tab" href="#auxilio_moradia" role="tab"><i class="fa fa-home"></i> Auxilio Moradia</a>
                        </li>
                        <?php if ($rh): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $activeTab == '9'   ? 'active' : '' ?>" data-toggle="tab" href="#salario" role="tab"><i class="fa fa-dollar-sign"></i> Antecipação 13º Salário</a>
                        </li>
                        <?php endif;?>
                       
                       
                       
                        <?php if ($rh): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $activeTab == '8'   ? 'active' : '' ?>" data-toggle="tab" href="#coparticipacao" role="tab"><i class="fa fa-hospital"></i> Coparticipação</a>
                        </li>
                        <?php endif;?>
                        <li class="nav-item">
                            <a class="nav-link <?= $activeTab == '5'   ? 'active' : '' ?>" data-toggle="tab" href="#desconto" role="tab"><i class="fa fa-dollar-sign"></i> Desconto Autorizado</a>
                        </li>
                       
                      
                        
                        <li class="nav-item">
                            <a class="nav-link <?= $activeTab == '1'   ? 'active' : '' ?>" data-toggle="tab" href="#substituicao" role="tab"><i class="fa fa-dollar-sign"></i> Salário  Substituição</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $activeTab == '3'   ? 'active' : '' ?>" data-toggle="tab" href="#sobreaviso" role="tab"><i class="fa fa-dollar-sign"></i>Sobreaviso</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#historico" role="tab"><i class="mdi mdi-history"></i> Histórico</a>
                        </li>
                        
                    </ul>

                    <div class="tab-content">
                        <!-- auxilio moradia -->
                        <div class="tab-pane  p-3 <?= $activeTab == '6'  ? 'active' : '' ?> " id="auxilio_moradia" role="tabpanel">
                            <div class="row d-flex justify-content-center mb-2">
                                    <div class="mb-2">
                                        <button id="enviaAprovacaoBtn" onclick="return enviaAprovacao(6)" type="button" class="btnpeq btn-sm btn-primary" style="margin-right: 10px;">
                                            <i class="fa fa-check"></i> Aprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <div class="mb-2">
                                        <button id="reprovaBTN" onclick="return enviaReprova(6)" type="button" class="btnpeq btn-sm btn-danger" style="margin-right: 10px;">
                                            <i class="fa fa-times"></i> Reprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <?php if ($rh): ?>
                                        <div class="mb-2">
                                            <button id="enviaSincBtn" onclick="return sincAprovacao(6)" type="button" class="btnpeq btn-sm  btn-success " style="margin-right: 10px;">
                                                <i class="fa fa-check"></i> Sincronizar Selecionado(s)
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>


                                <div class="">
                                    <table width="100%" cellspacing="0" cellpadding="0" id="datatable2"  class=" datatable table table-sm table-striped table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th class="n-mobile-cell" data-orderable="false"><input type="checkbox" onchange="toggleButtonVisibility()" id="select-all6"></th>
                                                <td class="n-mobile-cell" width="40" >Nº da requisição</td>
                                                <td class="n-mobile-cell" >Situação</td>
                                                <td class="n-mobile-cell">Filial</td>
                                                
                                                <td class="n-mobile-cell">Tipo</td>
                                                <td class="n-mobile-cell">Nome</td>
                                                <td class="n-mobile-cell">Função</td>
                                                <td class="n-mobile-cell">Data da solicitação</td>
                                                <td class="n-mobile-cell">Usuário Solicitande</td>
                                            
                                                <td class="n-mobile-cell" data-orderable="false">Ação</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Exemplo de linha de dados -->
                                            <?php if ($listaReqs6) : ?>
                                                <?php foreach ($listaReqs6 as $key => $dados) : ?>
                                                    <?php $valores = json_decode($dados->valores);
                                                        $fora_prazo ='';
                                                        if(isset($valores->fora_periodo)){
                                                            if($valores->fora_periodo == '2'){
                                                                $fora_prazo = 'style="color: red;" title="Criada fora do prazo como excessão"  ';
                                                            }
                                                        }
                                                    switch ($dados->status) {
                                                        case 0:
                                                            $descricao = "Excluída";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Excluída"
                                                            $corTexto = "#f57878"; // Cor do texto
                                                            break;
                                                        case 1:
                                                            $descricao = "Criada";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Criada"
                                                            $corTexto = "#000000"; // Cor do texto
                                                            break;
                                                        case 2:
                                                            $descricao = "Pend/Ação Gestor";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação gestor"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 3:
                                                            $descricao = "Pend/Ação RH";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 4:
                                                            $descricao = "Sincronizado";
                                                            $corFundo = "#e8ddfc"; // Cor de fundo para "Sincronizado"
                                                            $corTexto = "#8a00c2"; // Cor do texto
                                                            break;
                                                        case 5:
                                                            $descricao = "Reprov. Gestor";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. Gestor"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 6:
                                                            $descricao = "Reprov. RH";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. RH"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 7:
                                                                $descricao = "Pend/Sincronização";
                                                                $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                                $corTexto = "#8a5f00"; // Cor do texto
                                                                break;
                                                        default:
                                                            $descricao = "Todas";
                                                            $corFundo = "#ffffff"; // Cor de fundo padrão
                                                            $corTexto = "#000000"; // Cor do texto padrão
                                                            break;
                                                    }
                                                    
                                                    
                                                    
                                                    
                                                    ?>
                                                    <tr class="text-center">
                                                        <td class="n-mobile-cell" width="20" ><input type="checkbox" name="idbatida6[]"   value="<?= $dados->id ?>:<?= $dados->status ?>"> </td>
                                                        <td class="n-mobile-cell"  <?= $fora_prazo ?>><?= $dados->id ?></td>
                                                        <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                                        <td class="n-mobile-cell"><?= $valores->filial?></td>
                                                        
                                                        <td class="n-mobile-cell"><?= $dados->tiporeq == '1' ? 'Mensal' : 'Complementar' ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->Nome ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->funcao ?></td>
                                                        <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($dados->dtcad)) ?></td>
                                                        <td class="n-mobile-cell"><?= $dados->nome ?></td>
                                                        <td class="n-mobile-cell">
                                                        <div class="btn-group dropleft mb-2 mb-md-0">
                                                            <div class="dropdown">
                                                                <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                                <div class="dropdown-menu">
                                                                    <button onclick="aprovarReq('<?= $dados->id; ?>', 6,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status != '1' && $dados->status != '3' && $dados->status != '2' ) ? 'disabled'  : 'style="color: blue;"'; ?> > Aprovar</button>
                                                                    
                                                                    <button onclick="sincReq('<?= $dados->id; ?>', 6,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status == '7') ?'style="color: blue;"' : 'disabled'; ?> > Sincronizar</button>
                                                                    <?php if ($dados->status == '2'): ?>
                                                                     <!-- <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button> -->
                                                                    <?php endif; ?>
                                                                    <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button>
                                                                    <button onclick="verAnexos('<?= $dados->id; ?>','<?= $dados->status; ?>' )" class="dropdown-item  " > Documentos</button>
                                                 
                                                                    <a  href="<?= base_url('variaveis/moradia/editar/'.base64_encode($dados->id)); ?>"   class=" dropdown-item <?= ($dados->status == '2' || $dados->status == '0') ? 'disabled' : ''; ?> ">Editar requisição</a>
                                                                
                                                                    <button onclick="reprovarReq('<?= $dados->id; ?>', 6,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: red;">Reprovar</button>

                                                                    <?php if ($rh and $dados->status == '4'): ?>
                                                                      <button onclick="CancSincReq('<?= $dados->id; ?>', 6,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: DarkRed;">Cancelar Sincronismo</button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                            
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                        </tbody>
                                    </table>
                                
                                </div>
                                

                            </div>
                            <!--/ auxilio moradia -->

                            <div class="tab-pane  p-3 <?= $activeTab == '4' ? 'active' : '' ?> " id="reembolso_cpd" role="tabpanel">
                            <div class="">
                                <div class="row d-flex justify-content-center mb-2">
                                    <div class="mb-2">
                                        <button id="enviaAprovacaoBtn" onclick="return enviaAprovacao(4)" type="button" class="btnpeq btn-sm btn-primary" style="margin-right: 10px;">
                                            <i class="fa fa-check"></i> Aprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <div class="mb-2">
                                        <button id="reprovaBTN" onclick="return enviaReprova(4)" type="button" class="btnpeq btn-sm btn-danger" style="margin-right: 10px;">
                                            <i class="fa fa-times"></i> Reprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <?php if ($rh): ?>
                                        <div class="mb-2">
                                            <button id="enviaSincBtn" onclick="return sincAprovacao(4)" type="button" class="btnpeq btn-sm   btn-success" style="margin-right: 10px;">
                                                <i class="fa fa-check"></i> Sincronizar Selecionado(s)
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <table width="100%" cellspacing="0" cellpadding="0"  id="datatable1" class="datatable  table table-sm table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th data-orderable="false"><input type="checkbox" onchange="toggleButtonVisibility()" id="select-all4"></th>
                                            <td class="n-mobile-cell" width="40" >Nº da requisição</td>
                                            <td class="n-mobile-cell" >Situação</td>
                                            <td class="n-mobile-cell">Filial</td>
                                       
                                            <td class="n-mobile-cell">Tipo</td>
                                            <td class="n-mobile-cell">Nome</td>
                                            <td class="n-mobile-cell">Função</td>
                                            <td class="n-mobile-cell">Valor</td>
                                            <td class="n-mobile-cell">Data da solicitação</td>
                                            <td class="n-mobile-cell">Usuário Solicitande</td>
                                            <td class="n-mobile-cell" data-orderable="false">Ação</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Exemplo de linha de dados -->
                                        <?php if ($listaReqs4) : ?>
                                            <?php foreach ($listaReqs4 as $key => $dados) : ?>
                                                <?php $valores = json_decode($dados->valores); 
                                                    switch ($dados->status) {
                                                        case 0:
                                                            $descricao = "Excluída";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Excluída"
                                                            $corTexto = "#f57878"; // Cor do texto
                                                            break;
                                                        case 1:
                                                            $descricao = "Criada";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Criada"
                                                            $corTexto = "#000000"; // Cor do texto
                                                            break;
                                                        case 2:
                                                            $descricao = "Pend/Ação Gestor";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação gestor"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 3:
                                                            $descricao = "Pend/Ação RH";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 7:
                                                            $descricao = "Pend/Sincronização";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 4:
                                                            $descricao = "Sincronizado";
                                                            $corFundo = "#e8ddfc"; // Cor de fundo para "Sincronizado"
                                                            $corTexto = "#8a00c2"; // Cor do texto
                                                            break;
                                                        case 5:
                                                            $descricao = "Reprov. Gestor";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. Gestor"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 6:
                                                            $descricao = "Reprov. RH";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. RH"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                    
                                                        default:
                                                            $descricao = "Todas";
                                                            $corFundo = "#ffffff"; // Cor de fundo padrão
                                                            $corTexto = "#000000"; // Cor do texto padrão
                                                            break;
                                                    }
                                                    $fora_prazo ='';
                                                    if(isset($valores->fora_periodo)){
                                                        if($valores->fora_periodo == '2'){
                                                            $fora_prazo = 'style="color: red;" title="Criada fora do prazo como excessão"  ';
                                                        }
                                                    }
                                                
                                                
                                                
                                                ?>
                                                <tr class="text-center">
                                                    <td class="n-mobile-cell" width="80">
                                                    <input type="checkbox" name="idbatida4[]"  value="<?= $dados->id ?>:<?= $dados->status ?>"> 
                                                            <button class="btn btn-soft-primary btn-toggle" 
                                                                type="button"
                                                                data-dependentes='<?= $valores->dependentes; ?>'>
                                                                +
                                                            </button>
                                                    </td>
                                                    
                                                    <td class="n-mobile-cell"  <?= $fora_prazo ?>><?= $dados->id ?></td>
                                                    <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                                    <td class="n-mobile-cell"><?= $valores->filial?></td>
                                                 
                                                    <td class="n-mobile-cell"><?= $dados->tiporeq == '1' ? 'Mensal' : 'Complementar' ?></td>
                                                    <td class="n-mobile-cell"><?= $valores->Nome ?></td>
                                                    <td class="n-mobile-cell"><?= $valores->funcao ?></td>
                                                    <td class="n-mobile-cell"> <?= isset($valores->valor_total) ? 'R$'.$valores->valor_total : '';  ?></td>
                                                    <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($dados->dtcad)) ?></td>
                                                    <td class="n-mobile-cell"><?= $dados->nome ?></td>
                                                    <td class="n-mobile-cell">
                                                    <div class="btn-group dropleft mb-2 mb-md-0">
                                                        <div class="dropdown">
                                                            <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                            <div class="dropdown-menu">
                                                                <button onclick="aprovarReq('<?= $dados->id; ?>', 4,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status != '1' && $dados->status != '3' && $dados->status != '2' ) ? 'disabled'  : 'style="color: blue;"'; ?>> Aprovar</button>
                                                                <button onclick="sincReq('<?= $dados->id; ?>', 4,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status == '7') ? 'style="color: blue;"' : 'disabled' ; ?> > Sincronizar</button>
                                                                
                                                                <?php if ($dados->status == '2'): ?>
                                                                     <!-- <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button> -->
                                                                    <?php endif; ?>
                                                               <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button>
                                                               <button onclick="verAnexos('<?= $dados->id; ?>','<?= $dados->status; ?>' )" class="dropdown-item  " > Documentos</button>
                                                 
                                                                <a  href="<?= base_url('variaveis/pcd/editar/'.base64_encode($dados->id)); ?>"  class=" dropdown-item <?= ($dados->status == '2' || $dados->status == '0') ? 'disabled' : ''; ?> ">Editar requisição</a>
                                                            
                                                                <button onclick="reprovarReq('<?= $dados->id; ?>', 4,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: red;">Reprovar</button>

                                                                <?php if ($rh and $dados->status == '4'): ?>
                                                                  <button onclick="CancSincReq('<?= $dados->id; ?>', 4,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: DarkRed;">Cancelar Sincronismo</button>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        
                                                    </td>
                                                </tr>
                                                
                                                
                                            
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                    </tbody>
                                </table>
                            
                            </div>

                        </div>
                        
                        <div class="tab-pane  p-3  <?= $activeTab == '2'   ? 'active' : '' ?> " id="auxilio_creche"  role="tabpanel">
                            <div class="">
                                <div class="row d-flex justify-content-center mb-2">
                                    <div class="mb-2">
                                        <button id="enviaAprovacaoBtn" onclick="return enviaAprovacao(2)" type="button" class="btnpeq btn-sm btn-primary" style="margin-right: 10px;">
                                            <i class="fa fa-check"></i> Aprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <div class="mb-2">
                                        <button id="reprovaBTN" onclick="return enviaReprova(2)" type="button" class="btnpeq btn-sm btn-danger" style="margin-right: 10px;">
                                            <i class="fa fa-times"></i> Reprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <?php if ($rh): ?>
                                        <div class="mb-2">
                                            <button id="enviaSincBtn" onclick="return sincAprovacao(2)" type="button" class="btnpeq btn-sm   btn-success" style="margin-right: 10px;">
                                                <i class="fa fa-check"></i> Sincronizar Selecionado(s)
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <table width="100%" cellspacing="0" cellpadding="0"  id="datatableCreche" class="datatable  table table-sm table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th data-orderable="false"><input type="checkbox" onchange="toggleButtonVisibility()" id="select-all2"></th>
                                            <td class="n-mobile-cell" width="40" >Nº da requisição</td>
                                            <td class="n-mobile-cell" >Situação</td>
                                            <td class="n-mobile-cell">Filial</td>
                                         
                                            <td class="n-mobile-cell">Tipo</td>
                                            <td class="n-mobile-cell">Nome</td>
                                            <td class="n-mobile-cell">Função</td>
                                            <td class="n-mobile-cell">Valor</td>
                                            <td class="n-mobile-cell">Data da solicitação</td>
                                            <td class="n-mobile-cell">Usuário Solicitande</td>
                                            <td class="n-mobile-cell" data-orderable="false">Ação</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Exemplo de linha de dados -->
                                        <?php if ($listaReqs2) : ?>
                                            <?php foreach ($listaReqs2 as $key => $dados) : ?>
                                                <?php $valores = json_decode($dados->valores); 
                                                    switch ($dados->status) {
                                                        case 0:
                                                            $descricao = "Excluída";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Excluída"
                                                            $corTexto = "#f57878"; // Cor do texto
                                                            break;
                                                        case 1:
                                                            $descricao = "Criada";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Criada"
                                                            $corTexto = "#000000"; // Cor do texto
                                                            break;
                                                        case 2:
                                                            $descricao = "Pend/Ação Gestor";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação gestor"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 3:
                                                            $descricao = "Pend/Ação RH";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 7:
                                                            $descricao = "Pend/Sincronização";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 4:
                                                            $descricao = "Sincronizado";
                                                            $corFundo = "#e8ddfc"; // Cor de fundo para "Sincronizado"
                                                            $corTexto = "#8a00c2"; // Cor do texto
                                                            break;
                                                        case 5:
                                                            $descricao = "Reprov. Gestor";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. Gestor"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 6:
                                                            $descricao = "Reprov. RH";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. RH"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                    
                                                        default:
                                                            $descricao = "Todas";
                                                            $corFundo = "#ffffff"; // Cor de fundo padrão
                                                            $corTexto = "#000000"; // Cor do texto padrão
                                                            break;
                                                    }
                                                    $fora_prazo ='';
                                                    if(isset($valores->fora_periodo)){
                                                        if($valores->fora_periodo == '2'){
                                                            $fora_prazo = 'style="color: red;" title="Criada fora do prazo como excessão"  ';
                                                        }
                                                    }
                                                
                                                
                                                
                                                ?>
                                                <tr class="text-center">
                                                <td class="n-mobile-cell" width="80">
                                                    <input type="checkbox" name="idbatida2[]"  value="<?= $dados->id ?>:<?= $dados->status ?>"> 
                                                            <button class="btn btn-soft-primary btn-toggle2" 
                                                                type="button"
                                                                data-dependentes='<?= $valores->dependentes; ?>'>
                                                                +
                                                            </button>
                                                    </td>
                                                    <td class="n-mobile-cell"  <?= $fora_prazo ?>><?= $dados->id ?></td>
                                                    <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                                    <td class="n-mobile-cell"><?= $valores->filial?></td>
                                                  
                                                    <td class="n-mobile-cell"><?= $dados->tiporeq == '1' ? 'Mensal' : 'Complementar' ?></td>
                                                    <td class="n-mobile-cell"><?= $valores->Nome ?></td>
                                                    <td class="n-mobile-cell"><?= $valores->funcao ?></td>
                                                    <td class="n-mobile-cell"> <?= isset($valores->valor_total) ? 'R$'.$valores->valor_total : '';  ?></td>
                                                    <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($dados->dtcad)) ?></td>
                                                    <td class="n-mobile-cell"><?= $dados->nome ?></td>
                                                    <td class="n-mobile-cell">
                                                    <div class="btn-group dropleft mb-2 mb-md-0">
                                                        <div class="dropdown">
                                                            <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                            <div class="dropdown-menu">
                                                                <button onclick="aprovarReq('<?= $dados->id; ?>', 2,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status != '1' && $dados->status != '3' && $dados->status != '2' ) ? 'disabled'  : 'style="color: blue;"'; ?>> Aprovar</button>
                                                                <button onclick="sincReq('<?= $dados->id; ?>', 2,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status == '7') ? 'style="color: blue;"' : 'disabled' ; ?> > Sincronizar</button>
                                                                
                                                                <?php if ($dados->status == '2'): ?>
                                                                     <!-- <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button> -->
                                                                    <?php endif; ?>
                                                               <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button>
                                                               <button onclick="verAnexos('<?= $dados->id; ?>','<?= $dados->status; ?>' )" class="dropdown-item  " > Documentos</button>
                                                 
                                                                <a  href="<?= base_url('variaveis/creche/editar/'.base64_encode($dados->id)); ?>"  class=" dropdown-item <?= ($dados->status == '2' || $dados->status == '0') ? 'disabled' : ''; ?> ">Editar requisição</a>
                                                            
                                                                <button onclick="reprovarReq('<?= $dados->id; ?>', 2,'<?= $dados->status; ?>')" class="dropdown-item  <?= ($dados->status == '0') ? 'disabled' : ''; ?> " style="color: red;">Reprovar</button>

                                                                <?php if ($rh and $dados->status == '4'): ?>
                                                                  <button onclick="CancSincReq('<?= $dados->id; ?>', 2,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: DarkRed;">Cancelar Sincronismo</button>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        
                                                    </td>
                                                </tr>
                                                
                                                
                                            
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                    </tbody>
                                </table>
                            
                            </div>

                        </div>
                        <div class="tab-pane  p-3 <?= $activeTab == '8'   ? 'active' : '' ?>   " id="coparticipacao"  role="tabpanel">
                            <div class="">
                                <div class="row d-flex justify-content-center mb-2">
                                    <div class="mb-2">
                                        <button id="enviaAprovacaoBtn" onclick="return enviaAprovacao(8)" type="button" class="btnpeq btn-sm btn-primary" style="margin-right: 10px;">
                                            <i class="fa fa-check"></i> Aprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <div class="mb-2">
                                        <button id="reprovaBTN" onclick="return enviaReprova(8)" type="button" class="btnpeq btn-sm btn-danger" style="margin-right: 10px;">
                                            <i class="fa fa-times"></i> Reprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <?php if ($rh): ?>
                                        <div class="mb-2">
                                            <button id="enviaSincBtn" onclick="return sincAprovacao(8)" type="button" class="btnpeq btn-sm   btn-success" style="margin-right: 10px;">
                                                <i class="fa fa-check"></i> Sincronizar Selecionado(s)
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <table width="100%" cellspacing="0" cellpadding="0"  id="datatableCopart" class="datatable  table table-sm table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th data-orderable="false"><input type="checkbox" onchange="toggleButtonVisibility()" id="select-all8"></th>
                                            <td class="n-mobile-cell" width="40" >Nº da requisição</td>
                                            <td class="n-mobile-cell" >Situação</td>
                                            <td class="n-mobile-cell" >Prestadora</td>
                                         
                                            <td class="n-mobile-cell">Data da solicitação</td>
                                            <td class="n-mobile-cell">Usuário Solicitande</td>
                                            <td class="n-mobile-cell" data-orderable="false">Ação</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Exemplo de linha de dados -->
                                        <?php if ($listaReqs8) : ?>
                                            <?php foreach ($listaReqs8 as $key => $dados) : ?>
                                                <?php $valores = json_decode($dados->valores); 
                                                    switch ($dados->status) {
                                                        case 0:
                                                            $descricao = "Excluída";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Excluída"
                                                            $corTexto = "#f57878"; // Cor do texto
                                                            break;
                                                        case 1:
                                                            $descricao = "Criada";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Criada"
                                                            $corTexto = "#000000"; // Cor do texto
                                                            break;
                                                        case 2:
                                                            $descricao = "Pend/Ação Gestor";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação gestor"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 3:
                                                            $descricao = "Pend/Ação RH";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 7:
                                                            $descricao = "Pend/Sincronização";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 4:
                                                            $descricao = "Sincronizado";
                                                            $corFundo = "#e8ddfc"; // Cor de fundo para "Sincronizado"
                                                            $corTexto = "#8a00c2"; // Cor do texto
                                                            break;
                                                        case 5:
                                                            $descricao = "Reprov. Gestor";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. Gestor"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 6:
                                                            $descricao = "Reprov. RH";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. RH"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                    
                                                        default:
                                                            $descricao = "Todas";
                                                            $corFundo = "#ffffff"; // Cor de fundo padrão
                                                            $corTexto = "#000000"; // Cor do texto padrão
                                                            break;
                                                    }
                                                    $fora_prazo ='';
                                                    if(isset($valores->fora_periodo)){
                                                        if($valores->fora_periodo == '2'){
                                                            $fora_prazo = 'style="color: red;" title="Criada fora do prazo como excessão"  ';
                                                        }
                                                    }
                                                    switch ($dados->tiporeq) {
                                                        case 1:
                                                           $prestadora = 'Bradesco';
                                                            break;
                                                        case 2:
                                                            $prestadora = 'Unimed';
                                                            break;
                                                    }
                                                
                                                
                                                
                                                ?>
                                                <tr class="text-center">
                                                    <td class="n-mobile-cell" width="80">
                                                    <input type="checkbox" name="idbatida8[]"  value="<?= $dados->id ?>:<?= $dados->status ?>">
                                                            <button class="btn btn-soft-primary btn-toggle3" 
                                                                type="button"
                                                                data-dependentes='<?= $valores->dependentes; ?>'>
                                                                +
                                                            </button>
                                                    </td>
                                                  
                                                    <td class="n-mobile-cell"  <?= $fora_prazo ?>><?= $dados->id ?></td>
                                                    <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                                   
                                                    <td class="n-mobile-cell"><?= $prestadora  ?></td>
                                                    <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($dados->dtcad)) ?></td>
                                                    <td class="n-mobile-cell"><?= $dados->nome ?></td>
                                                    <td class="n-mobile-cell">
                                                    <div class="btn-group dropleft mb-2 mb-md-0">
                                                        <div class="dropdown">
                                                            <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                            <div class="dropdown-menu">
                                                                <button onclick="aprovarReq('<?= $dados->id; ?>', 8,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status != '1' && $dados->status != '3' && $dados->status != '2' ) ? 'disabled'  : 'style="color: blue;"'; ?>> Aprovar</button>
                                                                <button onclick="sincReq('<?= $dados->id; ?>', 8,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status == '7') ? 'style="color: blue;"' : 'disabled' ; ?> > Sincronizar</button>
                                                                
                                                                <?php if ($dados->status == '2'): ?>
                                                                     <!-- <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button> -->
                                                                    <?php endif; ?>
                                                               <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button>
                                                               <button onclick="verAnexos('<?= $dados->id; ?>','<?= $dados->status; ?>','8' )" class="dropdown-item  " > Documentos</button>
                                                 
                                                                <a  href="<?= base_url('variaveis/coparticipacao/editar/'.base64_encode($dados->id)); ?>"  class=" dropdown-item  <?= ($dados->status == '2' || $dados->status == '0') ? 'disabled' : ''; ?>">Editar requisição</a>
                                                            
                                                                <button onclick="reprovarReq('<?= $dados->id; ?>', 8,'<?= $dados->status; ?>')" class="dropdown-item  <?= ($dados->status == '0') ? 'disabled' : ''; ?> " style="color: red;">Reprovar</button>

                                                                <?php if ($rh and $dados->status == '4'): ?>
                                                                  <button onclick="CancSincReq('<?= $dados->id; ?>', 8,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: DarkRed;">Cancelar Sincronismo</button>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        
                                                    </td>
                                                </tr>
                                                
                                               
                                            
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                    </tbody>
                                </table>
                            
                            </div>

                        </div>
                        <div class="tab-pane  p-3 <?= $activeTab == '5'  ? 'active' : '' ?> " id="desconto"   role="tabpanel">
                            <div class="row d-flex justify-content-center mb-2">
                                    <div class="mb-2">
                                        <button id="enviaAprovacaoBtn" onclick="return enviaAprovacao(5)" type="button" class="btnpeq btn-sm btn-primary" style="margin-right: 10px;">
                                            <i class="fa fa-check"></i> Aprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <div class="mb-2">
                                        <button id="reprovaBTN" onclick="return enviaReprova(5)" type="button" class="btnpeq btn-sm btn-danger" style="margin-right: 10px;">
                                            <i class="fa fa-times"></i> Reprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <?php if ($rh): ?>
                                        <div class="mb-2">
                                            <button id="enviaSincBtn" onclick="return sincAprovacao(5)" type="button" class="btnpeq btn-sm  btn-success " style="margin-right: 10px;">
                                                <i class="fa fa-check"></i> Sincronizar Selecionado(s)
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>


                                <div class="">
                                    <table width="100%" cellspacing="0" cellpadding="0" id="datatableDesconto"  class=" datatable table table-sm table-striped table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th class="n-mobile-cell" data-orderable="false"><input type="checkbox" onchange="toggleButtonVisibility()" id="select-all5"></th>
                                                <td class="n-mobile-cell" width="40" >Nº da requisição</td>
                                                <td class="n-mobile-cell" >Situação</td>
                                                <td class="n-mobile-cell">Filial</td>
                                              
                                                <td class="n-mobile-cell">Tipo</td>
                                                <td class="n-mobile-cell">Valor Total </td>
                                                <td class="n-mobile-cell">Valor Descontado</td>
                                                <td class="n-mobile-cell">Nome</td>
                                                <td class="n-mobile-cell">Função</td>
                                                <td class="n-mobile-cell">Meses</td>
                                                <td class="n-mobile-cell">Data da solicitação</td>
                                                <td class="n-mobile-cell">Usuário Solicitande</td>
                                            
                                                <td class="n-mobile-cell" data-orderable="false">Ação</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Exemplo de linha de dados -->
                                            <?php if ($listaReqs5) : ?>
                                                <?php foreach ($listaReqs5 as $key => $dados) : ?>
                                                    <?php $valores = json_decode($dados->valores);
                                                        $fora_prazo ='';
                                                        if(isset($valores->fora_periodo)){
                                                            if($valores->fora_periodo == '2'){
                                                                $fora_prazo = 'style="color: red;" title="Criada fora do prazo como excessão"  ';
                                                            }
                                                        }
                                                    switch ($dados->status) {
                                                        case 0:
                                                            $descricao = "Excluída";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Excluída"
                                                            $corTexto = "#f57878"; // Cor do texto
                                                            break;
                                                        case 1:
                                                            $descricao = "Criada";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Criada"
                                                            $corTexto = "#000000"; // Cor do texto
                                                            break;
                                                        case 2:
                                                            $descricao = "Pend/Ação Gestor";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação gestor"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 3:
                                                            $descricao = "Pend/Ação RH";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 4:
                                                            $descricao = "Sincronizado";
                                                            $corFundo = "#e8ddfc"; // Cor de fundo para "Sincronizado"
                                                            $corTexto = "#8a00c2"; // Cor do texto
                                                            break;
                                                        case 5:
                                                            $descricao = "Reprov. Gestor";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. Gestor"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 6:
                                                            $descricao = "Reprov. RH";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. RH"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 7:
                                                                $descricao = "Pend/Sincronização";
                                                                $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                                $corTexto = "#8a5f00"; // Cor do texto
                                                                break;
                                                        case 8:
                                                                $descricao = "Preenchimento RH";
                                                                $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                                $corTexto = "#8a5f00"; // Cor do texto
                                                                break;
                                                        case 9:
                                                                $descricao = "Pend/Documento";
                                                                $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                                $corTexto = "#8a5f00"; // Cor do texto
                                                                break;
                                                        default:
                                                            $descricao = "Todas";
                                                            $corFundo = "#ffffff"; // Cor de fundo padrão
                                                            $corTexto = "#000000"; // Cor do texto padrão
                                                            break;
                                                    }
                                                    
                                                    if( $dados->tiporeq == '1' ){
                                                        $desconto = 'Desconto Autorizado';
                                                    }elseif($dados->tiporeq == '2'){
                                                        $desconto = 'Desconto de EPIs';
                                                    }else{
                                                        $desconto = 'Multa de Trânsito';
                                                    }
                                                    
                                                    
                                                    
                                                    ?>
                                                    <tr class="text-center">
                                                        <td class="n-mobile-cell" width="20" ><input <?= ($dados->status == '9') ? 'disabled' : ''; ?>  type="checkbox" name="idbatida5[]"   value="<?= $dados->id ?>:<?= $dados->status ?>"> </td>
                                                        <td class="n-mobile-cell"  <?= $fora_prazo ?>><?= $dados->id ?></td>
                                                        <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                                        <td class="n-mobile-cell"><?= $valores->filial?></td>
                                                      
                                                        <td class="n-mobile-cell"><?=   $desconto ?></td>
                                                        <td class="n-mobile-cell"> R$ <?= $valores->valor ?></td>
                                                        <td class="n-mobile-cell"> R$ <?= isset($valores->valor_desconto) ? $valores->valor_desconto : 0 ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->Nome ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->funcao ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->quantMes ?></td>
                                                        <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($dados->dtcad)) ?></td>
                                                        <td class="n-mobile-cell"><?= $dados->nome ?></td>
                                                        <td class="n-mobile-cell">
                                                        <div class="btn-group dropleft mb-2 mb-md-0">
                                                            <div class="dropdown">
                                                                <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                                <div class="dropdown-menu">
                                                                    <button onclick="aprovarReq('<?= $dados->id; ?>', 5,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status != '1' && $dados->status != '3' && $dados->status != '2' && $dados->status != '8'  ) ? 'disabled'  : 'style="color: blue;"'; ?> > Aprovar</button>
                                                                    <button onclick="sincReq('<?= $dados->id; ?>', 5,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status == '7') ?'style="color: blue;"' : 'disabled'; ?> > Sincronizar</button>
                                                                    <?php if ($dados->status == '2'): ?>
                                                                     <!-- <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button> -->
                                                                    <?php endif; ?>
                                                                     <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button>
                                                                    <button onclick="verAnexos('<?= $dados->id; ?>','<?= $dados->status; ?>' )" class="dropdown-item  " > Documentos</button>
                                                 
                                                                    <a  href="<?= base_url('variaveis/desconto/editar/'.base64_encode($dados->id)); ?>"   class=" dropdown-item <?= ($dados->status == '2' || $dados->status == '9' || $dados->status == '0') ? 'disabled' : ''; ?> ">Editar requisição</a>
                                                                     
                                                                    <button onclick="reprovarReq('<?= $dados->id; ?>', 5,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: red;">Reprovar</button>

                                                                    <?php if ($rh and $dados->status == '4'): ?>
                                                                      <button onclick="CancSincReq('<?= $dados->id; ?>', 5,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: DarkRed;">Cancelar Sincronismo</button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                            
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                        </tbody>
                                    </table>
                                
                                </div>
                                

                            </div>
                        <div class="tab-pane  p-3 <?= $activeTab == '3'  ? 'active' : '' ?> " id="sobreaviso"   role="tabpanel">
                            <div class="row d-flex justify-content-center mb-2">
                                    <div class="mb-2">
                                        <button id="enviaAprovacaoBtn" onclick="return enviaAprovacao(3)" type="button" class="btnpeq btn-sm btn-primary" style="margin-right: 10px;">
                                            <i class="fa fa-check"></i> Aprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <div class="mb-2">
                                        <button id="reprovaBTN" onclick="return enviaReprova(3)" type="button" class="btnpeq btn-sm btn-danger" style="margin-right: 10px;">
                                            <i class="fa fa-times"></i> Reprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <?php if ($rh): ?>
                                        <div class="mb-2">
                                            <button id="enviaSincBtn" onclick="return sincAprovacao(3)" type="button" class="btnpeq btn-sm  btn-success " style="margin-right: 10px;">
                                                <i class="fa fa-check"></i> Sincronizar Selecionado(s)
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>


                                <div class="">
                                    <table width="100%" cellspacing="0" cellpadding="0" id="datatableSobreaviso"  class=" datatable table table-sm table-striped table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th class="n-mobile-cell" data-orderable="false"><input type="checkbox" onchange="toggleButtonVisibility()" id="select-all3"></th>
                                                <td class="n-mobile-cell" width="40" >Nº da requisição</td>
                                                <td class="n-mobile-cell" >Situação</td>
                                                <td class="n-mobile-cell">Filial</td>
                                          
                                                <td class="n-mobile-cell">Tipo</td>
                                                <td class="n-mobile-cell">Nome</td>
                                                <td class="n-mobile-cell">Função</td>
                                                <td class="n-mobile-cell">Horas</td>
                                                <td class="n-mobile-cell">Data da solicitação</td>
                                                <td class="n-mobile-cell">Usuário Solicitande</td>
                                            
                                                <td class="n-mobile-cell" data-orderable="false">Ação</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Exemplo de linha de dados -->
                                            <?php if ($listaReqs3) : ?>
                                                <?php foreach ($listaReqs3 as $key => $dados) : ?>
                                                    <?php $valores = json_decode($dados->valores);
                                                        $fora_prazo ='';
                                                        if(isset($valores->fora_periodo)){
                                                            if($valores->fora_periodo == '2'){
                                                                $fora_prazo = 'style="color: red;" title="Criada fora do prazo como excessão"  ';
                                                            }
                                                        }
                                                    switch ($dados->status) {
                                                        case 0:
                                                            $descricao = "Excluída";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Excluída"
                                                            $corTexto = "#f57878"; // Cor do texto
                                                            break;
                                                        case 1:
                                                            $descricao = "Criada";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Criada"
                                                            $corTexto = "#000000"; // Cor do texto
                                                            break;
                                                        case 2:
                                                            $descricao = "Pend/Ação Gestor";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação gestor"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 3:
                                                            $descricao = "Pend/Ação RH";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 4:
                                                            $descricao = "Sincronizado";
                                                            $corFundo = "#e8ddfc"; // Cor de fundo para "Sincronizado"
                                                            $corTexto = "#8a00c2"; // Cor do texto
                                                            break;
                                                        case 5:
                                                            $descricao = "Reprov. Gestor";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. Gestor"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 6:
                                                            $descricao = "Reprov. RH";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. RH"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 7:
                                                                $descricao = "Pend/Sincronização";
                                                                $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                                $corTexto = "#8a5f00"; // Cor do texto
                                                                break;
                                                        default:
                                                            $descricao = "Todas";
                                                            $corFundo = "#ffffff"; // Cor de fundo padrão
                                                            $corTexto = "#000000"; // Cor do texto padrão
                                                            break;
                                                    }
                                                    
                                                    
                                                    
                                                    
                                                    ?>
                                                    <tr class="text-center">
                                                        <td class="n-mobile-cell" width="20" ><input type="checkbox" name="idbatida3[]"   value="<?= $dados->id ?>:<?= $dados->status ?>"> </td>
                                                        <td class="n-mobile-cell"  <?= $fora_prazo ?>><?= $dados->id ?></td>
                                                        <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                                        <td class="n-mobile-cell"><?= $valores->filial?></td>
                                                    
                                                        <td class="n-mobile-cell"><?= $dados->tiporeq == '1' ? 'Mensal' : 'Complementar' ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->Nome ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->funcao ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->valor ?></td>
                                                        <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($dados->dtcad)) ?></td>
                                                        <td class="n-mobile-cell"><?= $dados->nome ?></td>
                                                        <td class="n-mobile-cell">
                                                        <div class="btn-group dropleft mb-2 mb-md-0">
                                                            <div class="dropdown">
                                                                <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                                <div class="dropdown-menu">
                                                                    <button onclick="aprovarReq('<?= $dados->id; ?>', 3,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status != '1' && $dados->status != '3' && $dados->status != '2' ) ? 'disabled'  : 'style="color: blue;"'; ?> > Aprovar</button>
                                                                    <button onclick="sincReq('<?= $dados->id; ?>', 3,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status == '7') ?'style="color: blue;"' : 'disabled'; ?> > Sincronizar</button>
                                                                    <?php if ($dados->status == '2'): ?>
                                                                     <!-- <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button> -->
                                                                    <?php endif; ?>
                                                                     <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button>
                                                                    <button onclick="verAnexos('<?= $dados->id; ?>','<?= $dados->status; ?>' )" class="dropdown-item  " > Documentos</button>
                                                 
                                                                    <a  href="<?= base_url('variaveis/sobreaviso/editar/'.base64_encode($dados->id)); ?>"   class=" dropdown-item <?= ($dados->status == '2' || $dados->status == '0') ? 'disabled' : ''; ?>">Editar requisição</a>
                                                                
                                                                    <button onclick="reprovarReq('<?= $dados->id; ?>', 3,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: red;">Reprovar</button>

                                                                    <?php if ($rh and $dados->status == '4'): ?>
                                                                      <button onclick="CancSincReq('<?= $dados->id; ?>', 3,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: DarkRed;">Cancelar Sincronismo</button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                            
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                        </tbody>
                                    </table>
                                
                                </div>
                                

                            </div>
                        <div class="tab-pane  p-3 <?= $activeTab == '1'  ? 'active' : '' ?> " id="substituicao"   role="tabpanel">
                            <div class="row d-flex justify-content-center mb-2">
                                    <div class="mb-2">
                                        <button id="enviaAprovacaoBtn" onclick="return enviaAprovacao(1)" type="button" class="btnpeq btn-sm btn-primary" style="margin-right: 10px;">
                                            <i class="fa fa-check"></i> Aprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <div class="mb-2">
                                        <button id="reprovaBTN" onclick="return enviaReprova(1)" type="button" class="btnpeq btn-sm btn-danger" style="margin-right: 10px;">
                                            <i class="fa fa-times"></i> Reprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <?php if ($rh): ?>
                                        <div class="mb-2">
                                            <button id="enviaSincBtn" onclick="return sincAprovacao(1)" type="button" class="btnpeq btn-sm  btn-success " style="margin-right: 10px;">
                                                <i class="fa fa-check"></i> Sincronizar Selecionado(s)
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>


                                <div class="">
                                    <table width="100%" cellspacing="0" cellpadding="0" id="datatablesubstituicao"  class=" datatable table table-sm table-striped table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th class="n-mobile-cell" data-orderable="false"><input type="checkbox" onchange="toggleButtonVisibility()" id="select-all1"></th>
                                                <td class="n-mobile-cell" width="40" >Nº da requisição</td>
                                                <td class="n-mobile-cell" >Situação</td>
                                                <td class="n-mobile-cell">Filial</td>
                                                <td class="n-mobile-cell">Tipo</td>
                                                <td class="n-mobile-cell">Periodo Completo</td>
                                                <td class="n-mobile-cell">Data Referencia</td>
                                                <td class="n-mobile-cell">Dias Referencia</td>
                                                <td class="n-mobile-cell">Nome Substituto</td>
                                                <td class="n-mobile-cell">Função</td>
                                                <td class="n-mobile-cell">Nome Substituído</td>
                                                <td class="n-mobile-cell">Data da solicitação</td>
                                                <td class="n-mobile-cell">Usuário Solicitande</td>
                                               
                                            
                                                <td class="n-mobile-cell" data-orderable="false">Ação</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Exemplo de linha de dados -->
                                            <?php if ($listaReqs1) : ?>
                                                <?php foreach ($listaReqs1 as $key => $dados) : ?>
                                                    <?php $valores = json_decode($dados->valores);
                                                        $fora_prazo ='';
                                                        if(isset($valores->fora_periodo)){
                                                            if($valores->fora_periodo == '2'){
                                                                $fora_prazo = 'style="color: red;" title="Criada fora do prazo como excessão"  ';
                                                            }
                                                        }
                                                    switch ($dados->status) {
                                                        case 0:
                                                            $descricao = "Excluída";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Excluída"
                                                            $corTexto = "#f57878"; // Cor do texto
                                                            break;
                                                        case 1:
                                                            $descricao = "Criada";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Criada"
                                                            $corTexto = "#000000"; // Cor do texto
                                                            break;
                                                        case 2:
                                                            $descricao = "Pend/Ação Gestor";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação gestor"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 3:
                                                            $descricao = "Pend/Ação RH";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 4:
                                                            $descricao = "Sincronizado";
                                                            $corFundo = "#e8ddfc"; // Cor de fundo para "Sincronizado"
                                                            $corTexto = "#8a00c2"; // Cor do texto
                                                            break;
                                                        case 5:
                                                            $descricao = "Reprov. Gestor";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. Gestor"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 6:
                                                            $descricao = "Reprov. RH";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. RH"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 7:
                                                                $descricao = "Pend/Sincronização";
                                                                $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                                $corTexto = "#8a5f00"; // Cor do texto
                                                                break;
                                                        default:
                                                            $descricao = "Todas";
                                                            $corFundo = "#ffffff"; // Cor de fundo padrão
                                                            $corTexto = "#000000"; // Cor do texto padrão
                                                            break;
                                                    }
                                                    
                                                    
                                                    
                                                    
                                                    ?>
                                                    <tr class="text-center">
                                                        <td class="n-mobile-cell" width="20" ><input type="checkbox" name="idbatida1[]"   value="<?= $dados->id ?>:<?= $dados->status ?>"> </td>
                                                        <td class="n-mobile-cell"  <?= $fora_prazo ?>><?= $dados->id ?></td>
                                                        <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                                        <td class="n-mobile-cell"><?= $valores->filial?></td>
                                                        <td class="n-mobile-cell">Mensal</td>
                                                        <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($valores->data_inicio))  ?> - <?= date('d/m/Y', strtotime($valores->data_fim))  ?> </td>
                                                        <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($valores->data_inicio_referencia))  ?> - <?= date('d/m/Y', strtotime($valores->data_fim_referencia))  ?> </td>
                                                        <td class="n-mobile-cell"><?= $valores->dias_referencia ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->Nome ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->funcao ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->Nome_sub ?></td>
                                                        <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($dados->dtcad)) ?></td>
                                                        <td class="n-mobile-cell"><?= $dados->nome ?></td>
                                                        <td class="n-mobile-cell">
                                                        <div class="btn-group dropleft mb-2 mb-md-0">
                                                            <div class="dropdown">
                                                                <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                                <div class="dropdown-menu">
                                                                    <button onclick="aprovarReq('<?= $dados->id; ?>', 1,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status != '1' && $dados->status != '3' && $dados->status != '2' ) ? 'disabled'  : 'style="color: blue;"'; ?> > Aprovar</button>
                                                                    <button onclick="sincReq('<?= $dados->id; ?>', 1,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status == '7') ?'style="color: blue;"' : 'disabled'; ?> > Sincronizar</button>
                                                                    <?php if ($dados->status == '2'): ?>
                                                                     <!-- <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button> -->
                                                                    <?php endif; ?>
                                                                     <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button>
                                                                    <button onclick="verAnexos('<?= $dados->id; ?>','<?= $dados->status; ?>' )" class="dropdown-item  " > Documentos</button>
                                                                    <?php if ($dados->status == '7' && $rh): ?>
                                                                     <button onclick="logCalculo('<?= $dados->id; ?>')" class="dropdown-item">Log de Calculo</button>
                                                                    <?php endif; ?>
                                                                    <a  href="<?= base_url('variaveis/substituicao/editar/'.base64_encode($dados->id)); ?>"   class=" dropdown-item <?= ($dados->status == '2' || $dados->status == '0') ? 'disabled' : ''; ?>">Editar requisição</a>
                                                                
                                                                    <button onclick="reprovarReq('<?= $dados->id; ?>', 1,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: red;">Reprovar</button>

                                                                    <?php if ($rh and $dados->status == '4'): ?>
                                                                      <button onclick="CancSincReq('<?= $dados->id; ?>', 1,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: DarkRed;">Cancelar Sincronismo</button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                            
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                        </tbody>
                                    </table>
                                
                                </div>
                                

                            </div>
                        <div class="tab-pane  p-3 <?= $activeTab == '9'  ? 'active' : '' ?> " id="salario"   role="tabpanel">
                            <div class="row d-flex justify-content-center mb-2">
                                    <div class="mb-2">
                                        <button id="enviaAprovacaoBtn" onclick="return enviaAprovacao(9)" type="button" class="btnpeq btn-sm btn-primary" style="margin-right: 10px;">
                                            <i class="fa fa-check"></i> Aprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <div class="mb-2">
                                        <button id="reprovaBTN" onclick="return enviaReprova(9)" type="button" class="btnpeq btn-sm btn-danger" style="margin-right: 10px;">
                                            <i class="fa fa-times"></i> Reprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <?php if ($rh): ?>
                                        <div class="mb-2">
                                            <button id="enviaSincBtn" onclick="return sincAprovacao(9)" type="button" class="btnpeq btn-sm  btn-success " style="margin-right: 10px;">
                                                <i class="fa fa-check"></i> Sincronizar Selecionado(s)
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>


                                <div class="">
                                    <table width="100%" cellspacing="0" cellpadding="0" id="datatableSalario"  class=" datatable table table-sm table-striped table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th class="n-mobile-cell" data-orderable="false"><input type="checkbox" onchange="toggleButtonVisibility()" id="select-all9"></th>
                                                <td class="n-mobile-cell" width="40" >Nº da requisição</td>
                                                <td class="n-mobile-cell" >Situação</td>
                                                <td class="n-mobile-cell">Filial</td>
                                         
                                                <td class="n-mobile-cell">Tipo</td>
                                                <td class="n-mobile-cell">Nome</td>
                                                <td class="n-mobile-cell">Função</td>
                                                <td class="n-mobile-cell">Data da solicitação</td>
                                                <td class="n-mobile-cell">Usuário Solicitande</td>
                                            
                                                <td class="n-mobile-cell" data-orderable="false">Ação</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Exemplo de linha de dados -->
                                            <?php if ($listaReqs9) : ?>
                                                <?php foreach ($listaReqs9 as $key => $dados) : ?>
                                                    <?php $valores = json_decode($dados->valores);
                                                        $fora_prazo ='';
                                                        if(isset($valores->fora_periodo)){
                                                            if($valores->fora_periodo == '2'){
                                                                $fora_prazo = 'style="color: red;" title="Criada fora do prazo como excessão"  ';
                                                            }
                                                        }
                                                    switch ($dados->status) {
                                                        case 0:
                                                            $descricao = "Excluída";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Excluída"
                                                            $corTexto = "#f57878"; // Cor do texto
                                                            break;
                                                        case 1:
                                                            $descricao = "Criada";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Criada"
                                                            $corTexto = "#000000"; // Cor do texto
                                                            break;
                                                        case 2:
                                                            $descricao = "Pend/Ação Gestor";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação gestor"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 3:
                                                            $descricao = "Pend/Ação RH";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 4:
                                                            $descricao = "Sincronizado";
                                                            $corFundo = "#e8ddfc"; // Cor de fundo para "Sincronizado"
                                                            $corTexto = "#8a00c2"; // Cor do texto
                                                            break;
                                                        case 5:
                                                            $descricao = "Reprov. Gestor";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. Gestor"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 6:
                                                            $descricao = "Reprov. RH";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. RH"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 7:
                                                                $descricao = "Pend/Sincronização";
                                                                $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                                $corTexto = "#8a5f00"; // Cor do texto
                                                                break;
                                                        default:
                                                            $descricao = "Todas";
                                                            $corFundo = "#ffffff"; // Cor de fundo padrão
                                                            $corTexto = "#000000"; // Cor do texto padrão
                                                            break;
                                                    }
                                                    
                                                    
                                                    
                                                    
                                                    ?>
                                                    <tr class="text-center">
                                                        <td class="n-mobile-cell" width="20" ><input type="checkbox" name="idbatida9[]"   value="<?= $dados->id ?>:<?= $dados->status ?>"> </td>
                                                        <td class="n-mobile-cell"  <?= $fora_prazo ?>><?= $dados->id ?></td>
                                                        <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                                        <td class="n-mobile-cell"><?= $valores->filial?></td>
                                                    
                                                        <td class="n-mobile-cell"><?= $dados->tiporeq == '1' ? 'Mensal' : 'Complementar' ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->Nome ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->funcao ?></td>
                                                        <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($dados->dtcad)) ?></td>
                                                        <td class="n-mobile-cell"><?= $dados->nome ?></td>
                                                        <td class="n-mobile-cell">
                                                        <div class="btn-group dropleft mb-2 mb-md-0">
                                                            <div class="dropdown">
                                                                <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                                <div class="dropdown-menu">
                                                                    <button onclick="aprovarReq('<?= $dados->id; ?>', 9,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status != '1' && $dados->status != '3' && $dados->status != '2' ) ? 'disabled'  : 'style="color: blue;"'; ?> > Aprovar</button>
                                                                    <button onclick="sincReq('<?= $dados->id; ?>', 9,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status == '7') ?'style="color: blue;"' : 'disabled'; ?> > Sincronizar</button>
                                                                    <?php if ($dados->status == '2'): ?>
                                                                     <!-- <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button> -->
                                                                    <?php endif; ?>
                                                                     <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button>
                                                                    <button onclick="verAnexos('<?= $dados->id; ?>','<?= $dados->status; ?>' )" class="dropdown-item  " > Documentos</button>
                                                 
                                                                    <a  href="<?= base_url('variaveis/decimoterceiro/editar/'.base64_encode($dados->id)); ?>"   class=" dropdown-item <?= ($dados->status == '2' || $dados->status == '0') ? 'disabled' : ''; ?>">Editar requisição</a>
                                                                
                                                                    <button onclick="reprovarReq('<?= $dados->id; ?>', 9,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: red;">Reprovar</button>

                                                                    <?php if ($rh and $dados->status == '4'): ?>
                                                                      <button onclick="CancSincReq('<?= $dados->id; ?>', 9,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: DarkRed;">Cancelar Sincronismo</button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                            
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                        </tbody>
                                    </table>
                                
                                </div>
                                

                            </div>
                            <!--/ auxilio moradia -->
                        <div class="tab-pane  p-3  <?= $activeTab == '7' || !$activeTab    ? 'active' : '' ?> " id="auxilio_aluguel"  role="tabpanel">
                            <div class="row d-flex justify-content-center mb-2">
                                    <div class="mb-2">
                                        <button id="enviaAprovacaoBtn" onclick="return enviaAprovacao(7)" type="button" class="btnpeq btn-sm btn-primary" style="margin-right: 10px;">
                                            <i class="fa fa-check"></i> Aprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <div class="mb-2">
                                        <button id="reprovaBTN" onclick="return enviaReprova(7)" type="button" class="btnpeq btn-sm btn-danger" style="margin-right: 10px;">
                                            <i class="fa fa-times"></i> Reprovar Selecionado(s)
                                        </button>
                                    </div>
                                    <?php if ($rh): ?>
                                        <div class="mb-2">
                                            <button id="enviaSincBtn" onclick="return sincAprovacao(7)" type="button" class="btnpeq btn-sm  btn-success " style="margin-right: 10px;">
                                                <i class="fa fa-check"></i> Sincronizar Selecionado(s)
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>


                                <div class="">
                                    <table width="100%" cellspacing="0" cellpadding="0" id="datatableAluguel"  class=" datatable table table-sm table-striped table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th class="n-mobile-cell" data-orderable="false"><input type="checkbox" onchange="toggleButtonVisibility()" id="select-all7"></th>
                                                <td class="n-mobile-cell" width="40" >Nº da requisição</td>
                                                <td class="n-mobile-cell" >Situação</td>
                                                <td class="n-mobile-cell">Filial</td>
                                             
                                                <td class="n-mobile-cell">Tipo</td>
                                                <td class="n-mobile-cell">Valor</td>
                                                <td class="n-mobile-cell">Nome</td>
                                                <td class="n-mobile-cell">Função</td>
                                                <td class="n-mobile-cell">Data da solicitação</td>
                                                <td class="n-mobile-cell">Usuário Solicitande</td>
                                            
                                                <td class="n-mobile-cell" data-orderable="false">Ação</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Exemplo de linha de dados -->
                                            <?php if ($listaReqs7) : ?>
                                                <?php foreach ($listaReqs7 as $key => $dados) : ?>
                                                    <?php $valores = json_decode($dados->valores);
                                                        $fora_prazo ='';
                                                        if(isset($valores->fora_periodo)){
                                                            if($valores->fora_periodo == '2'){
                                                                $fora_prazo = 'style="color: red;" title="Criada fora do prazo como excessão"  ';
                                                            }
                                                        }
                                                    switch ($dados->status) {
                                                        case 0:
                                                            $descricao = "Excluída";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Excluída"
                                                            $corTexto = "#f57878"; // Cor do texto
                                                            break;
                                                        case 1:
                                                            $descricao = "Criada";
                                                            $corFundo = "#f0f0f0"; // Cor de fundo para "Criada"
                                                            $corTexto = "#000000"; // Cor do texto
                                                            break;
                                                        case 2:
                                                            $descricao = "Pend/Ação Gestor";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação gestor"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 3:
                                                            $descricao = "Pend/Ação RH";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                        case 4:
                                                            $descricao = "Sincronizado";
                                                            $corFundo = "#e8ddfc"; // Cor de fundo para "Sincronizado"
                                                            $corTexto = "#8a00c2"; // Cor do texto
                                                            break;
                                                        case 5:
                                                            $descricao = "Reprov. Gestor";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. Gestor"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 6:
                                                            $descricao = "Reprov. RH";
                                                            $corFundo = "#f7baba"; // Cor de fundo para "Reprov. RH"
                                                            $corTexto = "#d10000"; // Cor do texto
                                                            break;
                                                        case 7:
                                                                $descricao = "Pend/Sincronização";
                                                                $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                                $corTexto = "#8a5f00"; // Cor do texto
                                                                break;
                                                        default:
                                                            $descricao = "Todas";
                                                            $corFundo = "#ffffff"; // Cor de fundo padrão
                                                            $corTexto = "#000000"; // Cor do texto padrão
                                                            break;
                                                    }
                                                    
                                                    
                                                    
                                                    
                                                    ?>
                                                    <tr class="text-center">
                                                        <td class="n-mobile-cell" width="20" ><input type="checkbox" name="idbatida7[]"   value="<?= $dados->id ?>:<?= $dados->status ?>"> </td>
                                                        <td class="n-mobile-cell"  <?= $fora_prazo ?>><?= $dados->id ?></td>
                                                        <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                                        <td class="n-mobile-cell"><?= $valores->filial?></td>
                                                       
                                                        <td class="n-mobile-cell"><?= $dados->tiporeq == '1' ? 'Mensal' : 'Complementar' ?></td>
                                                        <td class="n-mobile-cell"> R$ <?= $valores->valor ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->Nome ?></td>
                                                        <td class="n-mobile-cell"><?= $valores->funcao ?></td>
                                                        <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($dados->dtcad)) ?></td>
                                                        <td class="n-mobile-cell"><?= $dados->nome ?></td>
                                                        <td class="n-mobile-cell">
                                                        <div class="btn-group dropleft mb-2 mb-md-0">
                                                            <div class="dropdown">
                                                                <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                                <div class="dropdown-menu">
                                                                    <button onclick="aprovarReq('<?= $dados->id; ?>', 7,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status != '1' && $dados->status != '3' && $dados->status != '2' ) ? 'disabled'  : 'style="color: blue;"'; ?> > Aprovar</button>
                                                                    <button onclick="sincReq('<?= $dados->id; ?>', 7,'<?= $dados->status; ?>')" class="dropdown-item" <?= ($dados->status == '7') ?'style="color: blue;"' : 'disabled'; ?> > Sincronizar</button>
                                                                    
                                                                    <?php if ($dados->status == '2'): ?>
                                                                     <!-- <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button> -->
                                                                    <?php endif; ?>
                                                                     <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button>
                                                                    <button onclick="verAnexos('<?= $dados->id; ?>','<?= $dados->status; ?>' )" class="dropdown-item  " > Documentos</button>
                                                 
                                                                    <a  href="<?= base_url('variaveis/aluguel/editar/'.base64_encode($dados->id)); ?>"   class=" dropdown-item <?= ($dados->status == '2' || $dados->status == '0') ? 'disabled' : ''; ?>">Editar requisição</a>
                                                                
                                                                    <button onclick="reprovarReq('<?= $dados->id; ?>', 7,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: red;">Reprovar</button>

                                                                    <?php if ($rh and $dados->status == '4'): ?>
                                                                      <button onclick="CancSincReq('<?= $dados->id; ?>', 7,'<?= $dados->status; ?>')" class="dropdown-item <?= ($dados->status == '0') ? 'disabled' : ''; ?>" style="color: DarkRed;">Cancelar Sincronismo</button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                            
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                        </tbody>
                                    </table>
                                
                                </div>
                                

                            </div>
                        <div class="tab-pane  p-3" id="historico"  role="tabpanel">
                            <div class="">
                               
                                <table width="100%" cellspacing="0" cellpadding="0"  id="historico1" class="datatable  table table-sm table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                           
                                            <td class="n-mobile-cell" width="40" >Nº da requisição</td>
                                            <td class="n-mobile-cell" width="40">Situação Atual</td>
                                            <td class="n-mobile-cell" width="40">Tipo</td>
                                          
                                            <td class="n-mobile-cell">Nome</td>
                                            <td class="n-mobile-cell">Data da Ação</td>
                                          
                                            <td class="n-mobile-cell">Usuário Aprovador</td>
                                            <td class="n-mobile-cell" data-orderable="false">Ação</td>
                                          
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Exemplo de linha de dados -->
                                        <?php if ($historico) : ?>
                                            <?php foreach ($historico as $key => $dados) : ?>
                                                <?php 
                                                 $valores = json_decode($dados->valores); 
                                                 switch ($dados->status) {
                                                    case 0:
                                                        $descricao = "Excluída";
                                                        $corFundo = "#f0f0f0"; // Cor de fundo para "Excluída"
                                                        $corTexto = "#f57878"; // Cor do texto
                                                        break;
                                                    case 1:
                                                        $descricao = "Criada";
                                                        $corFundo = "#f0f0f0"; // Cor de fundo para "Criada"
                                                        $corTexto = "#000000"; // Cor do texto
                                                        break;
                                                    case 2:
                                                        $descricao = "Pend/Ação gestor";
                                                        $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação gestor"
                                                        $corTexto = "#8a5f00"; // Cor do texto
                                                        break;
                                                    case 3:
                                                        $descricao = "Pend/Ação RH";
                                                        $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                        $corTexto = "#8a5f00"; // Cor do texto
                                                        break;
                                                    case 4:
                                                        $descricao = "Sincronizado";
                                                        $corFundo = "#e8ddfc"; // Cor de fundo para "Sincronizado"
                                                        $corTexto = "#8a00c2"; // Cor do texto
                                                        break;
                                                    case 5:
                                                        $descricao = "Reprov. Gestor";
                                                        $corFundo = "#f7baba"; // Cor de fundo para "Reprov. Gestor"
                                                        $corTexto = "#d10000"; // Cor do texto
                                                        break;
                                                    case 6:
                                                        $descricao = "Reprov. RH";
                                                        $corFundo = "#f7baba"; // Cor de fundo para "Reprov. RH"
                                                        $corTexto = "#d10000"; // Cor do texto
                                                        break;
                                                    case 7:
                                                        $descricao = "Pend/Sincronização";
                                                        $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                        $corTexto = "#8a5f00"; // Cor do texto
                                                        break;
                                                    case 8:
                                                            $descricao = "Preenchimento RH ";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                    case 9:
                                                            $descricao = "Pend/Documento";
                                                            $corFundo = "#fce388"; // Cor de fundo para "Pend/Ação RH"
                                                            $corTexto = "#8a5f00"; // Cor do texto
                                                            break;
                                                    
                                                }

                                                    switch ($dados->tipo) {
                                                        case 6:
                                                            $tipo = "Auxilio Moradia";
                                                            $url =  base_url('variaveis/moradia/editar/'.base64_encode($dados->id_requisicao));
                                                            break;
                                                        case 4:
                                                            $tipo = "Auxilio Excepcional";
                                                            $url =  base_url('variaveis/pcd/editar/'.base64_encode($dados->id_requisicao));
                                                            break;
                                                        case 9:
                                                            $tipo = "13º Salário";
                                                            $url =  base_url('variaveis/decimoterceiro/editar/'.base64_encode($dados->id_requisicao));
                                                            break;
                                                        case 8:
                                                            if($dados->tiporeq == 1){
                                                                $tipo = "Coparticipação Bradesco";
                                                            }else{
                                                                $tipo = "Coparticipação Unimed";
                                                            }
                                                          
                                                            $url =  base_url('variaveis/coparticipacao/editar/'.base64_encode($dados->id_requisicao));
                                                             
                                                            break;
                                                        case 7:
                                                            $tipo = "Auxilio Aluguel";
                                                            $url =  base_url('variaveis/aluguel/editar/'.base64_encode($dados->id_requisicao));
                                                                 
                                                            break;
                                                        case 2:
                                                            $tipo = "Auxilio Creche";
                                                            $url =  base_url('variaveis/creche/editar/'.base64_encode($dados->id_requisicao));
                                                                      
                                                            break;
                                                        case 1:
                                                            $tipo = "Salário Substituição";
                                                            $url =  base_url('variaveis/substituicao/editar/'.base64_encode($dados->id_requisicao));
                                                                          
                                                            break;
                                                        case 3:
                                                            $tipo = "Sobreaviso";
                                                            $url =  base_url('variaveis/sobreaviso/editar/'.base64_encode($dados->id_requisicao));
                                                                              
                                                            break;
                                                        case 5:
                                                            if( $dados->tiporeq == '1' ){
                                                                $tipo = 'Desconto Autorizado';
                                                            }elseif($dados->tiporeq == '2'){
                                                                $tipo = 'Desconto de EPIs';
                                                            }else{
                                                                $tipo = 'Desconto: Multa de Trânsito';
                                                            }
                                                            $url =  base_url('variaveis/desconto/editar/'.base64_encode($dados->id_requisicao));
                                                                                  
                                                            break;
                                                    }

                                                ?>
                                                <tr class="text-center">
                                                    <td class="n-mobile-cell"><?= $dados->id_requisicao ?></td>
                                                    <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                    
                                                    <td class="n-mobile-cell"><?=  $tipo ?></td>
                                                   
                                                    <td class="n-mobile-cell"><?= $valores->Nome ?></td>
                                                    <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($dados->dtcad)) ?></td>
                                                    <td class="n-mobile-cell"><?= $dados->nome ?></td>
                                                    <td class="n-mobile-cell">
                                                        <div class="btn-group dropleft mb-2 mb-md-0">
                                                        <button onclick="historicoAprovacao('<?= $dados->id_requisicao; ?>')" class="btn btn-soft-primary pl-1 pr-1"  aria-expanded="true"> <i class="mdi mdi-history"></i></button>
                                
                                                            <div class="dropdown">
                                                                <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                                <div class="dropdown-menu">
                                                                    <a  href="<?= $url ?>" target="_blank"    class=" dropdown-item ">Ver requisição</a>
                                                                    <?php if ($dados->status == '4' && ($dados->tipo == 5 || $dados->tipo == 1) ): ?>
                                                                     <button onclick="logCalculo('<?= $dados->id_requisicao; ?>')" class="dropdown-item">Log de Calculo</button>
                                                                    <?php endif; ?>
                                                                    <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button>
                                                                    <button onclick="verAnexos('<?= $dados->id_requisicao; ?>','<?= $dados->status; ?>' )" class="dropdown-item  " > Documentos</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                            
                                                    </td>
                                                   
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
			</div><!-- end card -->
		</div><!-- end col-12 -->
		
	</div><!-- end row -->
</div><!-- end container -->

<div class="modal modal_resumo_diario" tabindex="-1" role="dialog" aria-labelledby="modal_resumo_diario" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-full">
        <div class="modal-content modal-content-full">
            <div class="modal-header bg-dark">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-file-document-box-outline"></i> Justificativa</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        <form action="" method="post" enctype="multipart/form-data">
                            <table class="table" style="font-size: 16px;">
                                <thead>
                                    <tr>
                                       
                                        <th width="60" class="text-rigth"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal_Anexos" tabindex="-1" role="dialog" aria-labelledby="modal_Anexos" aria-hidden="true" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" style="max-width: 50%; width: 50%;">
        <div class="modal-content modal-content-full">
            <div class="modal-header bg-dark">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-file-document-box-outline"></i> Anexos</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        <form action="" method="post" enctype="multipart/form-data">
                            <table class="table" style="font-size: 16px;">
                                <thead>
                                    <tr>
                                       
                                        <th width="60" class="text-rigth">Anexo</th>
                                        <th width="60" class="text-rigth">Ação</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="justificativa" id="anx" class="col-sm-2 col-form-label text-right text-left-sm">Anexar:</label>
                    <div class="col-sm-10">
                        <input class="form-control filepond" type="file" name="anexo[]" id="anexo" multiple required>
                        <input class="form-control" hidden type="text" value="" name="id" id="id" required>
                    </div>
                </div>
                <div class="card-footer text-center">
      
                    <button class="btn btn-success " style="visibility: hidden;"  id="btnSaveTermo" onclick="return salvaTermo()"><i class="fas fa-check"></i> Enviar Documento para o RH</button>
                    <button class="btn btn-success bteldorado_1"  id="btnsaveAnexo" onclick="return salvaAnexo()"><i class="fas fa-check"></i> Salvar</button>
                    <button type="button" class="btn btn-primary" style="visibility: hidden;" id="btnTermoSubstituicao"><i class="fas fa-file-pdf"></i> Autorização de Desconto</button>
                
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal_historico" tabindex="-1" role="dialog" aria-labelledby="modal_historico" aria-hidden="true" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" style="max-width: 50%; width: 50%;">
        <div class="modal-content modal-content-full">
            <div class="modal-header bg-dark">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-file-document-box-outline"></i> Historico</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        <form action="" method="post" enctype="multipart/form-data">
                            <table class="table" style="font-size: 16px;">
                                <thead>
                                    <tr>
                                       
                                        <th width="60" class="text-rigth">Situação</th>
                                        <th width="60" class="text-rigth">Nº req</th>
                                       
                                        <th width="60" class="text-rigth">Data</th>
                                        <th width="60" class="text-rigth">Usuário Solicitante</th>
                                        <th width="60" class="text-rigth">Usuário Aprovador</th>
                                        <th width="60" class="text-rigth">Justificativa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal_calculo" tabindex="-1" role="dialog" aria-labelledby="modal_calculo" aria-hidden="true" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" style="max-width: 50%; width: 50%;">
        <div class="modal-content modal-content-full">
            <div class="modal-header bg-dark">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-file-document-box-outline"></i> Ações Sincronizadas</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        <form action="" method="post" enctype="multipart/form-data">
                            <table class="table" style="font-size: 16px;">
                                <thead>
                                    <tr>
                                       
                                       
                                        <th width="60" class="text-rigth">Nº req</th>
                                       
                                        <th width="60" class="text-rigth">Referencia</th>
                                        <th width="60" class="text-rigth">Valor</th>
                                        <th width="60" class="text-rigth">Data da Ação</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
.disabled {
    pointer-events: none;
    color: grey; /* Estilização para parecer desabilitado */
}
.anexo-claro {
    background-color: #f0f0f0; /* Cor mais clara */
}
</style>
<script>
    const abrirResumoDiario = (justificativa) => {

        $(".modal_resumo_diario").modal('show');

        $('.modal_resumo_diario tbody').html('');

        $('.modal_resumo_diario tbody').append('<tr><td class="">' + justificativa + '</td></tr>');
    }
    
    const historicoAprovacao = (id) => {

$('.modal_historico tbody').html('');

$.ajax({
    url: "<?= base_url('variaveis/aprovacao/historico'); ?>",
    type: "POST",
    data: {
        "id": id
    },
    success: function (response) {
    
        swal.close();

        response.forEach(function(historico, i) {
            $(".modal_historico tbody").append(`
                <tr>
                    <td>${historico.acao}</td>
                    <td>${historico.req}</td>
                  
                    <td>${historico.data}</td>
                    <td>${historico.solicitante}</td>
                    <td>${historico.gestor}</td>
                    <td>${historico.comentario}</td>
                </tr>
            `);
        });

        $(".modal_historico").modal("show");
    },
    beforeSend: function () {
        Swal.fire({
            title: 'Carregando histórico...',
            html: 'Por favor, aguarde...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },
    error: function (response) {
        Swal.fire(
            response.responseJSON.title,
            response.responseJSON.message,
            response.responseJSON.status
        );
    },
});

}


$('#btnTermoSubstituicao').click(function(e) {
    e.preventDefault();
    let id = $("#id").val();

    
    $.ajax({
        url: '<?= base_url('variaveis/desconto/substituicaoTermo') ?>',
        type: 'GET',
        data: { id: id }, // Passa os dados como um objeto
        xhrFields: {
            responseType: 'blob'
        },
        success: function(response) {
            var link = document.createElement('a');
            var url = window.URL.createObjectURL(response);
            link.href = url;
            link.download = 'Termo_desconto.pdf';
            document.body.append(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        },
        error: function(xhr, status, error) {
            console.error('Erro ao gerar o termo: ', error);
        }
    });
});


const salvaTermo = () => {
        // Obtenha os arquivos selecionados
        let arquivos = $('#anexo')[0].files;

        if (arquivos.length === 0) {
            Swal.fire('Erro', 'Nenhum arquivo selecionado.', 'error');
            return false;
        }
        
        // Definir extensões permitidas
        const extensoesPermitidas = ['pdf', 'jpeg', 'jpg', 'doc', 'doc', 'docx', 'png', 'gif', 'tiff', 'webp', 'bmp'];
        let extensaoInvalida = false;

        // Validar extensões dos arquivos
        $.each(arquivos, function(i, file) {
            const extensaoArquivo = file.name.split('.').pop().toLowerCase();
            if (!extensoesPermitidas.includes(extensaoArquivo)) {
                extensaoInvalida = true;
                return false; // Interromper o loop ao encontrar uma extensão inválida
            }
        });

        if (extensaoInvalida) {
            Swal.fire('Erro', 'Apenas arquivos PDF, DOC, DOCx e imagens são permitidos.', 'error');
            return false;
        }

        // Cria um objeto FormData para enviar os arquivos
        let formData = new FormData();
        $.each(arquivos, function(i, file) {
            formData.append('anexo[]', file);
        });

        // Adicione outros dados que você queira enviar, se necessário
        formData.append("id", $("#id").val());

        $.ajax({
            url: "<?= base_url('variaveis/desconto/salvarAnexo2'); ?>",  // Altere para a URL do seu endpoint de upload
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                var response = JSON.parse(result);

               
                if (response.tipo != 'success') {
                    exibeAlerta(response.tipo, response.msg, 2);
                } else {
                    $(".modal_Anexos").modal("hide");
                    exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/desconto'); ?>');
                }
            },
            error: function () {
                Swal.fire('Erro', 'Ocorreu um erro ao salvar os anexos.', 'error');
            }
        });

        return false; // Prevenir o recarregamento da página
    }

const logCalculo = (id) => {

$('.modal_calculo tbody').html('');

$.ajax({
    url: "<?= base_url('variaveis/aprovacao/logCalculo'); ?>",
    type: "POST",
    data: {
        "id": id
    },
    success: function (response) {
        console.log(response);
        swal.close();
        if (response?.[0]?.tipo !== undefined && response[0].tipo == 5) {
                response.forEach(function(historico, i) {
                    $(".modal_calculo tbody").append(`
                        <tr>
                            <td>${historico.req}</td>
                            <td>${historico.mes}/${historico.ano} - ${historico.novoMes}/${historico.novoAno}</td>
                            <td>${historico.val}</td>
                            <td>${historico.data}</td>
                        </tr>
                    `);
                });
            } else {
                response.forEach(function(historico, i) {
                    $(".modal_calculo tbody").append(`
                        <tr>
                            <td>${historico.req}</td>
                            <td>${historico.data_inicio_referencia} - ${historico.data_fim_referencia}</td>
                            <td>${historico.val}</td>
                            <td>${historico.data}</td>
                        </tr>
                    `);
                });
            }
       

        $(".modal_calculo").modal("show");
    },
    beforeSend: function () {
        Swal.fire({
            title: 'Carregando histórico...',
            html: 'Por favor, aguarde...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },
    error: function (response) {
        Swal.fire(
            response.responseJSON.title,
            response.responseJSON.message,
            response.responseJSON.status
        );
    },
});

}
    const save = (tipo) => {

        switch(tipo){
            case 4: 
                var dados = validaTipo4();
                if(!dados) return;
            break;
            case 6: 
                var dados = validaTipo6();
                if(!dados) return;
            break;
        }

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
    $('#select-all2').on('click', function() {
        $('input[name="idbatida2[]"]').prop('checked', this.checked);
    });
    $('#select-all4').on('click', function() {
        $('input[name="idbatida4[]"]').prop('checked', this.checked);
    });
    $('#select-all6').on('click', function() {
        $('input[name="idbatida6[]"]').prop('checked', this.checked);
    });
    $('#select-all7').on('click', function() {
        $('input[name="idbatida7[]"]').prop('checked', this.checked);
    });
    $('#select-all8').on('click', function() {
        $('input[name="idbatida8[]"]').prop('checked', this.checked);
    });
    $('#select-all9').on('click', function() {
        $('input[name="idbatida9[]"]').prop('checked', this.checked);
    });
    $('#select-all1').on('click', function() {
        $('input[name="idbatida1[]"]').prop('checked', this.checked);
    });
    $('#select-all3').on('click', function() {
        $('input[name="idbatida3[]"]').prop('checked', this.checked);
    });
    $('#select-all5').on('click', function() {
        $('input[name="idbatida5[]"]').prop('checked', this.checked);
    });
    $('.btn-toggle').on('click', function() {
            var $button = $(this);
            var $detailRow = $button.closest('tr').next('.detail-row');

            if ($detailRow.is(':visible')) {
                $detailRow.hide();
                $button.text('+');
            } else {
                $detailRow.show();
                $button.text('−'); // Muda o texto do botão para um menos (−) ao expandir
            }
    });

    const reprovarReq = (id,tipo, status) => {
        // Perguntar ao usuário se deseja aprovar usando Swal, incluindo um campo de texto para a justificativa
        Swal.fire({
            icon: 'question',
            title: 'Deseja Reprovar essa requisição?',
            input: 'textarea', // Tipo de input como textarea para a justificativa
            inputLabel: '',
            inputPlaceholder: 'Insira sua justificativa aqui...',
            inputAttributes: {
                'aria-label': 'Insira sua justificativa aqui'
            },
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Sim, confirmar',
            denyButtonText: 'Cancelar',
            showCloseButton: false,
            allowOutsideClick: false,
            width: 600,
            preConfirm: (justificativa) => {
                if (!justificativa) {
                    Swal.showValidationMessage('A justificativa é obrigatória');
                    return false;
                }
                return justificativa;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let dados = {
                    "idStatus": [{
                        id: id,
                        status: status
                    }],
                    "tipo": tipo,
                    "justificativa": result.value // Envia a justificativa junto com o ID
                };
                openLoading();
                $.ajax({
                    url: "<?= base_url('variaveis/sincronizacao/reprovaRH'); ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg, 2);
                        } else {
                            exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/sincronizacao/index/'); ?>/'+tipo+'');
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar aprovar a requisição.', 2);
                    }
                });
            }
        });
    };

    const validaTipo4 = () => {

        var dados = {
            'tipo': 4,
            
            'reembolso_cpd_valor_demais_filiais': $('#reembolso_cpd_valor_demais_filiais').val().replaceAll('.', '').replaceAll(',', '.'),
            'reembolso_cpd_evento': $('#reembolso_cpd_evento').val(),
            'reembolso_cpd_secao': $('#reembolso_cpd_secao').val(),
            "dependentes": [] // Adicionando o array de dependentes
        }

        $("#dependentesTable tbody tr").each(function() {
            let dependente = {
                "nome": $(this).find("td:eq(0)").text(),
                "valor": $(this).find("td:eq(1) input").val() // Captura o valor do input
            };
            dados.dependentes.push(dependente);
        });

        if(dados.reembolso_cpd_valor_filiais == ''){exibeAlerta('error', 'Valor da filial 01/02 não informado.'); return false;}
        if(dados.reembolso_cpd_valor_demais_filiais == ''){exibeAlerta('error', 'Valor das demais filiais não informado.'); return false;}
        if(dados.reembolso_cpd_evento == ''){exibeAlerta('error', 'Evento do reembolso pcd não informado.'); return false;}

        return dados;
        
    }
    const salvaAnexo = () => {
        // Obtenha os arquivos selecionados
        let arquivos = $('#anexo')[0].files;

        if (arquivos.length === 0) {
            Swal.fire('Erro', 'Nenhum arquivo selecionado.', 'error');
            return false;
        }
          
        // Definir extensões permitidas
        const extensoesPermitidas = ['pdf', 'jpeg', 'jpg', 'doc', 'doc', 'docx', 'png', 'gif', 'tiff', 'webp', 'bmp'];
        let extensaoInvalida = false;

        // Validar extensões dos arquivos
        $.each(arquivos, function(i, file) {
            const extensaoArquivo = file.name.split('.').pop().toLowerCase();
            if (!extensoesPermitidas.includes(extensaoArquivo)) {
                extensaoInvalida = true;
                return false; // Interromper o loop ao encontrar uma extensão inválida
            }
        });

        if (extensaoInvalida) {
            Swal.fire('Erro', 'Apenas arquivos PDF, DOC, DOCx e imagens são permitidos.', 'error');
            return false;
        }

        // Cria um objeto FormData para enviar os arquivos
        let formData = new FormData();
        $.each(arquivos, function(i, file) {
            formData.append('anexo[]', file);
        });

        // Adicione outros dados que você queira enviar, se necessário
        formData.append("id", $("#id").val());

        $.ajax({
            url: "<?= base_url('variaveis/moradia/salvarAnexo'); ?>",  // Altere para a URL do seu endpoint de upload
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                var response = JSON.parse(result);

               
                if (response.tipo != 'success') {
                    exibeAlerta(response.tipo, response.msg, 2);
                } else {
                    $(".modal_Anexos").modal("hide");
                    exibeAlerta(response.tipo, response.msg, 3);
                }
            },
            error: function () {
                Swal.fire('Erro', 'Ocorreu um erro ao salvar os anexos.', 'error');
            }
        });

        return false; // Prevenir o recarregamento da página
    }
    
    const verAnexos = (id, status, tipo = false) => {
        const novoTitulo = `Anexos - ${id}`; // Exemplo de como construir o novo título
        $(".modal_Anexos .modal-title").text(novoTitulo);
    $('.modal_Anexos tbody').html('');
    $('#anexo').val(''); // Limpa o campo de anexo
    $("#id").val(id);
    let disableDelete =  '';
     if (status == 5 || status == 6 || status == 4) {
         disableDelete =  'disabled';
         $('#anexo').prop('disabled', true);
     } else {
        disableDelete =  '';
         $('#anexo').prop('disabled', false);
     }

    
      
     

        if (status == 9) {
            $('#anexo').prop('disabled', false);
            $('#btnTermoSubstituicao').css('visibility', 'visible'); // Torna o botão visível
            $('#btnSaveTermo').css('visibility', 'visible'); // Torna o botão visível
            $("#btnsaveAnexo").hide();
        } else {
            if(tipo == 8){
                $("#btnsaveAnexo").hide();
                $('#anexo').hide();
                $('#anx').hide();
            }else{
                disableDelete =  '';
                $('#anexo').prop('disabled', false);
                $('#anexo').show();
                $('#btnsaveAnexo').show();
                $('#anx').show();
                
            }
            $('#btnTermoSubstituicao').css('visibility', 'hidden'); // Mantém o botão oculto
            $('#btnSaveTermo').css('visibility', 'hidden'); // Mantém o botão oculto
        }


    $.ajax({
        url: "<?= base_url('variaveis/moradia/Anexos'); ?>",
        type: "POST",
        data: {
            "id": id
        },
        success: function (response) {
            swal.close();
            if (Array.isArray(response) && response.length > 0) {
                let anexoCounter = 0;
                response.forEach(anexo => {
                    anexoCounter++;
                   
                        let rowClass = anexoCounter != 1 ? 'anexo-claro' : '';

                        let row = `
                            <tr id="anexo_${anexo.id}" class="${rowClass}">
                                <td style="width: 80%;"><a href="data:${anexo.file_type};base64,${anexo.file_data}" download="${anexo.file_name}">
                                ${anexoCounter}: ${anexo.file_name}
                                </a></td>
                                <td><button type="button" class="btn btn-danger btn-sm" ${disableDelete} onclick="excluirAnexo(${anexo.id})"><i class="mdi mdi-delete"></i> Excluir</button></td>
                            </tr>
                        `;

                    $('.modal_Anexos tbody').append(row);
                });
            }

                $(".modal_Anexos").modal("show");
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Carregando Anexos...',
                    html: 'Por favor, aguarde...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            error: function (response) {
                Swal.fire(
                    response.responseJSON.title,
                    response.responseJSON.message,
                    response.responseJSON.status
                );
            },
        });
    }


    const validaAnexos = (id, callback) => {
        $.ajax({
            url: "<?= base_url('variaveis/moradia/Anexos'); ?>",
            type: "POST",
            data: { id: id },
            success: function (response) {
                if (Array.isArray(response) && response.length > 0) {
                    callback(true);  // Chama o callback com true
                } else {
                    callback(false); // Chama o callback com false
                }
            },
            error: function () {
                console.error('Erro na requisição AJAX');
                callback(false); // Em caso de erro, retorna false
            }
        });
    };
    const excluirAnexo = (id ) => {
        let totalLinhas = $('.modal_Anexos tbody tr').length;

        if (totalLinhas <= 1) {
            Swal.fire('Erro', 'Você não pode excluir o único anexo restante.', 'error');
            return false;
        }

        // Perguntar ao usuário se deseja excluir usando Swal
        Swal.fire({
            icon: 'question',
            title: 'Deseja excluír esse Anexo?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Sim, confirmar',
            denyButtonText: 'Cancelar',
            showCloseButton: false,
            allowOutsideClick: false,
            width: 600,
        }).then((result) => {
            if (result.isConfirmed) {
                let dados = {
                    "id": id,
                }
                
                $.ajax({
                    url: "<?= base_url('variaveis/moradia/deleteAnexo'); ?>",
                    type: 'POST',
                    data: dados, // Enviar o ID para exclusão
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg, 2);
                        } else {
                            $(`#anexo_${id}`).remove();
                            exibeAlerta(response.tipo, response.msg, 3);
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar excluir o anexo.', 2);
                    }
                });
            }
        });
    };
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

    const aprovarReq = (id, tipo, status) => {
        // Perguntar ao usuário se deseja aprovar usando Swal, incluindo um campo de texto para a justificativa

        let texto ='';
        if(status == '8'){
            texto ='<span style="color: red;"> Uma ou mais das requisições irão voltar para o gestor, tem certeza que está tudo preenchido?  </span>';
        }
        Swal.fire({
            icon: 'question',
            title: 'Deseja Aprovar essa requisição?',
            html: texto,
            input: 'textarea', // Tipo de input como textarea para a justificativa
            inputLabel: '',
            inputPlaceholder: 'Insira sua justificativa aqui...',
            inputAttributes: {
                'aria-label': 'Insira sua justificativa aqui'
            },
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Sim, confirmar',
            denyButtonText: 'Cancelar',
            showCloseButton: false,
            allowOutsideClick: false,
            width: 600,
            preConfirm: (justificativa) => {
                return justificativa;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let dados = {
                    "idStatus": [{
                        id: id,
                        status: status
                    }],
                    "tipo": tipo,
                    "justificativa": result.value // Envia a justificativa junto com o ID e status
                };
                openLoading();
                $.ajax({
                    url: "<?= base_url('variaveis/sincronizacao/aprovarGestor'); ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg, 2,'<?= base_url('variaveis/sincronizacao/index/'); ?>/'+tipo+'');
                        } else {
                            exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/sincronizacao/index/'); ?>/'+tipo+'');
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar aprovar a requisição.', 2);
                    }
                });
            }
        });
    };

    
    const sincReq = (id, tipo, status) => {
        // Perguntar ao usuário se deseja aprovar usando Swal, incluindo um campo de texto para a justificativa
        Swal.fire({
            icon: 'question',
            title: 'Deseja Sincronizar essa requisição?',
            input: 'textarea', // Tipo de input como textarea para a justificativa
            inputLabel: '',
            inputPlaceholder: 'Insira sua justificativa aqui...',
            inputAttributes: {
                'aria-label': 'Insira sua justificativa aqui'
            },
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Sim, confirmar',
            denyButtonText: 'Cancelar',
            showCloseButton: false,
            allowOutsideClick: false,
            width: 600,
            preConfirm: (justificativa) => {
                return justificativa;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let dados = {
                    "idStatus": [{
                        id: id,
                        status: status
                    }],
                    "tipo": tipo,
                    "justificativa": result.value // Envia a justificativa junto com o ID e status
                };
                openLoading();
                $.ajax({
                    url: "<?= base_url('variaveis/sincronizacao/sincronizaGestor'); ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg, 2);
                        } else {
                            Filtro();
                            //exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/sincronizacao'); ?>');
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar aprovar a requisição.', 2);
                    }
                });
            }
        });
    };

    const CancSincReq = (id, tipo, status) => {
        // Perguntar ao usuário se deseja aprovar usando Swal, incluindo um campo de texto para a justificativa
        Swal.fire({
            icon: 'question',
            title: 'Deseja Cancelar o Sincronismo da Requisição?',
            input: 'textarea', // Tipo de input como textarea para a justificativa
            inputLabel: '',
            inputPlaceholder: 'Insira sua justificativa aqui...',
            inputAttributes: {
                'aria-label': 'Insira sua justificativa aqui'
            },
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Sim, cancelar',
            denyButtonText: 'Não',
            showCloseButton: false,
            allowOutsideClick: false,
            width: 600,
            preConfirm: (justificativa) => {
                return justificativa;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let dados = {
                    "idStatus": [{
                        id: id,
                        status: status
                    }],
                    "tipo": tipo,
                    "justificativa": result.value // Envia a justificativa junto com o ID e status
                };
                openLoading();
                $.ajax({
                    url: "<?= base_url('variaveis/sincronizacao/cancelaSincronismo'); ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg, 2);
                        } else {
                            Filtro();
                            //exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/sincronizacao'); ?>');
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar cancelar sincronismo.', 2);
                    }
                });
            }
        });
    };

    const enviaAprovacao = (tipo) => {
        // Captura todos os checkboxes marcados
        let idStatusList = [];
        let texto ='';
        if(tipo == 1){
            $('input[name="idbatida1[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 2){
            $('input[name="idbatida2[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 3){
            $('input[name="idbatida3[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 4){
            $('input[name="idbatida4[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 5){
            $('input[name="idbatida5[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
                if(valor[1] == '8'){
                    texto ='<span style="color: red;"> Uma ou mais das requisições irão voltar para o gestor, tem certeza que está tudo preenchido?  </span>';
                }else{
                    texto ='';
                }
            });
        } else if(tipo == 6){
            $('input[name="idbatida6[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 7){
            $('input[name="idbatida7[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 8){
            $('input[name="idbatida8[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 9){
            $('input[name="idbatida9[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        }


        if (idStatusList.length === 0) {
            exibeAlerta('warning', 'Nenhuma requisição selecionada.', 2);
            return;
        }

        // Perguntar ao usuário se deseja aprovar usando Swal, incluindo um campo de texto para a justificativa
        Swal.fire({
            icon: 'question',
            title: 'Deseja Aprovar essas requisições?',
            html: texto,
            input: 'textarea', // Tipo de input como textarea para a justificativa
            inputLabel: '',
            inputPlaceholder: 'Insira sua justificativa aqui...',
            inputAttributes: {
                'aria-label': 'Insira sua justificativa aqui'
            },
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Sim, confirmar',
            denyButtonText: 'Cancelar',
            showCloseButton: false,
            allowOutsideClick: false,
            width: 600,
            preConfirm: (justificativa) => {
                return justificativa;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let dados = {
                    "idStatus": idStatusList, // Envia o array de objetos com ID e status
                    "tipo": tipo,
                    "justificativa": result.value // Envia a justificativa junto com o ID e status
                };
                openLoading();
                $.ajax({
                    url: "<?= base_url('variaveis/sincronizacao/aprovarGestor'); ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') { 
                            exibeAlerta(response.tipo, response.msg, 2,'<?= base_url('variaveis/sincronizacao/index/'); ?>/'+tipo+'');
                        } else {
                            exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/sincronizacao/index/'); ?>/'+tipo+'');
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar Sincronizar a requisição.', 2);
                    }
                });
            }
        });
    };


    
    const sincAprovacao = (tipo) => {
        // Captura todos os checkboxes marcados
        let idStatusList = [];
        texto ='';
        if(tipo == 1){
            $('input[name="idbatida1[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 2){
            $('input[name="idbatida2[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 3){
            $('input[name="idbatida3[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 4){
            $('input[name="idbatida4[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 5){
            $('input[name="idbatida5[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });

               
            });
           
        } else if(tipo == 6){
            $('input[name="idbatida6[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 7){
            $('input[name="idbatida7[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 8){
            $('input[name="idbatida8[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 9){
            $('input[name="idbatida9[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        }


        if (idStatusList.length === 0) {
            exibeAlerta('warning', 'Nenhuma requisição selecionada.', 2);
            return;
        }

        // Perguntar ao usuário se deseja aprovar usando Swal, incluindo um campo de texto para a justificativa
        Swal.fire({
            icon: 'question',
            title: 'Deseja Sincronizar essas requisições?',
            html: texto,
            input: 'textarea', // Tipo de input como textarea para a justificativa
            inputLabel: '',
            inputPlaceholder: 'Insira sua justificativa aqui...',
            inputAttributes: {
                'aria-label': 'Insira sua justificativa aqui'
            },
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Sim, confirmar',
            denyButtonText: 'Cancelar',
            showCloseButton: false,
            allowOutsideClick: false,
            width: 600,
            preConfirm: (justificativa) => {
                return justificativa;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let dados = {
                    "idStatus": idStatusList, // Envia o array de objetos com ID e status
                    "tipo": tipo,
                    "justificativa": result.value // Envia a justificativa junto com o ID e status
                };
                openLoading();
                $.ajax({
                    url: "<?= base_url('variaveis/sincronizacao/sincronizaGestor'); ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') { 
                            exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('variaveis/sincronizacao/index/'); ?>/'+tipo+'');
                        } else {
                            exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/sincronizacao/index/'); ?>/'+tipo+'');
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar Sincronizar a requisição.', 2);
                    }
                });
            }
        });
    };


    const Filtro = () => {
	
        openLoading();
        $("#form_filtro").attr('action', base_url + '/variaveis/sincronizacao').attr('target', '_self');
        document.getElementById("form_filtro").submit();

    }

    

    const enviaReprova = (tipo) => {

        let idStatusList = [];
        if(tipo == 1){
            $('input[name="idbatida1[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 2){
            $('input[name="idbatida2[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 3){
            $('input[name="idbatida3[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 4){
            $('input[name="idbatida4[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 5){
            $('input[name="idbatida5[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 6){
            $('input[name="idbatida6[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 7){
            $('input[name="idbatida7[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 8){
            $('input[name="idbatida8[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        } else if(tipo == 9){
            $('input[name="idbatida9[]"]:checked').each(function() {
                let valor = $(this).val().split(':');
                idStatusList.push({
                    id: valor[0], 
                    status: valor[1]
                });
            });
        }


        if (idStatusList.length === 0) {
            exibeAlerta('warning', 'Nenhuma requisição selecionada.', 2);
            return;
        }


        
        // Perguntar ao usuário se deseja aprovar usando Swal, incluindo um campo de texto para a justificativa
        Swal.fire({
            icon: 'question',
            title: 'Deseja Reprovar essa requisição?',
            input: 'textarea', // Tipo de input como textarea para a justificativa
            inputLabel: 'Justificativa',
            inputPlaceholder: 'Insira sua justificativa aqui...',
            inputAttributes: {
                'aria-label': 'Insira sua justificativa aqui'
            },
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Sim, confirmar',
            denyButtonText: 'Cancelar',
            showCloseButton: false,
            allowOutsideClick: false,
            width: 600,
            preConfirm: (justificativa) => {
                if (!justificativa) {
                    Swal.showValidationMessage('A justificativa é obrigatória');
                    return false;
                }
                return justificativa;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let dados = {
                    "idStatus": idStatusList,
                    "tipo": tipo,
                    "justificativa": result.value // Envia a justificativa junto com o ID
                };
                openLoading();
                $.ajax({
                    url: "<?= base_url('variaveis/sincronizacao/reprovaRH'); ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg, 2);
                        } else {
                            exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/sincronizacao/index/'); ?>/'+tipo+'');
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar Reprovar a requisição.', 2);
                    }
                });
            }
        });
        };


    const validaTipo6 = () => {

        var dados = {
            'tipo': 6,
            'auxilio_moradia_porcentagem': $('#auxilio_moradia_porcentagem').val(),
            'auxilio_moradia_evento': $('#auxilio_moradia_evento').val(),
        }

        if(dados.auxilio_moradia_porcentagem == ''){exibeAlerta('error', 'Porcentagem do salário base do colaborador não informado.'); return false;}
        if(dados.auxilio_moradia_evento == ''){exibeAlerta('error', 'Evento do auxílio moradia não informado.'); return false;}

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
        
        $('#historico1').DataTable({
            "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            "iDisplayLength"    : 50,
            "aaSorting"         : [[0, "desc"]]
        });
        $('#datatable2').DataTable({
            "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            "iDisplayLength"    : 50,
            "aaSorting"         : [[0, "desc"]]
        });
        $('#datatablesubstituicao').DataTable({
            "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            "iDisplayLength"    : 50,
            "aaSorting"         : [[0, "desc"]]
        });
        $('#datatableDesconto').DataTable({
            "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            "iDisplayLength"    : 50,
            "aaSorting"         : [[0, "desc"]]
        });
        $('#datatableSobreaviso').DataTable({
            "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            "iDisplayLength"    : 50,
            "aaSorting"         : [[0, "desc"]]
        });
  



        $('#datatableAluguel').DataTable({
            "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            "iDisplayLength"    : 50,
            "aaSorting"         : [[0, "desc"]]
        });
        $('#datatableSalario').DataTable({
            "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            "iDisplayLength"    : 50,
            "aaSorting"         : [[0, "desc"]]
        });
       
        // Inicializa o DataTables na tabela principal
        var table = $('#datatable1').DataTable({
            "aLengthMenu": [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            "iDisplayLength": 50,
            "aaSorting": [[1, "desc"]],
        });
        var table = $('#datatableCreche').DataTable({
            "aLengthMenu": [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            "iDisplayLength": 50,
            "aaSorting": [[1, "desc"]],
        });
        var table = $('#datatableCopart').DataTable({
            "aLengthMenu": [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            "iDisplayLength": 50,
            "aaSorting": [[1, "desc"]],
        });

        // Manipulador de clique para o botão "+"
        $(document).on('click', '.btn-toggle', function() {
            // Obtém a linha atual
            var $currentRow = $(this).closest('tr');

            // Verifica se a linha de detalhe já existe
            if ($currentRow.next('.detail-row').length > 0) {
                // Se a linha de detalhe já existe, simplesmente a remove (fechar)
                $currentRow.next('.detail-row').remove();
            } else {
                // Obtém os dados de dependentes do atributo data
                var dependentesData = $(this).attr('data-dependentes');

                if (dependentesData) {
                    try {
                        // Converte a string JSON para um array de objetos
                        var dependentes = JSON.parse(dependentesData);

                        if (Array.isArray(dependentes)) {
                            // Cria o HTML da linha de detalhe
                            var detailRow = '<tr class="detail-row"><td colspan="12">';
                            detailRow += '<table class="table table-sm table-bordered">';
                            detailRow += '<thead><tr><th>Nome</th><th>Data de nascimento</th><th>Parentesco</th><th>Valor</th></tr></thead>';
                            detailRow += '<tbody>';

                            // Itera sobre os dados dos dependentes e cria as linhas da tabela de detalhes
                            dependentes.forEach(function(dependente) {
                                detailRow += '<tr>';
                                detailRow += '<td>' + dependente.nome + '</td>';
                                detailRow += '<td>' + dependente.data_nascimento + '</td>';
                                detailRow += '<td>' + dependente.parentesco + '</td>';
                                detailRow += '<td>' + dependente.valor + '</td>';
                                detailRow += '</tr>';
                            });

                            detailRow += '</tbody></table></td></tr>';

                            // Adiciona a linha de detalhe após a linha atual
                            $currentRow.after(detailRow);
                        } else {
                            console.error("Os dados de dependentes não são um array.");
                        }
                    } catch (e) {
                        console.error("Erro ao fazer parse dos dados de dependentes:", e);
                    }
                }
            }
        });
 
    
        // Manipulador de clique para o botão "+"
        $(document).on('click', '.btn-toggle2', function() {
            // Obtém a linha atual
            var $currentRow = $(this).closest('tr');

            // Verifica se a linha de detalhe já existe
            if ($currentRow.next('.detail-row').length > 0) {
                // Se a linha de detalhe já existe, simplesmente a remove (fechar)
                $currentRow.next('.detail-row').remove();
            } else {
                // Obtém os dados de dependentes do atributo data
                var dependentesData = $(this).attr('data-dependentes');

                if (dependentesData) {
                    try {
                        // Converte a string JSON para um array de objetos
                        var dependentes = JSON.parse(dependentesData);

                        if (Array.isArray(dependentes)) {
                            // Cria o HTML da linha de detalhe
                            var detailRow = '<tr class="detail-row"><td colspan="12">';
                            detailRow += '<table class="table table-sm table-bordered">';
                            detailRow += '<thead><tr><th>Nome</th><th>Data de nascimento</th><th>Parentesco</th><th>Valor</th></tr></thead>';
                            detailRow += '<tbody>';

                            // Itera sobre os dados dos dependentes e cria as linhas da tabela de detalhes
                            dependentes.forEach(function(dependente) {
                                detailRow += '<tr>';
                                detailRow += '<td>' + dependente.nome + '</td>';
                                detailRow += '<td>' + dependente.data_nascimento + '</td>';
                                detailRow += '<td>' + dependente.parentesco + '</td>';
                                detailRow += '<td>' + dependente.valor + '</td>';
                                detailRow += '</tr>';
                            });

                            detailRow += '</tbody></table></td></tr>';

                            // Adiciona a linha de detalhe após a linha atual
                            $currentRow.after(detailRow);
                        } else {
                            console.error("Os dados de dependentes não são um array.");
                        }
                    } catch (e) {
                        console.error("Erro ao fazer parse dos dados de dependentes:", e);
                    }
                }
            }
        });
        // Manipulador de clique para o botão "+"
    $(document).on('click', '.btn-toggle3', function() {
        // Obtém a linha atual
        var $currentRow = $(this).closest('tr');

        // Verifica se a linha de detalhe já existe
        if ($currentRow.next('.detail-row').length > 0) {
            // Se a linha de detalhe já existe, simplesmente a remove (fechar)
            $currentRow.next('.detail-row').remove();
        } else {
            // Obtém os dados de dependentes do atributo data
            var dependentesData = $(this).attr('data-dependentes');

            if (dependentesData) {
                try {
                    // Converte a string JSON para um array de objetos
                    var dependentes = JSON.parse(dependentesData);

                    if (Array.isArray(dependentes)) {
                        // Cria o HTML da linha de detalhe
                        var detailRow = '<tr class="detail-row"><td colspan="7">';
                        detailRow += '<table class="table table-sm table-bordered">';
                        detailRow += '<thead><tr><th>Chapa</th><th>Nome</th><th>Valor</th><th>Situação</th><th>Função</th></tr></thead>';
                        detailRow += '<tbody>';

                        // Itera sobre os dados dos dependentes e cria as linhas da tabela de detalhes
                        dependentes.forEach(function(dependente) {
                            detailRow += '<tr>';
                            detailRow += '<td>' + dependente.chapa + '</td>';
                            detailRow += '<td>' + dependente.nome + '</td>';
                            detailRow += '<td>' + dependente.valor + '</td>';
                            detailRow += '<td>' + dependente.situacao + '</td>';
                            detailRow += '<td>' + dependente.funcao + '</td>';
                            detailRow += '</tr>';
                        });

                        detailRow += '</tbody></table></td></tr>';

                        // Adiciona a linha de detalhe após a linha atual
                        $currentRow.after(detailRow);
                    } else {
                        console.error("Os dados de dependentes não são um array.");
                    }
                } catch (e) {
                    console.error("Erro ao fazer parse dos dados de dependentes:", e);
                }
            }
        }
    });
   
  

     

  
    });
</script>
<?php loadPlugin(['select2','maskmoney','datatable']); ?>

