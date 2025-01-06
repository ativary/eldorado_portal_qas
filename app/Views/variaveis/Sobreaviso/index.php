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
                            <a href="<?= base_url('variaveis/sobreaviso/novo') ?>" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova Requisição</a>
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
							<div class="input-group col-sm-10 ">
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
                                <th class="n-mobile-cell" data-orderable="false"><input type="checkbox" onchange="toggleButtonVisibility()" id="select-all"></th>
                                <td class="n-mobile-cell" width="40" >Nº da requisição</td>
                                <td class="n-mobile-cell" >Situação</td>
                                <td class="n-mobile-cell">Filial</td>
                              
                                <td class="n-mobile-cell">Tipo</td>
                                <td class="n-mobile-cell">Horas</td>
                                <td class="n-mobile-cell">Nome</td>
                                <td class="n-mobile-cell">Função</td>
                                <td class="n-mobile-cell">Gestor</td>
                                <td class="n-mobile-cell">Data da solicitação</td>
                                <td class="n-mobile-cell">Usuário Solicitande</td>
                              
                                <td class="n-mobile-cell" width="70" data-orderable="false">Ação</td>
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
                                        
                                    }
                                    $fora_prazo ='';
                                    if(isset($valores->fora_periodo)){
                                        if($valores->fora_periodo == '2'){
                                            $fora_prazo = 'style="color: red;" title="Criada fora do prazo como excessão"  ';
                                        }
                                    }
                                    
                                    
                                    
                                    ?>
                                    <tr class="text-center">
                                        <td class="n-mobile-cell" width="20" ><input <?= ($dados->status != '1') ? 'disabled' : ''; ?> type="checkbox" name="idbatida[]" onchange="toggleButtonVisibility()"   value="<?= $dados->id ?>"> </td>
                                        <td class="n-mobile-cell" <?= $fora_prazo ?>><?= $dados->id ?></td>
                                        <?= '  <td class="n-mobile-cell"  width="150"><span style="background-color: ' . $corFundo . '; color: ' . $corTexto . '; padding: 5px 10px; border-radius: 5px; font-weight: bold;">' . $descricao . '</span></td>'; ?>
                                        <td class="n-mobile-cell" ><?= $valores->filial?></td>
                                     
                                        <td class="n-mobile-cell"><?= $dados->tiporeq == '1' ? 'Mensal' : 'Complementar' ?></td>
                                        <td class="n-mobile-cell"><?= $valores->valor ?></td>
                                                
                                        <td class="n-mobile-cell"><?= $valores->Nome ?></td>
                                        <td class="n-mobile-cell"><?= $valores->funcao ?></td>
                                        <td class="n-mobile-cell"><?= $dados->nome_aprovador ?></td>
                                        <td class="n-mobile-cell"><?= date('d/m/Y', strtotime($dados->dtcad)) ?></td>
                                        <td class="n-mobile-cell"><?= $dados->nome ?></td>
                                        <td class="n-mobile-cell">
                                        <button onclick="historicoAprovacao('<?= $dados->id; ?>')" class="btn btn-soft-primary pl-1 pr-1"  aria-expanded="true"> <i class="mdi mdi-history"></i></button>
                                        <div class="btn-group dropleft mb-2 mb-md-0">
                                            <div class="dropdown">
                                                
                                                <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                <div class="dropdown-menu">
                                                    <button onclick="aprovarReq('<?= $dados->id; ?>')" class="dropdown-item" <?= ($dados->status != '1') ? 'disabled' : 'style="color: blue;"'; ?> > Enviar para aprovação</button>
                                                    <button onclick="verAnexos('<?= $dados->id; ?>','<?= $dados->status; ?>' )" class="dropdown-item  " > Documentos</button>
                                                    
                                                    <button onclick="abrirReq('<?= base64_encode(json_encode($dados)); ?>')" class="dropdown-item">Ver requisição</button>
                                                    <!-- <button onclick="abrirResumoDiario('<?= $valores->justificativa; ?>')"  class="dropdown-item">Ver justificativa</button> -->
                                                   
                                                    <a href="<?= base_url('variaveis/sobreaviso/editar/'.base64_encode($dados->id)); ?>" class="dropdown-item <?= ($dados->status != '1') ? 'disabled' : ''; ?>">Editar requisição</a>

                                                    <button onclick="excluirReq('<?= $dados->id; ?>')" <?= ($dados->status != '1') ? 'disabled' : 'style="color: red;"'; ?> class="dropdown-item" >Excluir</button>
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
                                        <th width="60" class="text-rigth">Usuário Aprovador/Sincronização</th>
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
                <h5 class="modal-title  mt-0 text-white"><i class="mdi mdi-file-document-box-outline"></i> Anexos</h5>
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
                    <button type="button" class="btn btn-primary" style="visibility: hidden;" id="btnTermoSubstituicao"><i class="fas fa-file-pdf"></i> Termo de Sobreaviso</button>
            
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal_visualiza_req" tabindex="-1" role="dialog" aria-labelledby="modal_visualiza_req" aria-hidden="true" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" style="max-width: 90%; width: 90%;">
        <div class="modal-content modal-content-full">
            <div class="modal-header bg-dark">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-file-document-box-outline"></i> Auxilio Sobreaviso</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        
                      
                    <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#auxilio_sobreaviso" role="tab"><i class="fa fa-home"></i> sobreaviso</a>
                            </li>
                          
                    </ul>
                    <div class="tab-content">
                            <!-- auxilio sobreaviso -->
                            <div class="tab-pane active p-3" id="auxilio_sobreaviso" role="tabpanel">
                                <div class="form-group row mb-2">
                                    <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm">Tipo de Requisição:</label>
                                    <div class="col-sm-8">
                                        <select disabled  class="select2 custom-select form-control form-control-sm" name="tipoReq" id="tipoReq">
                                           
                                        </select>
                                    
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="Reqfuncionario" class="col-sm-2 col-form-label text-right text-left-sm">Funcionário:</label>
                                    <div class="col-sm-10">
                                        <select disabled class="select2 custom-select form-control form-control-sm" name="Reqfuncionario" id="Reqfuncionario">
                                           
                                                  
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="tipoReq" class="col-sm-2 col-form-label text-sm-right text-left">Quantidade de horas:</label>
                                    <div class="col-sm-10">
                                        <input disabled class="form-control form-control-sm" type="number" value="" name="valor" id="valor" min="1" step="1" required>
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
.modal-content-full {
        height: auto;
        min-height: 100%;
        border-radius: 0;
    }

