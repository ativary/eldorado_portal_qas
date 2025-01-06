<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
					
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-6 mb-1 mt-1"><?= $_titulo; ?></h4>
                    <div class="col-6 text-right">
                        <div class="button-items">
                            <a href="<?= base_url('variaveis/pcd/novo') ?>" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova Requisição</a>
                        </div>
                    </div>
                    </div>
                </div>
				
				<div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <div class="tab-content">
                       <form action="" method="post" name="form_filtro" id="form_filtro">

						<!-- <div class="form-group row mb-2">
							<label for="secao"   class="col-sm-2 col-form-label text-right text-left-sm">Seção:</label>
							<div class="col-sm-10 ">
								<select data-secao class="select2 mb-3  form-control-sm" name="secao" id="secao" data-secao style="width: 100%"  onchange="selecionaFuncionario(this.value)">
									<option value="">- Todos -</option>
									<?php if($listaSecao): ?>
                                        <?php foreach($listaSecao as $Secao): ?>
                                            <option value="<?= $Secao->CODIGO; ?>" <?= ($Secao->CODIGO == $secao  ) ? 'selected' : ''; ?>><?= $Secao->CODIGO.' - '.$Secao->DESCRICAO; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
								</select>
							</div>
						</div> -->
                      
						<div class="form-group row mb-2">
							<label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm">Funcionário:</label>
							<div class="col-sm-10">
								<select class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
									<option value="">- Todos -</option>
									<?php if ($resFuncionarioSecao) : ?>
										<?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
											<option value="<?= $DadosFunc['CHAPA'] ?>" <?= ($funcionario == $DadosFunc['CHAPA']) ? " selected " : ""; ?>><?= $DadosFunc['CHAPA'] . ' - ' . $DadosFunc['NOME'] ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
						</div>
                        <div class="form-group row mb-2">
							<label for="situacao" class="col-sm-2 col-form-label text-right text-left-sm">Situação:</label>
							<div class="col-sm-10">
								<select class="select2 custom-select form-control form-control-sm" name="situacao" id="situacao">
                                    <option value="" > Todas </option>
                                    <option value="6" <?= ($situacao == '6'  ) ? 'selected' : ''; ?>>  Reprovação RH </option>
                                    <option value="5"<?= ($situacao == '5'  ) ? 'selected' : ''; ?>>  Reprovação Gestor </option>
                                    <option value="1"<?= ($situacao == '1'  ) ? 'selected' : ''; ?>>  Criada </option>
                                    <option value="2"<?= ($situacao == '2'  ) ? 'selected' : ''; ?>>  Pendente Ação gestor </option>
                                    <option value="3"<?= ($situacao == '3'  ) ? 'selected' : ''; ?>>  Pendente Ação RH </option>
                                    <option value="4"<?= ($situacao == '4'  ) ? 'selected' : ''; ?>>  Sincronizado </option>
                                    <option value="7"<?= ($situacao == '7'  ) ? 'selected' : ''; ?>>  Pendente Sincronização </option>
									
									
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
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <button id="enviaAprovacaoBtn" onclick="return enviaAprovacao()" type="button" class="btnpeq btn-sm btn-success" style="float: center; margin: -13px 0 2px 0; display: none;">
                    <i class="fa fa-check"></i> Enviar todos para aprovação
                </button>
                <div class="">
                    <table width="100%" cellspacing="0" cellpadding="0" id="datatable" class="table table-sm table-striped table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th data-orderable="false"><input type="checkbox" onchange="toggleButtonVisibility()" id="select-all"></th>
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
                            <?php if ($listaReqs) : ?>
                                <?php foreach ($listaReqs as $key => $dados) : ?>
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

                                        $fora_prazo ='';
                                        if(isset($valores->fora_periodo)){
                                            if($valores->fora_periodo == '2'){
                                                $fora_prazo = 'style="color: red;" title="Criada fora do prazo como excessão"  ';
                                            }
                                        }
                                    
                                    ?>
                                    <tr class="text-center">
                                        <td class="n-mobile-cell" width="80">
                                                <input <?= ($dados->status != '1') ? 'disabled' : ''; ?> type="checkbox" onchange="toggleButtonVisibility()" name="idbatida[]" value="<?= $dados->id ?>">  
                                                <button class="btn btn-soft-primary btn-toggle" 
                                                    type="button"
                                                    data-dependentes='<?= $valores->dependentes; ?>'>
                                                    +
                                                </button>
                                        </td>
                                        <td class="n-mobile-cell" <?= $fora_prazo ?>><?= $dados->id ?></td>
                                        <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                        <td class="n-mobile-cell"><?= $valores->filial?></td>
                                        
                                        <td class="n-mobile-cell"><?= $dados->tiporeq == '1' ? 'Mensal' : 'Complementar' ?></td>
                                                 
                                        <td class="n-mobile-cell"><?= $valores->Nome ?></td>
                                        <td class="n-mobile-cell"><?= $valores->funcao ?></td>
                                        
                                        <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($dados->dtcad)) ?></td>
                                        <td class="n-mobile-cell"><?= $dados->nome ?></td>
                                        <td class="n-mobile-cell">
                                        <div class="btn-group dropleft mb-2 mb-md-0">
                                        <button onclick="historicoAprovacao('<?= $dados->id; ?>')" class="btn btn-soft-primary pl-1 pr-1"  aria-expanded="true"> <i class="mdi mdi-history"></i></button>
                                
                                            <div class="dropdown">
                                                <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                <div class="dropdown-menu">
                                                    <button onclick="aprovarReq('<?= $dados->id; ?>')" <?= ($dados->status != '1') ? 'disabled' : 'style="color: blue;"'; ?>  class="dropdown-item" > Enviar para aprovação</button>
                                                    <button onclick="verAnexos('<?= $dados->id; ?>','<?= $dados->status; ?>' )" class="dropdown-item  " > Documentos</button>
                                                     
                                                    <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button>
                                                    <!-- <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button> -->
                                                   
                                                    <a  href="<?= base_url('variaveis/pcd/editar/'.base64_encode($dados->id)); ?>" class="dropdown-item <?= ($dados->status != '1') ? 'disabled' : ''; ?>">Editar requisição</a>
                                                  
                                                    <button  onclick="excluirReq('<?= $dados->id; ?>')" class="dropdown-item" <?= ($dados->status != '1') ? 'disabled' : 'style="color: red;"'; ?>>Excluir</button>
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
                </br>
                
                      </div>
          
        </div>
        
    </div>
