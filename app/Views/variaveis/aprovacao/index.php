<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
					
				<div class="card-header mt-0">
					<div class="row">
						<h4 class="col-12 mb-1 mt-1"><?= $_titulo; ?></h4>
					</div>
				</div>
				
				<div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <ul class="nav nav-tabs mb-2" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#auxilio_moradia" role="tab"><i class="fa fa-home"></i> Auxílio Moradia</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#reembolso_cpd" role="tab"><i class="fa fa-wheelchair"></i> Auxilio Excepicional</a>
                        </li> -->
                       
                    </ul>

                    <div class="tab-content">
                        <!-- auxilio moradia -->
                        <div class="tab-pane active p-3 " id="auxilio_moradia" role="tabpanel">
                            <div class="row d-flex justify-content-center mb-2">
                                <div class="mb-2">
                                    <button id="enviaAprovacaoBtn" onclick="return enviaAprovacao(6)" type="button" class="btnpeq btn-sm btn-success" style="margin: -13px 10px 2px 0;">
                                        <i class="fa fa-check"></i> Aprovar Selecionados
                                    </button>
                                </div>
                                <div class="mb-2">
                                    <button id="reprovaBTN" onclick="return enviaReprova(6)" type="button" class="btnpeq btn-sm btn-danger" style="margin: -13px 0 2px 0;">
                                        <i class="fa fa-times"></i> Reprovar Selecionados
                                    </button>
                                </div>
                            </div>


                                <div class="">
                                    <table width="100%" cellspacing="0" cellpadding="0" id="datatable2"  class=" datatable table table-sm table-striped table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th class="n-mobile-cell" data-orderable="false"><input type="checkbox" onchange="toggleButtonVisibility()" id="select-all6"></th>
                                                <td class="n-mobile-cell" width="40" >Nº da requisição</td>
                                                <td class="n-mobile-cell" >Situação</td>
                                                <td class="n-mobile-cell">Filial</td>
                                                <td class="n-mobile-cell" width="40">Chapa</td>
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
                                                            $descricao = "Aprov. Gestor";
                                                            $corFundo = "#e0f8e0"; // Cor de fundo para "Aprov. Gestor"
                                                            $corTexto = "#006e34"; // Cor do texto
                                                            break;
                                                        default:
                                                            $descricao = "Todas";
                                                            $corFundo = "#ffffff"; // Cor de fundo padrão
                                                            $corTexto = "#000000"; // Cor do texto padrão
                                                            break;
                                                    }
                                                    
                                                    
                                                    
                                                    
                                                    ?>
                                                    <tr class="text-center">
                                                        <td class="n-mobile-cell" width="20" ><input type="checkbox" name="idbatida6[]" onchange="toggleButtonVisibility6()"   value="<?= $dados->id ?>"> </td>
                                                        <td class="n-mobile-cell"  <?= $fora_prazo ?>><?= $dados->id ?></td>
                                                        <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                                        <td class="n-mobile-cell"><?= $valores->filial?></td>
                                                        <td class="n-mobile-cell"><?= $valores->funcionario ?></td>
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
                                                                    <button onclick="aprovarReq('<?= $dados->id; ?>', 6)" class="dropdown-item" style="color: blue;"> Aprovar</button>
                                                                   
                                                                   <!-- <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button> -->
                                                                   <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button>
                                                                   <button onclick="verAnexos('<?= $dados->id; ?>','<?= $dados->status; ?>' )" class="dropdown-item  " > Documentos</button>
                                                 
                                                                   <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button>
                                                                    <a  href="<?= base_url('variaveis/moradia/editar/'.base64_encode($dados->id)); ?>" class=" dropdown-item disabled ">Editar requisição</a>
                                                                
                                                                    <button onclick="reprovarReq('<?= $dados->id; ?>', 6)" class="dropdown-item" style="color: red;">Reprovar</button>
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

                            <div class="tab-pane  p-3" id="reembolso_cpd" role="tabpanel">
                            <div class="">
                            <div class="row d-flex justify-content-center mb-2">
                                <div class="mb-2">
                                    <button id="enviaAprovacaoBtn" onclick="return enviaAprovacao(4)" type="button" class="btnpeq btn-sm btn-success" style="margin: -13px 10px 2px 0;">
                                        <i class="fa fa-check"></i> Aprovar Selecionados
                                    </button>
                                </div>
                                <div class="mb-2">
                                    <button id="reprovaBTN" onclick="return enviaReprova(4)" type="button" class="btnpeq btn-sm btn-danger" style="margin: -13px 0 2px 0;">
                                        <i class="fa fa-times"></i> Reprovar Selecionados
                                    </button>
                                </div>
                            </div>
                                <table width="100%" cellspacing="0" cellpadding="0"  id="datatable1" class="datatable  table table-sm table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th data-orderable="false"><input type="checkbox" onchange="toggleButtonVisibility()" id="select-all4"></th>
                                            <td class="n-mobile-cell" width="40" >Nº da requisição</td>
                                            <td class="n-mobile-cell" >Situação</td>
                                            <td class="n-mobile-cell">Filial</td>
                                            <td class="n-mobile-cell" width="40">Chapa</td>
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
                                                            $descricao = "Aprov. Gestor";
                                                            $corFundo = "#e0f8e0"; // Cor de fundo para "Aprov. Gestor"
                                                            $corTexto = "#006e34"; // Cor do texto
                                                            break;
                                                        default:
                                                            $descricao = "Todas";
                                                            $corFundo = "#ffffff"; // Cor de fundo padrão
                                                            $corTexto = "#000000"; // Cor do texto padrão
                                                            break;
                                                    }
                                                
                                                
                                                
                                                ?>
                                                <tr class="text-center">
                                                    <td class="n-mobile-cell" width="80" ><input type="checkbox" onchange="toggleButtonVisibility4()"  name="idbatida4[]"  value="<?= $dados->id ?>">  <button class="btn btn-soft-primary btn-toggle" type="button">+</button></td>
                                                    <td class="n-mobile-cell"><?= $dados->id ?></td>
                                                    <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                                    <td class="n-mobile-cell"><?= $valores->filial?></td>
                                                    <td class="n-mobile-cell"><?= $valores->funcionario ?></td>
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
                                                                <button onclick="aprovarReq('<?= $dados->id; ?>', 4)" class="dropdown-item" style="color: blue;"> Aprovar</button>
                                                                
                                                                <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button>
                                                                <!-- <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button> -->
                                                                <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button>
                                                                <button onclick="verAnexos('<?= $dados->id; ?>','<?= $dados->status; ?>' )" class="dropdown-item  " > Documentos</button>
                                                 
                                                                <a  href="<?= base_url('variaveis/pcd/editar/'.base64_encode($dados->id)); ?>" class=" dropdown-item disabled ">Editar requisição</a>
                                                            
                                                                <button onclick="reprovarReq('<?= $dados->id; ?>', 4)" class="dropdown-item" style="color: red;">Reprovar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        
                                                    </td>
                                                </tr>
                                                
                                                <tr class="detail-row" style="display: none;">
                                                    <td colspan="11">
                                                        <table width="100%" class="table table-sm table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Nome</th>
                                                                    <th>Data de nascimento</th>
                                                                    <th>Parentesco</th>
                                                                    <th>Valor</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php foreach ($valores->dependentes as $key2 => $dados2) : ?>
                                                                <tr>
                                                                    <td class="n-mobile-cell" width="100"><?= $dados2->nome ?></td>
                                                                    <td class="n-mobile-cell" width="80"><?= $dados2->data_nascimento ?></td>
                                                                    <td class="n-mobile-cell" width="20"><?= $dados2->parentesco ?></td>
                                                                    <td class="n-mobile-cell" width="80"><?= $dados2->valor ?></td>
                                                                </tr>
                                                                <!-- Mais linhas de detalhes -->
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
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