</style>
<script>
    $(document).ready(function() {
        var table = $('#datatable').DataTable({
        "aLengthMenu": [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength": 50,
        "aaSorting": [[1, "desc"]],
        });
    });
    $('#select-all').on('click', function() {
        $('input[name="idbatida[]"]:not(:disabled)').prop('checked', this.checked);
    });

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
            Swal.fire('Erro', 'Apenas arquivos com as extensões .jpeg ou .pdf são permitidos.', 'error');
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
            url: "<?= base_url('variaveis/sobreaviso/salvarAnexo'); ?>",  // Altere para a URL do seu endpoint de upload
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

    $('#btnTermoSubstituicao').click(function(e) {
        e.preventDefault();
        let id = $("#id").val();

        
        $.ajax({
            url: '<?= base_url('variaveis/sobreaviso/substituicaoTermo') ?>',
            type: 'GET',
            data: { id: id }, // Passa os dados como um objeto
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response) {
                var link = document.createElement('a');
                var url = window.URL.createObjectURL(response);
                link.href = url;
                link.download = 'termo_de_sobreaviso.pdf';
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
                                "situacao": '2' 
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
                                        exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/sobreaviso'); ?>');
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
    const abrirResumoDiario = (justificativa) => {

        $(".modal_resumo_diario").modal('show');

        $('.modal_resumo_diario tbody').html('');

        $('.modal_resumo_diario tbody').append('<tr><td class="">' + justificativa + '</td></tr>');
    }
   
    

    const selecionaFuncionario = (codigo) => {
        let dados = {
            "codigo":codigo,
            
        }
        openLoading();

        $.ajax({
            url: "<?= base_url('variaveis/sobreaviso/selectFunc'); ?>",
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
                            <td>${historico.req}</td>]
                            
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
        
    const verAnexos = (id, status) => {
        const novoTitulo = `Anexos - ${id}`; // Exemplo de como construir o novo título
        $(".modal_Anexos .modal-title").text(novoTitulo);
    $('.modal_Anexos tbody').html('');
    $('#anexo').val(''); // Limpa o campo de anexo
    $("#id").val(id);
    if (status != 1) {
        $('#anexo').prop('disabled', true);
    } else {
        $('#anexo').prop('disabled', false);
    }
    if (status == 1) {
            $('#anexo').prop('disabled', false);
            $('#btnTermoSubstituicao').css('visibility', 'visible'); // Torna o botão visível
    } else {
            $('#btnTermoSubstituicao').css('visibility', 'hidden'); // Mantém o botão oculto
    }
    $.ajax({
        url: "<?= base_url('variaveis/sobreaviso/Anexos'); ?>",
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


    const validaAnexos = (id, callback) => {
        $.ajax({
            url: "<?= base_url('variaveis/sobreaviso/Anexos'); ?>",
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


    



    const abrirReq = (dados) => {
        dados = atob(dados);
        const req = JSON.parse(dados);
        const valor = JSON.parse(req.valores);
        const novoTitulo = `Auxilio Sobreaviso - ${req.id}`; // Exemplo de como construir o novo título
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
        $("#valor").val(valor.valor);
      
    }
    const Filtro = () => {
		
		
		openLoading();
		$("#form_filtro").attr('action', base_url + '/variaveis/sobreaviso').attr('target', '_self');
		document.getElementById("form_filtro").submit();

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
                    url: "<?= base_url('variaveis/sobreaviso/deleteAnexo'); ?>",
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
                    url: "<?= base_url('variaveis/sobreaviso/delete'); ?>",
                    type: 'POST',
                    data: dados, // Enviar o ID para exclusão
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg, 2);
                        } else {
                            exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/sobreaviso'); ?>');
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
                        "situacao": '2' 
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
                                exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/sobreaviso'); ?>');
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