</div>



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
                                       
                                        <th width="60" class="text-rigth">justificativa</th>
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
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-file-document-box-outline"></i> Auxilio Excepcional</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        
                      
                    <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#auxilio_pcd" role="tab"><i class="fa fa-home"></i> Auxilio Excepcional</a>
                            </li>
                          
                    </ul>
                    <div class="tab-content">
                            <!-- auxilio pcd -->
                            <div class="tab-pane active p-3" id="auxilio_pcd" role="tabpanel">
                            
                                <div class="form-group row mb-2">
                                    <label for="Reqfuncionario" class="col-sm-2 col-form-label text-right text-left-sm">Funcionário:</label>
                                    <div class="col-sm-10">
                                        <select disabled class="select2 custom-select form-control form-control-sm" name="Reqfuncionario" id="Reqfuncionario">
                                           
                                                  
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm">Tipo de Requisição:</label>
                                    <div class="col-sm-8">
                                        <select disabled  class="select2 custom-select form-control form-control-sm" name="tipoReq" id="tipoReq">
                                           
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
                        <input class="form-control filepond" type="file" name="anexo[]" id="anexo" multiple required  accept="application/pdf, image/jpeg">
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
.anexo-claro {
    background-color: #f0f0f0; /* Cor mais clara */
}

</style>
<script>
    $(document).ready(function() {
    // Inicializa o DataTables na tabela principal
    var table = $('#datatable').DataTable({
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
});

    $('#select-all').on('click', function() {
        $('input[name="idbatida[]"]:not(:disabled)').prop('checked', this.checked);
    });


    function toggleButtonVisibility() {
        const checkboxes = document.querySelectorAll('input[name="idbatida[]"]');
        const button = document.getElementById('enviaAprovacaoBtn');
        let isChecked = false;

        checkboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                isChecked = true;
            }
        });

        button.style.display = isChecked ? 'block' : 'none';
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
    const abrirResumoDiario = (justificativa) => {

        $(".modal_resumo_diario").modal('show');

        $('.modal_resumo_diario tbody').html('');

        $('.modal_resumo_diario tbody').append('<tr><td class="">' + justificativa + '</td></tr>');
    }
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
const salvaAnexo = () => {
    // Obtenha os arquivos selecionados
    let arquivos = $('#anexo')[0].files;

    if (arquivos.length === 0) {
        Swal.fire('Erro', 'Nenhum arquivo selecionado.', 'error');
        return false;
    }

    // Definir extensões permitidas
    const extensoesPermitidas = ['jpeg',  'pdf'];
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
        Swal.fire('Erro', 'Apenas arquivos com as extensões .jpeg, ou .pdf são permitidos.', 'error');
        return false;
    }

    // Cria um objeto FormData para enviar os arquivos
    let formData = new FormData();
    $.each(arquivos, function(i, file) {
        formData.append('anexo[]', file);
    });

    // Adicione outros dados que você queira enviar, se necessário
    formData.append("id", $("#id").val());

    // Enviar requisição AJAX com os anexos validados
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

    const verAnexos = (id,status) => {
        const novoTitulo = `Anexos - ${id}`; // Exemplo de como construir o novo título
        $(".modal_Anexos .modal-title").text(novoTitulo);
        $('.modal_Anexos tbody').html('');
        $("#id").val(id);
        $('#anexo').val(''); // Limpa o campo de anexo
        if (status != 1) {
            $('#anexo').prop('disabled', true);
        } else {
            $('#anexo').prop('disabled', false);
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
                        let disableDelete = status != 1 ? 'disabled' : '';
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
    

    const selecionaFuncionario = (codigo) => {
        let dados = {
            "codigo":codigo,
            
        }
        openLoading();

        $.ajax({
            url: "<?= base_url('variaveis/pcd/selectFunc'); ?>",
            type:'POST',
            data:dados,
            success:function(result){
               
                var response = JSON.parse(result);

               
                 // Limpa todas as opções do select com id "funcionario"
                $('#funcionario').empty();
                if (response.tipo === 'error') {
                    $('#funcionario').append(
                        $('<option>', {
                            value: '',
                            text: 'Nenhum funcionário encontrado'
                        })
                    );
                } else {
                    $('#funcionario').append(
                            $('<option>', {
                                value: '',
                                text: '-Todos-'
                            })
                        );
                    // Cria uma option para cada item no response se não for um erro
                    $.each(response, function(index, func) {
                        $('#funcionario').append(
                            $('<option>', {
                                value: func.CHAPA,
                                text: func.CHAPA + " - " + func.NOME
                            })
                        );
                    });
                }

                openLoading(true);
                

            },
        });
        
    }    

    const abrirReq = (dados) => {
        dados = atob(dados);
        const req = JSON.parse(dados);
        var tipo = '';
        const valor = JSON.parse(req.valores);

        const novoTitulo = `Auxilio Excepcional - ${req.id}`; // Exemplo de como construir o novo título
        $(".modal_visualiza_req .modal-title").text(novoTitulo);

        $(".modal_visualiza_req").modal('show');
       
        $("#Reqfuncionario").append($('<option></option>')
        .attr('value', valor.funcionario)
        .attr('selected', true) // Define a opção como selecionada
        .text(valor.Nome));
        if(valor.tipoReq == '1'){
            tipo = 'Mensal';
        }else{
            tipo = 'Complementar';
        }
        $("#tipoReq").append($('<option></option>')
        .attr('value', valor.tipoReq)
        .attr('selected', true) // Define a opção como selecionada
        .text(tipo));

        $("#justificativa").val(valor.justificativa);
      
    }
    const Filtro = () => {
	
		openLoading();
		$("#form_filtro").attr('action', base_url + '/variaveis/pcd').attr('target', '_self');
		document.getElementById("form_filtro").submit();

	}
    const enviaAprovacao = () => {
        // Captura todos os checkboxes marcados
        let ids = [];
        $('input[name="idbatida[]"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            exibeAlerta('warning', 'Nenhuma requisição selecionada.', 2);
            return;
        }

        let anexosValidos = true;
        let verificados = 0;

        ids.forEach((id) => {
            validaAnexos(id, (isValid) => {
                verificados++;
                if (!isValid) {
                    anexosValidos = false;
                    exibeAlerta('error', `Sem anexos na requisição ${id}.`, 2);
                }

                // Verifica se todas as validações foram feitas
                if (verificados === ids.length) {
                    if (!anexosValidos) {
                        return; // Não prossegue se houver anexos inválidos
                    }

                    // Todos os anexos são válidos, prosseguir com a aprovação
                    Swal.fire({
                        icon: 'question',
                        title: 'Deseja enviar essas requisições para aprovação?',
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
                                "ids": ids, // Enviar os IDs para aprovação
                                "situacao": '3' 
                            };
                            openLoading();
                            $.ajax({
                                url: "<?= base_url('variaveis/aprovacao/aprovar_em_lote'); ?>",
                                type: 'POST',
                                data: dados,
                                success: function(result) {
                                    console.log(result);
                                    var response = JSON.parse(result);

                                    if (response.tipo != 'success') {
                                        exibeAlerta(response.tipo, response.msg, 2);
                                    } else {
                                        exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/pcd'); ?>');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    exibeAlerta('error', 'Ocorreu um erro ao tentar enviar as requisições.', 2);
                                }
                            });
                        }
                    });
                }
            });
        });
    };

    const excluirReq = (id) => {
        // Perguntar ao usuário se deseja excluir usando Swal
        Swal.fire({
            icon: 'question',
            title: 'Deseja excluir essa Requisição?',
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
                openLoading();
                $.ajax({
                    url: "<?= base_url('variaveis/pcd/delete'); ?>",
                    type: 'POST',
                    data: dados, // Enviar o ID para exclusão
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg, 2);
                        } else {
                            exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/pcd'); ?>');
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar excluir a requisição.', 2);
                    }
                });
            }
        });
    };
   


    
    const aprovarReq = (id) => {
        // Validar os anexos
        validaAnexos(id, function(anexos) {
            if (!anexos) {
                Swal.fire('Erro', 'Requisição não possui anexos.', 'error');
                return false;
            }

            
            Swal.fire({
                icon: 'question',
                title: 'Deseja enviar essa Requisição para aprovação?',
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
                        "situacao": '3' 
                    };
                    openLoading();
                    $.ajax({
                        url: "<?= base_url('variaveis/aprovacao/aprovar'); ?>",
                        type: 'POST',
                        data: dados, 
                        success: function(result) {
                            console.log(result);
                            var response = JSON.parse(result);

                            if (response.tipo != 'success') {
                                exibeAlerta(response.tipo, response.msg, 2);
                            } else {
                                exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/pcd'); ?>');
                            }
                        },
                        error: function(xhr, status, error) {
                            exibeAlerta('error', 'Ocorreu um erro ao tentar aprovar a requisição.', 2);
                        }
                    });
                }
            });
        });
    };


</script>

<?php loadPlugin(['select2','maskmoney','datatable']); ?>