<div class="modal modal_visualiza_req" tabindex="-1" role="dialog" aria-labelledby="modal_visualiza_req" aria-hidden="true" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" style="max-width: 90%; width: 90%;">
        <div class="modal-content modal-content-full">
            <div class="modal-header bg-dark">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-file-document-box-outline"></i> Auxilio Moradia</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        
                      
                    <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#auxilio_moradia" role="tab"><i class="fa fa-home"></i> Auxílio moradia</a>
                            </li>
                          
                    </ul>
                    <div class="tab-content">
                            <!-- auxilio moradia -->
                            <div class="tab-pane active p-3" id="auxilio_moradia" role="tabpanel">
                            
                                <div class="form-group row mb-2">
                                    <label for="Reqfuncionario" class="col-sm-2 col-form-label text-right text-left-sm">Funcionário:</label>
                                    <div class="col-sm-10">
                                        <select disabled class="select2 custom-select form-control form-control-sm" name="Reqfuncionario" id="Reqfuncionario">
                                           
                                                  
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm">Justificativa:</label>
                                    <div class="col-sm-10">
                                        <textarea disabled class="form-control" name="justificativa" id="justificativa" cols="30" rows="3"></textarea>
                                    </div>
                                </div>

                                <input class="form-control datepicker m_data" hidden type="text" value="" name="filial" id="filial" required>
                                <input class="form-control datepicker m_data" hidden type="text" value="" name="funcao" id="funcao" required>
                                

                                
                                

                            </div>
                        </div>
                        

                    </div>
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
                    <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm">Anexar:</label>
                    <div class="col-sm-10">
                        <input class="form-control filepond" type="file" name="anexo[]" id="anexo" multiple required>
                        <input class="form-control" hidden type="text" value="" name="id" id="id" required>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-success bteldorado_1" id="btnsave" onclick="return salvaAnexo()"><i class="fas fa-check"></i> Salvar</button>
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
</style>
<script>
     const salvaAnexo = () => {
        // Obtenha os arquivos selecionados
        let arquivos = $('#anexo')[0].files;

        if (arquivos.length === 0) {
            Swal.fire('Erro', 'Nenhum arquivo selecionado.', 'error');
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
    
    const verAnexos = (id, status) => {
        const novoTitulo = `Anexos - ${id}`; // Exemplo de como construir o novo título
        $(".modal_Anexos .modal-title").text(novoTitulo);
    $('.modal_Anexos tbody').html('');
    $("#id").val(id);
    $('#anexo').val(''); // Limpa o campo de anexo
    // if (status !== 1) {
    //     $('#anexo').prop('disabled', true);
    // } else {
    //     $('#anexo').prop('disabled', false);
    // }
    $.ajax({
        url: "<?= base_url('variaveis/moradia/Anexos'); ?>",
        type: "POST",
        data: {
            "id": id
        },
        success: function (response) {
            swal.close();
            if (Array.isArray(response) && response.length > 0) {

                response.forEach(anexo => {
                    let disableDelete =  '';
                    let row = `
                        <tr id="anexo_${anexo.id}">
                            <td style="width: 80%;"><a href="data:${anexo.file_type};base64,${anexo.file_data}" download="${anexo.file_name}">
                                ${anexo.file_name}
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

    const abrirResumoDiario = (justificativa) => {

        $(".modal_resumo_diario").modal('show');

        $('.modal_resumo_diario tbody').html('');

        $('.modal_resumo_diario tbody').append('<tr><td class="">' + justificativa + '</td></tr>');
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
    $('#select-all4').on('click', function() {
        $('input[name="idbatida4[]"]').prop('checked', this.checked);
    });
    $('#select-all6').on('click', function() {
        $('input[name="idbatida6[]"]').prop('checked', this.checked);
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

    const abrirReq = (dados) => {
        dados = atob(dados);
        const req = JSON.parse(dados);
        const valor = JSON.parse(req.valores);
        const novoTitulo = `Auxilio Moradia - ${req.id}`; // Exemplo de como construir o novo título
        $(".modal_visualiza_req .modal-title").text(novoTitulo);

        $(".modal_visualiza_req").modal('show');

        $("#Reqfuncionario").append($('<option></option>')
        .attr('value', valor.funcionario)
        .attr('selected', true) // Define a opção como selecionada
        .text(valor.funcionario +'- ' +valor.Nome));

        $("#justificativa").val(valor.justificativa);
      
    }
    

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

    
    const aprovarReq = (id, tipo) => {
        // Perguntar ao usuário se deseja aprovar usando Swal, incluindo um campo de texto para a justificativa
        Swal.fire({
            icon: 'question',
            title: 'Deseja Aprovar essa requisição?',
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
                    "id": id,
                    "tipo": tipo,
                    "justificativa": result.value // Envia a justificativa junto com o ID
                };
                openLoading();
                $.ajax({
                    url: "<?= base_url('variaveis/aprovacao/aprovarGestor'); ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg, 2);
                        } else {
                            exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/aprovacao'); ?>');
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar aprovar a requisição.', 2);
                    }
                });
            }
        });
    };

    const reprovarReq = (id,tipo) => {
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
                    "id": id,
                    "tipo": tipo,
                    "justificativa": result.value // Envia a justificativa junto com o ID
                };
                openLoading();
                $.ajax({
                    url: "<?= base_url('variaveis/aprovacao/reprovaGestor'); ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg, 2);
                        } else {
                            exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/aprovacao'); ?>');
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar aprovar a requisição.', 2);
                    }
                });
            }
        });
    };


    const enviaAprovacao = (tipo) => {

        // Captura todos os checkboxes marcados
        let ids = [];
        if(tipo == 4){
            $('input[name="idbatida4[]"]:checked').each(function() {
                ids.push($(this).val());
            });
        }else if(tipo == 6){
            $('input[name="idbatida6[]"]:checked').each(function() {
                ids.push($(this).val());
            });

        }
   

        if (ids.length === 0) {
            exibeAlerta('warning', 'Nenhuma requisição selecionada.', 2);
            return;
        }
        // Perguntar ao usuário se deseja aprovar usando Swal, incluindo um campo de texto para a justificativa
        Swal.fire({
            icon: 'question',
            title: 'Deseja Aprovar essas requisições?',
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
                    "id": ids,
                    "tipo": tipo,
                    "justificativa": result.value // Envia a justificativa junto com o ID
                };
                openLoading();
                $.ajax({
                    url: "<?= base_url('variaveis/aprovacao/aprovarGestor'); ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg, 2);
                        } else {
                            exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/aprovacao'); ?>');
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar aprovar a requisição.', 2);
                    }
                });
            }
        });
    };

    const enviaReprova= (tipo) => {

        // Captura todos os checkboxes marcados
        let ids = [];
        if(tipo == 4){
            $('input[name="idbatida4[]"]:checked').each(function() {
                ids.push($(this).val());
            });
        }else if(tipo == 6){
            $('input[name="idbatida6[]"]:checked').each(function() {
                ids.push($(this).val());
            });

        }


        if (ids.length === 0) {
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
                    "id": ids,
                    "tipo": tipo,
                    "justificativa": result.value // Envia a justificativa junto com o ID
                };
                openLoading();
                $.ajax({
                    url: "<?= base_url('variaveis/aprovacao/reprovaGestor'); ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg, 2);
                        } else {
                            exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/aprovacao'); ?>');
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar reprovar a requisição.', 2);
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
        
    });
</script>
<?php loadPlugin(['select2','maskmoney','datatable']); ?>

