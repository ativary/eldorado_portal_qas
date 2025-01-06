"use strict";
const selecionaRelatorio = (idRel, selecionados = '') => {

    var colunas = {};
    switch(idRel){
        case '1': 
            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'NOME_FILIAL', 'name': 'Nome Filial'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CODIGO_FUNCAO', 'name': 'Cód. Função'},
                {'value':'FUNCAO', 'name': 'Nome Função'},
                {'value':'CC', 'name': 'Cód. Centro de Custo'},
                {'value':'DESCRICAO_CC', 'name': 'Nome Centro de Custo'},
                {'value':'SECAO', 'name': 'Cód. Seção'},
                {'value':'DESCRICAO_SECAO', 'name': 'Nome Seção'},
                {'value':'CODIGO_ABONO', 'name': 'Cód. Abono'},
                {'value':'TIPO_ABONO', 'name': 'Nome Abono'},
                {'value':'DATA', 'name': 'Data Abono'},
                {'value':'HORA_INICIO', 'name': 'Hora Início'},
                {'value':'HORA_TERMINO', 'name': 'Hora Fim'}
            ];
        break;
        case '2':

            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'FILIAL', 'name': 'Nome Filial'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODIGO_FUNCAO', 'name': 'Cód. Função'},
                {'value':'FUNCAO', 'name': 'Nome Função'},
                {'value':'CC', 'name': 'Cód. Centro de Custo'},
                {'value':'DESCRICAO_CC', 'name': 'Nome Centro de Custo'},
                {'value':'SECAO', 'name': 'Cód. Seção'},
                {'value':'DESCRICAO_SECAO', 'name': 'Nome Seção'},
                {'value':'CODIGO_SITUACAO', 'name': 'Cód. Situação'},
                {'value':'SITUACAO', 'name': 'Situação'},
                {'value':'DATA_ADMISSAO', 'name': 'Data Admissão'},
                {'value':'DATA_TRANSFERENCIA', 'name': 'Data transferência'},
                {'value':'DATA_DEMISSAO', 'name': 'Data demissão'},
                {'value':'COD_SINDICATO_PONTO', 'name': 'Cód. Sindicato Ponto'},
                {'value':'SINDICATO_PONTO', 'name': 'Sindicato Ponto'},
                {'value':'COD_SINDICATO_FOLHA', 'name': 'Cód. Sindicato Folha'},
                {'value':'SINDICATO_FOLHA', 'name': 'Sindicato Folha'}

            ];
        break;
        case '3':

            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CODIGO_FUNCAO', 'name': 'Cód. Função'},
                {'value':'FUNCAO', 'name': 'Nome Função'},
                {'value':'CC', 'name': 'Cód. Centro de Custo'},
                {'value':'DESCRICAO_CC', 'name': 'Nome Centro de Custo'},
                {'value':'SECAO', 'name': 'Cód. Seção'},
                {'value':'DESCRICAO_SECAO', 'name': 'Nome Seção'},
                {'value':'TIPO_AFASTAMENTO', 'name': 'Tipo Afastamento'},
                {'value':'INICIO_AFASTAMENTO', 'name': 'Início Afastamento'},
                {'value':'TERMINO_AFASTAMENTO', 'name': 'Término Afastamento'},
                {'value':'QTD_DIAS', 'name': 'Qtd. Dias'}


            ];
        break;
        case '4':
            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CODIGO_FUNCAO', 'name': 'Cód. Função'},
                {'value':'FUNCAO', 'name': 'Nome Função'},
                {'value':'CC', 'name': 'Cód. Centro de Custo'},
                {'value':'DESCRICAO_CC', 'name': 'Nome Centro de Custo'},
                {'value':'SECAO', 'name': 'Cód. Seção'},
                {'value':'DESCRICAO_SECAO', 'name': 'Nome Seção'},
                {'value':'CODIGO_SINDICATO', 'name': 'Cód. Sindicato'},
                {'value':'SINDICATO', 'name': 'Sindicato'},
                {'value':'DATA', 'name': 'Data'},
                {'value':'DIA_SEMANA', 'name': 'Dia semana'},
                {'value':'ESCALA', 'name': 'Escala'},
                {'value':'HORARIO', 'name': 'Horário'},
                {'value':'DESC_HORARIO', 'name': 'Desc. horário'},
                {'value':'ENTRADA', 'name': 'Entrada'},
                {'value':'HR_REFEICAO', 'name': 'Hr Refeição'},
                {'value':'HRS_DIRECAO', 'name': 'Horas Direção'},
                {'value':'TIPO_ABONO', 'name': 'Tipo abono'},
                {'value':'HRS_NORMAIS', 'name': 'Hrs. Normais'},
                {'value':'HRS_ABONADAS', 'name': 'Hrs. Abonadas'},
                {'value':'HRS_FALTAS', 'name': 'Hrs. Faltas'},
                {'value':'HRS_ATRASOS', 'name': 'Hrs. Atrasos'},
                {'value':'HRS_ESPERA', 'name': 'Hrs. Espera'},
                {'value':'HRS_50', 'name': 'Hrs. 50%'},
                {'value':'HRS_60', 'name': 'Hrs. 60%'},
                {'value':'HRS_80', 'name': 'Hrs. 80%'},
                {'value':'HRS_100', 'name': 'Hrs. 100%'},
                {'value':'AD_NOTURNO_25', 'name': 'Ad. Noturno 25%'},
                {'value':'AD_NOTURNO_30', 'name': 'Ad. Noturno 30%'},
                {'value':'AD_NOTURNO_35', 'name': 'Ad. Noturno 35%'},
                {'value':'AD_NOTURNO_40', 'name': 'Ad. Noturno 40%'},
                {'value':'DEBITO_BH', 'name': 'Debito BH'},
                {'value':'SALDO_BH_COM_ACRESCIMO', 'name': 'Saldo BH c/ Acréscimo'},
                {'value':'JUSTIFICATIVA_EXTRA', 'name': 'Justificativas de Horas Extras'}

            ];
        break;
        case '5':
            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CODIGO_FUNCAO', 'name': 'Cód. Função'},
                {'value':'FUNCAO', 'name': 'Nome Função'},
                {'value':'CC', 'name': 'Cód. Centro de Custo'},
                {'value':'DESCRICAO_CC', 'name': 'Nome Centro de Custo'},
                {'value':'SECAO', 'name': 'Cód. Seção'},
                {'value':'DESCRICAO_SECAO', 'name': 'Nome Seção'},
                {'value':'DATA', 'name': 'Data'},
                {'value':'DIA_SEMANA', 'name': 'Dia semana'},
                {'value':'ESCALA', 'name': 'Escala'},
                {'value':'HORARIO', 'name': 'Horário'},
                {'value':'DESC_HORARIO', 'name': 'Desc. horário'},
                {'value':'BATIDAS', 'name': 'batidas'},
                {'value':'HORAS_TRABALHADAS', 'name': 'Hrs trabalhadas (+06 horas)'}
             
            ];
        break;
        case '6':
            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CODIGO_FUNCAO', 'name': 'Cód. Função'},
                {'value':'FUNCAO', 'name': 'Nome Função'},
                {'value':'CC', 'name': 'Cód. Centro de Custo'},
                {'value':'DESCRICAO_CC', 'name': 'Nome Centro de Custo'},
                {'value':'SECAO', 'name': 'Cód. Seção'},
                {'value':'DESCRICAO_SECAO', 'name': 'Nome Seção'},
                {'value':'DATA', 'name': 'Data'},
                {'value':'DIA_SEMANA', 'name': 'Dia semana'},
                {'value':'ESCALA', 'name': 'Escala'},
                {'value':'HORARIO', 'name': 'Horário'},
                {'value':'JORNADA', 'name': 'Jornada'},
                {'value':'HORARIO_NOME', 'name': 'Desc. Horário'},
                {'value':'BATIDAS', 'name': 'Batidas'},
                {'value':'INTERJORNADA', 'name': 'Interjornada'}
             
            ];
        break;
        case '7':
            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CODIGO_FUNCAO', 'name': 'Cód. Função'},
                {'value':'FUNCAO', 'name': 'Nome Função'},
                {'value':'CC', 'name': 'Cód. Centro de Custo'},
                {'value':'DESCRICAO_CC', 'name': 'Nome Centro de Custo'},
                {'value':'SECAO', 'name': 'Cód. Seção'},
                {'value':'DESCRICAO_SECAO', 'name': 'Nome Seção'},
                {'value':'DATA', 'name': 'Data'},
                {'value':'DIA_SEMANA', 'name': 'Dia semana'},
                {'value':'ESCALA', 'name': 'Escala'},
                {'value':'JORNADA', 'name': 'Jornada'},
                {'value':'HORARIO', 'name': 'Horário'},
                {'value':'HORARIO_NOME', 'name': 'Desc. horário'},
                {'value':'BATIDAS', 'name': 'batidas'},
                {'value':'VALOR', 'name': 'Valor'}
             
            ];
        break;

        case '8':
            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CODIGO_FUNCAO', 'name': 'Cód. Função'},
                {'value':'FUNCAO', 'name': 'Nome Função'},
                {'value':'CC', 'name': 'Cód. Centro de Custo'},
                {'value':'DESCRICAO_CC', 'name': 'Nome Centro de Custo'},
                {'value':'SECAO', 'name': 'Cód. Seção'},
                {'value':'DESCRICAO_SECAO', 'name': 'Nome Seção'},
                {'value':'CODIGO_SINDICATO', 'name': 'Cód. Sindicato'},
                {'value':'SINDICATO', 'name': 'Sindicato'},
                {'value':'DATA', 'name': 'Data'},
                {'value':'DIA_SEMANA', 'name': 'Dia semana'},
                {'value':'ESCALA', 'name': 'Escala'},
                {'value':'HORARIO', 'name': 'Horário'},
                {'value':'HORARIO_NOME', 'name': 'Desc. horário'},
                {'value':'BATIDAS', 'name': 'batidas'},
                {'value':'HR_REFEICAO', 'name': 'Hrs refeição'}
             
            ];
        break;

        case '9':
            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CODIGO_FUNCAO', 'name': 'Cód. Função'},
                {'value':'FUNCAO', 'name': 'Nome Função'},
                {'value':'CC', 'name': 'Cód. Centro de Custo'},
                {'value':'DESCRICAO_CC', 'name': 'Nome Centro de Custo'},
                {'value':'SECAO', 'name': 'Cód. Seção'},
                {'value':'DESCRICAO_SECAO', 'name': 'Nome Seção'},
                {'value':'SINDICATO', 'name': 'Sindicato'},
                {'value':'DATA', 'name': 'Data'},
                {'value':'DIA_SEMANA', 'name': 'Dia semana'},
                {'value':'ESCALA', 'name': 'Escala'},
                {'value':'JORNADA', 'name': 'Jornada'},
                {'value':'HORARIO', 'name': 'Horário'},
                {'value':'DESCRICAO_HORARIO', 'name': 'Desc. horário'},
                {'value':'BATIDAS', 'name': 'batidas'},
                {'value':'HORARIO_BRITANICO', 'name': 'Horario britanico'}
             
            ];
        break;

        case '10':
            colunas = [
                {'value':'CODCOLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'SALDO', 'name': 'Saldo'}
            ];
        break;
         
        case '11':
            colunas = [
                {'value':'CODCOLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CODFILIAL', 'name': 'Cód. Filial'},
                {'value':'NOMESECAO', 'name': 'Nome Seção'},
                {'value':'CODCUSTO', 'name': 'Cód. Centro de Custo'},
                {'value':'NOMECUSTO', 'name': 'Nome Centro de Custo'},
                {'value':'SINDICATO', 'name': 'Sindicato'},
                {'value':'INICIO_PERIODO', 'name': 'Início Período'},
                {'value':'FIM_PERIODO', 'name': 'Fim Período'},
                {'value':'DATA', 'name': 'Data'},
                {'value':'SALDO_INICIAL', 'name': 'Saldo Inicial'},
                {'value':'OCORRENCIA', 'name': 'Ocorrência'},
                {'value':'HORAS_POSITIVAS', 'name': 'Horas Positivas'},
                {'value':'HORAS_NEGATIVAS', 'name': 'Horas Negativas'},
                {'value':'TOTAL', 'name': 'Total'}
                
            ];
        break;
         
        case '12':
            colunas = [
                {'value':'CODCOLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'NOMEFUNCAO', 'name': 'Nome Função'},
                {'value':'CODCUSTO', 'name': 'Cód. Centro de Custo'},
                {'value':'DESCRICAO_CUSTO', 'name': 'Nome Centro de Custo'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'NOMESECAO', 'name': 'Nome Seção'},
                {'value':'LIDER', 'name': 'Líder'},
                {'value':'GESTOR', 'name': 'Gestor'},
                {'value':'OPERACAO', 'name': 'Operação'}
            ];
        break;
         
        case '13':
            colunas = [
                {'value':'CODCOLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODFILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'SECAO', 'name': 'Descrição Seção'},
                {'value':'CODCUSTO', 'name': 'Cód. Centro Custo'},
                {'value':'CENTROCUSTO', 'name': 'Nome Centro de Custo'},
                {'value':'DATA', 'name': 'Data Batida'},
                {'value':'BATIDA', 'name': 'Batida'},
                {'value':'STATUS', 'name': 'Status'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa da Batida'},
                {'value':'DATA_REGISTRO', 'name': 'Data Registro'},
                {'value':'HORA_REGISTRO', 'name': 'Hora Registro'},
                {'value':'USUARIO', 'name': 'Usuário'},
                {'value':'APROVADOR', 'name': 'Aprovador'},
                {'value':'DATA_APROVADOR', 'name': 'Data Aprovador'},
                {'value':'HORA_APROVADOR', 'name': 'Hora Aprovador'},
                {'value':'POSSUI_ANEXO', 'name': 'Possui Anexo'},
            ];
        break;

        case '14':
            colunas = [
                {'value':'CODCOLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODFILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'SECAO', 'name': 'Seção'},
                {'value':'CCUSTO', 'name': 'Cód. Centro Custo'},
                {'value':'CUSTO', 'name': 'Nome Centro de Custo'},
                {'value':'DATA', 'name': 'Data Batida'},
                {'value':'BATIDA', 'name': 'Batida'},
                {'value':'STATUS', 'name': 'Status'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa da Batida'},
                {'value':'CODUSUARIO', 'name': 'Usuário'},
                {'value':'DATA_ALTERACAO', 'name': 'Data Alteração'},
                {'value':'HORA_ALTERACAO', 'name': 'Hora Alteração'},
                {'value':'LINHAORIGINAL_DATA', 'name': 'Linha Original Data'},
                {'value':'LINHAORIGINAL_HORA', 'name': 'Linha Original Hora'},
            ];
        break;
         
        case '15':
            colunas = [
                {'value':'CODCOLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODFILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'SECAO', 'name': 'Seção'},
                {'value':'CODCUSTO', 'name': 'Cód. Centro Custo'},
                {'value':'CENTROCUSTO', 'name': 'Nome Centro de Custo'},
                {'value':'DATA', 'name': 'Data Batida'},
                {'value':'BATIDA', 'name': 'Batida'},
                {'value':'STATUS', 'name': 'Status'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa da Batida'},
                {'value':'USUARIO', 'name': 'Usuário'},
                {'value':'DATA_REGISTRO', 'name': 'Data Registro'},
                {'value':'HORA_REGISTRO', 'name': 'Hora Registro'},
                {'value':'USUARIO_REPROVA', 'name': 'Usuário Reprova'},
                {'value':'MOTIVO_REPROVA', 'name': 'Motivo Reprova'},
                {'value':'DATA_REPROVA', 'name': 'Data Reprova'},
                {'value':'HORA_REPROVA', 'name': 'Hora Reprova'},
                
            ];
        break;
         
        case '16':
            colunas = [
                {'value':'CHAPA', 'name': 'Chapa Funcionário'},
                {'value':'NOME', 'name': 'Nome Funcionário'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'SECAO', 'name': 'Seção'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'CPF', 'name': 'CPF'},
                {'value':'DATA_INICIO', 'name': 'Data Início'},
                {'value':'DATA_FIM', 'name': 'Data Fim'},
                {'value':'DATA_CADASTRO', 'name': 'Data Cadastro'},
                {'value':'PLACA', 'name': 'Placa'},
                {'value':'STATUS', 'name': 'Status Macro'},
                {'value':'TEMPO', 'name': 'Tempo'},                
            ];
        break;
        case '26':
            colunas = [
                {'value': 'CODCOLIGADA', 'name': 'Código Coligada'},
                {'value': 'CODFILIAL', 'name': 'Código Filial'},
                {'value': 'CHAPA', 'name': 'Chapa'},
                {'value': 'NOME', 'name': 'Nome'},
                {'value': 'FUNCAO', 'name': 'Função'},
                {'value': 'CODSECAO', 'name': 'Código Seção'},
                {'value': 'SECAO', 'name': 'Seção'},
                {'value': 'CODCUSTO', 'name': 'Código Centro Custo'},
                {'value': 'CENTRO_CUSTO', 'name': 'Centro de Custo'},
                {'value': 'DATA', 'name': 'Data'},
                {'value': 'BATIDA', 'name': 'Batida'},
                {'value': 'STATUS', 'name': 'Status'},
                {'value': 'DATA_REGISTRO', 'name': 'Data Registro'},
                {'value': 'HORA_REGISTRO', 'name': 'Hora Registro'},
                {'value': 'SISTEMA_ORIGEM', 'name': 'Sistema de Origem'},
            ]
        break;
        case '27':
            colunas = [
                {'value': 'CODCOLIGADA', 'name': 'Código Coligada'},
                {'value': 'CODFILIAL', 'name': 'Código Filial'},
                {'value': 'CHAPA', 'name': 'Chapa'},
                {'value': 'NOME', 'name': 'Nome'},
                {'value': 'FUNCAO', 'name': 'Função'},
                {'value': 'CODSECAO', 'name': 'Código Seção'},
                {'value': 'SECAO', 'name': 'Seção'},
                {'value': 'CODCUSTO', 'name': 'Código Centro Custo'},
                {'value': 'CENTRO_CUSTO', 'name': 'Centro de Custo'},
                {'value': 'DATA', 'name': 'Data'},
                {'value': 'BATIDA', 'name': 'Batida'},
                {'value': 'ABONO', 'name': 'Abono'},
                {'value': 'STATUS', 'name': 'Status'},
                {'value': 'JUSTIFICATIVA', 'name': 'Justificativa'},
                {'value': 'DATA_REGISTRO', 'name': 'Data Registro'},
                {'value': 'HORA_REGISTRO', 'name': 'Hora Registro'},
                {'value': 'POSSUI_ANEXO', 'name': 'Possui Anexo'},
                {'value': 'USUARIO', 'name': 'Usuário'},
                {'value': 'APROVADOR', 'name': 'Aprovador'},
                {'value': 'DATA_APROVACAO', 'name': 'Data Aprovação'},
                {'value': 'HORA_APROVACAO', 'name': 'Hora Aprovação'},
               
                {'value': 'DATA_HORA_SINCRONISMO_RM', 'name': 'Data Hora Sincronismo RM'},
            ]
            
        break;
    }

    $("#colunas").html('');
    var selected = selecionados.split(',');
    
    for(var x=0; x<colunas.length; x++){
        var liberado = true;
        if(selecionados != ''){
            if(!selected.includes(colunas[x].value)) var liberado = false;
        }
        if(liberado){
            $("#colunas").append('<option value="'+colunas[x].value+'" selected>'+colunas[x].name+'</option>');
        }else{
            $("#colunas").append('<option value="'+colunas[x].value+'" >'+colunas[x].name+'</option>');
        }
    }

}
const procurarFuncionario = () => {
    
    let keyword = $('[name=keyword]').val();
    if(keyword.length < 4){ exibeAlerta('warning', 'Digite pelo menos 4 caracteres para pesquisar.'); return false; }

    openLoading();
    
    $("select[name=chapa]").html('');

    $.ajax({
        url: base_url + '/relatorio/gerar/action/lista_funcionarios',
        type: 'POST',
        data: {
            'keyword'  : keyword
        },
        success: function(result) {

            openLoading(true);

            try {
                var response = JSON.parse(result);

                $("select[name=chapa]").append('<option value="">Selecione o Funcionário ('+response.length+')</option>');
                if(response.length >= 1000){exibeAlerta('info', 'Qtde de registro ultrapassou o limite permitido, exibindo os primeiros 1000 registros.');}
                for(var x = 0; x < response.length; x++){
                    $("select[name=chapa]").append('<option value="'+response[x].CHAPA+'">'+response[x].NOME + ' - ' +response[x].CHAPA+'</option>');
                }

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

}
const gerarRelatorioTabela = () => {

    var relatorio = $('#relatorio').val();
    var dataI = $('#dataIni').val();
    var dataF = $('#dataFim').val();

    if(relatorio == 1 && dataI == "" || relatorio == 1 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 2 && dataI == "" || relatorio == 2 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 4 && dataI == "" || relatorio == 4 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 5 && dataI == "" || relatorio == 5 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 6 && dataI == "" || relatorio == 6 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 7 && dataI == "" || relatorio == 7 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 8 && dataI == "" || relatorio == 8 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 9 && dataI == "" || relatorio == 9 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 10 && dataF == ""){ exibeAlerta('warning', '<b>Data Fim</b> obrigatório.'); return false; }
    if(relatorio == 11 && dataF == ""){ exibeAlerta('warning', '<b>Data Fim</b> obrigatório.'); return false; }
    if((relatorio == 13 || relatorio == 16 || relatorio == 2) && dataI == "" && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }

    if(relatorio == ""){ exibeAlerta('warning', 'Selecione um tipo de relatório.'); return false; }

    openLoading();

    $("[name=colunas_filtro]").val($("#colunas").val());
//     openLoading(true)
// ;return false;
    $("#form_filtro").attr('action',base_url + '/relatorio/gerar').attr('target', '_self');
    $("#form_filtro").submit();
}
const gerarExcel = () => {

    let relatorio = $('#relatorio').val();
    let dataI = $('#dataIni').val();
    let dataF = $('#dataFim').val();

    if(relatorio == 1 && dataI == "" || relatorio == 1 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 4 && dataI == "" || relatorio == 4 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 5 && dataI == "" || relatorio == 5 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 6 && dataI == "" || relatorio == 6 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 7 && dataI == "" || relatorio == 7 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 8 && dataI == "" || relatorio == 8 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 9 && dataI == "" || relatorio == 9 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 10 && dataF == ""){ exibeAlerta('warning', '<b>Data Fim</b> obrigatório.'); return false; }
    if((relatorio == 13 || relatorio == 16) && dataI == "" && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    
    if(relatorio == ""){ exibeAlerta('warning', 'Selecione um tipo de relatório.'); return false; }
  

    $("#form_filtro").attr('action',base_url + '/relatorio/gerar/excel').attr('target', '_blank');
    $("#form_filtro").submit();
}
const gerarPDF = () => {

    let relatorio = $('#relatorio').val();
    let dataI = $('#dataIni').val();
    let dataF = $('#dataFim').val();

    if(relatorio == 1 && dataI == "" || relatorio == 1 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 4 && dataI == "" || relatorio == 4 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 5 && dataI == "" || relatorio == 5 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 6 && dataI == "" || relatorio == 6 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 7 && dataI == "" || relatorio == 7 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 8 && dataI == "" || relatorio == 8 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 9 && dataI == "" || relatorio == 9 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 10 && dataF == ""){ exibeAlerta('warning', '<b>Data Fim</b> obrigatório.'); return false; }
    if(relatorio == 11 && dataF == ""){ exibeAlerta('warning', '<b>Data Fim</b> obrigatório.'); return false; }
    if((relatorio == 13 || relatorio == 16) && dataI == "" && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }

    if(relatorio == ""){ exibeAlerta('warning', 'Selecione um tipo de relatório.'); return false; }

    $("#form_filtro").attr('action',base_url + '/relatorio/gerar/pdf').attr('target', '_blank');
    $("#form_filtro").submit();

}
const gerarCSV = () => {

    let dataI = $('#dataIni').val();
    let dataF = $('#dataFim').val();
    let relatorio = $('#relatorio').val();

    if(relatorio == 1 && dataI == "" || relatorio == 1 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 4 && dataI == "" || relatorio == 4 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 5 && dataI == "" || relatorio == 5 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 6 && dataI == "" || relatorio == 6 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 7 && dataI == "" || relatorio == 7 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 8 && dataI == "" || relatorio == 8 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if(relatorio == 9 && dataI == "" || relatorio == 9 && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    if((relatorio == 13 || relatorio == 16) && dataI == "" && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    
    if(relatorio == ""){ exibeAlerta('warning', 'Selecione um tipo de relatório.'); return false; }


    $("#form_filtro").attr('action',base_url + '/relatorio/gerar/csv').attr('target', '_blank');
    $("#form_filtro").submit();

}

$(document).ready(function(){

    selecionaRelatorio($('#relatorio').val(), $("[name=colunas_filtro]").val());
    // if($('#table_relatorios').length > 0){
    //     $('#table_relatorios').DataTable({
    //         "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
    //         "iDisplayLength"    : 50,
    //         "aaSorting"         : [[0, "desc"]]
    //     });
    // }
    // $("#colunas").html(decodeURIComponent($("[name=colunas_filtro]").val()));
});