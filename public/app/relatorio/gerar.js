"use strict";
const selecionaRelatorio = (idRel, selecionados = '') => {

    var colunas = {};
    switch(idRel){
        case '1': 
            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'NOME_FILIAL', 'name': 'Nome Filial'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
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
                {'value':'COD_COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'COD_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
                {'value':'COD_FUNCAO', 'name': 'Cód. Função'},
                {'value':'FUNCAO', 'name': 'Nome Função'},
                {'value':'DATA_ADMISSAO', 'name': 'Data Admissão'},
                {'value':'DATA_NASCIMENTO', 'name': 'Data Nascimento'},
                {'value':'GENERO', 'name': 'Gênero'},
                {'value':'SITUACAO', 'name': 'Situação'},
                {'value':'DATA_DEMISSAO', 'name': 'Data demissão'},
                {'value':'COD_CUSTO', 'name': 'Cód. Centro de Custo'},
                {'value':'DESCRICAO_CUSTO', 'name': 'Nome Centro de Custo'},
                {'value':'COD_SECAO', 'name': 'Cód. Seção'},
                {'value':'DESCRICAO_SECAO', 'name': 'Nome Seção'},
                {'value':'COD_SIND_PONTO', 'name': 'Cód. Sindicato Ponto'},
                {'value':'DESCRIÇÃO_SIND_PONTO', 'name': 'Sindicato Ponto'},
                {'value':'COD_SIND_FOLHA', 'name': 'Cód. Sindicato Folha'},
                {'value':'DESCRIÇÃO_SIND_FOLHA', 'name': 'Sindicato Folha'}                

            ];
        break;
        case '3':

            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
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
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
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
                {'value':'HRS_EM_ESPERA', 'name': 'Horas Em Espera'},
                {'value':'HRS_PARADO', 'name': 'Horas Parado'},
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
                {'value':'JUSTIFICATIVA_EXTRA', 'name': 'Justificativas de Horas Extras'},
                {'value':'OBS', 'name': 'Observação'}

            ];
        break;
        case '5':
            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
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
                {'value':'REGISTROS', 'name': 'registros'},
                {'value':'HORAS_TRABALHADAS', 'name': 'Hrs trabalhadas (+06 horas)'}
             
            ];
        break;
        case '6':
            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
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
                {'value':'REGISTROS', 'name': 'Registros'},
                {'value':'INTERJORNADA', 'name': 'Interjornada'}
             
            ];
        break;
        case '7':
            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
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
                {'value':'REGISTROS', 'name': 'registros'},
                {'value':'VALOR', 'name': 'Valor'}
             
            ];
        break;

        case '8':
            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
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
                {'value':'REGISTROS', 'name': 'registros'},
                {'value':'HR_REFEICAO', 'name': 'Hrs refeição'}
             
            ];
        break;

        case '9':
            colunas = [
                {'value':'COLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CODIGO_FILIAL', 'name': 'Cód. Filial'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
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
                {'value':'REGISTROS', 'name': 'registros'},
                {'value':'HORARIO_BRITANICO', 'name': 'Horario britanico'}
             
            ];
        break;

        case '10':
            colunas = [
                {'value':'CODCOLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
                {'value':'SALDO', 'name': 'Saldo'}
            ];
        break;
         
        case '11':
            colunas = [
                {'value':'CODCOLIGADA', 'name': 'Cód. Coligada'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
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
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
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
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'SECAO', 'name': 'Descrição Seção'},
                {'value':'CODCUSTO', 'name': 'Cód. Centro Custo'},
                {'value':'CENTROCUSTO', 'name': 'Nome Centro de Custo'},
                {'value':'DATA', 'name': 'Data Registro'},
                {'value':'REGISTRO', 'name': 'Registro'},
                {'value':'STATUS', 'name': 'Status'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa do Registro'},
                {'value':'OBS', 'name': 'Observação'},
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
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'SECAO', 'name': 'Seção'},
                {'value':'CCUSTO', 'name': 'Cód. Centro Custo'},
                {'value':'CUSTO', 'name': 'Nome Centro de Custo'},
                {'value':'DATA', 'name': 'Data Registro'},
                {'value':'REGISTRO', 'name': 'Registro'},
                {'value':'STATUS', 'name': 'Status'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa do Registro'},
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
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'SECAO', 'name': 'Seção'},
                {'value':'CODCUSTO', 'name': 'Cód. Centro Custo'},
                {'value':'CENTROCUSTO', 'name': 'Nome Centro de Custo'},
                {'value':'DATA', 'name': 'Data Registro'},
                {'value':'REGISTRO', 'name': 'Registro'},
                {'value':'STATUS', 'name': 'Status'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa do Registro'},
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
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
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
                {'value':'ORIGEM', 'name': 'Origem'},                
            ];
        break;
        
        case '17':
            colunas = [
                {'value':'ID_REQUISIÇÂO', 'name': 'N requisição'},
                {'value':'TIPO_REQUISIÇÂO', 'name': 'Tipo da requisição'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CENTROCUSTO', 'name': 'Centro de custo'},
                {'value':'DESC_CUST', 'name': 'Descrição custo'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'SECAO', 'name': 'Seção'},
                {'value':'DATA_CADASTRO', 'name': 'Data Criação'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa Criação'},
                {'value':'USUARIO_SOLICITANTE', 'name': 'Usuário solicitante'},
                {'value':'DATA_APROVACAO', 'name': 'Data Aprovação'},
                {'value':'USUARIO_APROVACAO', 'name': 'Usuário Aprovador'},
                {'value':'JUSTIFICATIVA_APROV', 'name': 'Justificativa Aprovação'}, 
                {'value':'DATA_APROV_RH', 'name': 'Data RH'},
                {'value':'USUARIO_RH', 'name': 'Usuário RH'},
                {'value':'DATA_SINCRONIZAÇÂO', 'name': 'Data Sincronização'},
                {'value':'EVENTO', 'name': 'Evento'},
             
                {'value':'PERIODO_SINC', 'name': 'Periodo Sincronismo'},
               
            ];
        break;
        case '18':
            colunas = [
                {'value':'ID_REQUISIÇÂO', 'name': 'N requisição'},
                {'value':'TIPO_REQUISIÇÂO', 'name': 'Tipo da requisição'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'CENTROCUSTO', 'name': 'Centro de custo'},
                {'value':'DESC_CUST', 'name': 'Descrição custo'},
                {'value':'SECAO', 'name': 'Seção'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'Nome_dependente', 'name': 'Nome Dependente'},
                {'value':'Valor_Dependente', 'name': 'Valor Dependente'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa Criação'},
                {'value':'DATA_CADASTRO', 'name': 'Data Criação'},
                {'value':'USUARIO_SOLICITANTE', 'name': 'Usuário solicitante'},
                {'value':'DATA_APROVACAO', 'name': 'Data Aprovação'},
                {'value':'USUARIO_APROVACAO', 'name': 'Usuário Aprovador'},
                {'value':'DATA_APROV_RH', 'name': 'Data RH'},
                {'value':'USUARIO_RH', 'name': 'Usuário RH'},
                {'value':'DATA_SINCRONIZAÇÂO', 'name': 'Data Sincronização'},
                {'value':'EVENTO', 'name': 'Evento'},
                {'value':'JUSTIFICATIVA_APROV', 'name': 'Justificativa Aprovação'},
                {'value':'PERIODO_SINC', 'name': 'Periodo Sincronismo'},
               
            ];
        break;
        case '19':
            colunas = [
                {'value':'ID_REQUISIÇÂO', 'name': 'N requisição'},
                {'value':'TIPO_REQUISIÇÂO', 'name': 'Tipo da requisição'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'CENTROCUSTO', 'name': 'Centro de custo'},
                {'value':'DESC_CUST', 'name': 'Descrição custo'},
                {'value':'SECAO', 'name': 'Seção'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'Nome_dependente', 'name': 'Nome Dependente'},
                {'value':'Valor_Dependente', 'name': 'Valor Dependente'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa Criação'},
                {'value':'DATA_CADASTRO', 'name': 'Data Criação'},
                {'value':'USUARIO_SOLICITANTE', 'name': 'Usuário solicitante'},
                {'value':'DATA_APROVACAO', 'name': 'Data Aprovação'},
                {'value':'USUARIO_APROVACAO', 'name': 'Usuário Aprovador'},
                {'value':'JUSTIFICATIVA_APROV', 'name': 'Justificativa Aprovação'},
                {'value':'DATA_APROV_RH', 'name': 'Data RH'},
                {'value':'USUARIO_RH', 'name': 'Usuário RH'},
                {'value':'DATA_SINCRONIZAÇÂO', 'name': 'Data Sincronização'},
                {'value':'EVENTO', 'name': 'Evento'},
                
                {'value':'PERIODO_SINC', 'name': 'Periodo Sincronismo'},
               
            ];
        break;
        case '20':
            colunas = [
                {'value':'ID_REQUISIÇÂO', 'name': 'N requisição'},
                {'value':'TIPO_REQUISIÇÂO', 'name': 'Tipo da requisição'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CENTROCUSTO', 'name': 'Centro de custo'},
                {'value':'DESC_CUST', 'name': 'Descrição custo'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'SECAO', 'name': 'Seção'},
                {'value':'DATA_CADASTRO', 'name': 'Data Criação'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa Criação'},
                {'value':'VALOR', 'name': 'Valor'},
                {'value':'QUANTIDADE_MESES', 'name': 'Quantidade de Meses'},
                {'value':'USUARIO_SOLICITANTE', 'name': 'Usuário solicitante'},
                {'value':'DATA_APROVACAO', 'name': 'Data Aprovação'},
                {'value':'USUARIO_APROVACAO', 'name': 'Usuário Aprovador'},
                {'value':'JUSTIFICATIVA_APROV', 'name': 'Justificativa Aprovação'},
                {'value':'DATA_APROV_RH', 'name': 'Data RH'},
                {'value':'USUARIO_RH', 'name': 'Usuário RH'},
                {'value':'DATA_SINCRONIZAÇÂO', 'name': 'Data Sincronização'},
                {'value':'EVENTO', 'name': 'Evento'},
                {'value':'PERIODO_SINC', 'name': 'Periodo Sincronismo'},
               
            ];
        break;
        case '21':
            colunas = [
                {'value':'ID_REQUISIÇÂO', 'name': 'N requisição'},
                {'value':'TIPO_REQUISIÇÂO', 'name': 'Tipo da requisição'},

                {'value':'CHAPA_SUB', 'name': 'Chapa Colaborador substituido'},
                {'value':'NOME_SUB', 'name': 'Nome Colaborador substituido'},
                {'value':'CODSITUACAO_SUB', 'name': 'Cód. Situação substituido'},
                {'value':'CODSECAO_SUB', 'name': 'Cód. Seção substituido'},
                {'value':'SECAO_SUB', 'name': 'Seção substituido'},
                {'value':'FUNCAO_SUB', 'name': 'Função substituido'},
                {'value':'CENTROCUSTO_SUB', 'name': 'Centro de custo substituido'},
                {'value':'DESC_CUST_SUB', 'name': 'Descrição custo substituido'},
                {'value':'SALARIO_SUB', 'name': 'Salário substituido'},

                {'value':'CHAPA', 'name': 'Chapa Colaborador Substituto'},
                {'value':'NOME', 'name': 'Nome Colaborador Substituto'},
                {'value':'CENTROCUSTO', 'name': 'Centro de custo'},
                {'value':'DESC_CUST', 'name': 'Descrição custo'},
                {'value':'SALARIO', 'name': 'Salário subistituido'},

              

                {'value':'CODSECAO', 'name': 'Cód. Seção Subisituito'},
                {'value':'SECAO', 'name': 'Seção Subisituito'},
                {'value':'FUNCAO', 'name': 'Função Subisituito'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação Subisituito'},

                {'value':'ATESTADO', 'name': 'Atestados'},
                {'value':'FERIAS', 'name': 'Férias'},
                {'value':'FALTAS', 'name': 'Faltas'},
                
                {'value':'DATA_CADASTRO', 'name': 'Data Criação'},
                {'value':'USUARIO_SOLICITANTE', 'name': 'Usuário solicitante'},
                {'value':'DATA_APROVACAO', 'name': 'Data Aprovação'},
                {'value':'USUARIO_APROVACAO', 'name': 'Usuário Aprovador'},
                {'value':'JUSTIFICATIVA_APROV', 'name': 'Justificativa Aprovação'},
                {'value':'DATA_APROV_RH', 'name': 'Data RH'},
                {'value':'USUARIO_RH', 'name': 'Usuário RH'},
                {'value':'DATA_SINCRONIZAÇÂO', 'name': 'Data Sincronização'},
                {'value':'EVENTO', 'name': 'Evento'},
                {'value':'PERIODO_SINC', 'name': 'Periodo Sincronismo'},
               
            ];
            
        break;
        case '22':
            colunas = [
                {'value':'ID_REQUISIÇÂO', 'name': 'N requisição'},
                {'value':'TIPO_REQUISIÇÂO', 'name': 'Tipo da requisição'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CENTROCUSTO', 'name': 'Centro de custo'},
                {'value':'DESC_CUST', 'name': 'Descrição custo'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'SECAO', 'name': 'Seção'},
                {'value':'DATA_CADASTRO', 'name': 'Data Criação'},
                {'value':'SALARIO', 'name': 'Salario'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa Criação'},
                {'value':'VALOR', 'name': 'Valor'},
                {'value':'QUANTIDADE_HORAS', 'name': 'Quantidade de Horas'},
                {'value':'USUARIO_SOLICITANTE', 'name': 'Usuário solicitante'},
                {'value':'DATA_APROVACAO', 'name': 'Data Aprovação'},
                {'value':'USUARIO_APROVACAO', 'name': 'Usuário Aprovador'},
                {'value':'JUSTIFICATIVA_APROV', 'name': 'Justificativa Aprovação'},
                {'value':'DATA_APROV_RH', 'name': 'Data RH'},
                {'value':'USUARIO_RH', 'name': 'Usuário RH'},
                {'value':'DATA_SINCRONIZAÇÂO', 'name': 'Data Sincronização'},
                {'value':'EVENTO', 'name': 'Evento'},
                {'value':'PERIODO_SINC', 'name': 'Periodo Sincronismo'},
               
            ];
        break;
        case '23':
            colunas = [
                {'value':'ID_REQUISIÇÂO', 'name': 'N requisição'},
                {'value':'TIPO_REQUISIÇÂO', 'name': 'Tipo da requisição'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CENTROCUSTO', 'name': 'Centro de custo'},
                {'value':'DESC_CUST', 'name': 'Descrição custo'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'SECAO', 'name': 'Seção'},
                {'value':'DATA_CADASTRO', 'name': 'Data Criação'},
                {'value':'SALARIO', 'name': 'Salario'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa Criação'},
                {'value':'VALOR', 'name': 'Valor'},
                {'value':'QUANTIDADE_MESES', 'name': 'Quantidade de Meses'},
                {'value':'VALOR_PARCELA', 'name': 'Valor'},
                {'value':'USUARIO_SOLICITANTE', 'name': 'Usuário solicitante'},
                {'value':'DATA_APROVACAO', 'name': 'Data Aprovação'},
                {'value':'USUARIO_APROVACAO', 'name': 'Usuário Aprovador'},
                {'value':'JUSTIFICATIVA_APROV', 'name': 'Justificativa Aprovação'},
                {'value':'DATA_APROV_RH', 'name': 'Data RH'},
                {'value':'USUARIO_RH', 'name': 'Usuário RH'},
                {'value':'DATA_SINCRONIZAÇÂO', 'name': 'Data Sincronização'},
                {'value':'EVENTO', 'name': 'Evento'},
                {'value':'PERIODO_SINC', 'name': 'Periodo Sincronismo'},
               
            ];
        break;
        case '24':
            colunas = [
               
                {'value':'ID_REQUISIÇÂO', 'name': 'N requisição'},
                {'value':'TIPO_REQUISIÇÂO', 'name': 'Tipo da requisição'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CENTROCUSTO', 'name': 'Centro de custo'},
                {'value':'DESC_CUST', 'name': 'Descrição custo'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'SECAO', 'name': 'Seção'},
                {'value':'DATA_CADASTRO', 'name': 'Data Criação'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa Criação'},
                {'value':'VALOR', 'name': 'Valor'},
                {'value':'USUARIO_SOLICITANTE', 'name': 'Usuário solicitante'},
                {'value':'DATA_APROVACAO', 'name': 'Data Aprovação'},
                {'value':'USUARIO_APROVACAO', 'name': 'Usuário Aprovador'},
                {'value':'JUSTIFICATIVA_APROV', 'name': 'Justificativa Aprovação'},
                {'value':'DATA_APROV_RH', 'name': 'Data RH'},
                {'value':'USUARIO_RH', 'name': 'Usuário RH'},
                {'value':'DATA_SINCRONIZAÇÂO', 'name': 'Data Sincronização'},
                {'value':'EVENTO', 'name': 'Evento'},
                {'value':'PERIODO_SINC', 'name': 'Periodo Sincronismo'},
               
            ];
        break;
        case '25':
            colunas = [
               
                {'value':'ID_REQUISIÇÂO', 'name': 'N requisição'},
                {'value':'TIPO_REQUISIÇÂO', 'name': 'Tipo da requisição'},
                {'value':'CHAPA', 'name': 'Chapa Colaborador'},
                {'value':'NOME', 'name': 'Nome Colaborador'},
                {'value':'FUNCAO', 'name': 'Função'},
                {'value':'CODSITUACAO', 'name': 'Cód. Situação'},
                {'value':'CENTROCUSTO', 'name': 'Centro de custo'},
                {'value':'DESC_CUST', 'name': 'Descrição custo'},
                {'value':'CODSECAO', 'name': 'Cód. Seção'},
                {'value':'SECAO', 'name': 'Seção'},
                {'value':'DATA_CADASTRO', 'name': 'Data Criação'},
                {'value':'JUSTIFICATIVA', 'name': 'Justificativa Criação'},
                {'value':'VALOR', 'name': 'Valor'},
                {'value':'USUARIO_SOLICITANTE', 'name': 'Usuário solicitante'},
                {'value':'DATA_APROVACAO', 'name': 'Data Aprovação'},
                {'value':'USUARIO_APROVACAO', 'name': 'Usuário Aprovador'},
                {'value':'JUSTIFICATIVA_APROV', 'name': 'Justificativa Aprovação'},
                {'value':'DATA_APROV_RH', 'name': 'Data RH'},
                {'value':'USUARIO_RH', 'name': 'Usuário RH'},
                {'value':'DATA_SINCRONIZAÇÂO', 'name': 'Data Sincronização'},
                {'value':'EVENTO', 'name': 'Evento'},
                {'value':'PERIODO_SINC', 'name': 'Periodo Sincronismo'},
               
            ];
        break;

        // IMPLEMENTAÇÕES ATIVARY - MODULO PREMIOS
        case '26':
            colunas = [
                {'value':'NOME_PREMIO', 'name': 'Prêmio'},
                {'value':'TIPO_REQUISICAO', 'name': 'Tipo de Prêmio'},
                {'value':'DT_INICIO_PONTO', 'name': 'Início do Ponto'},
                {'value':'DT_FIM_PONTO', 'name': 'Fim do Ponto'},
                {'value':'CODFILIAL_COLAB', 'name': 'Filial'},
                {'value':'CHAPA_COLAB', 'name': 'Chapa Colaborador'},
                {'value':'NOME_COLAB', 'name': 'Nome Colaborador'},
                {'value':'FUNCAO_COLAB', 'name': 'Função'},
                {'value':'DT_ADMISSAO_COLAB', 'name': 'Data de Admissão'},
                {'value':'CCUSTO_COLAB', 'name': 'Centro de Custo'},
                {'value':'CODSECAO_COLAB', 'name': 'Codigo da Seção'},
                {'value':'SECAO_COLAB', 'name': 'Seção'},
                {'value':'CODSITUACAO_COLAB', 'name': 'Situação'},
                {'value':'PERCENT_TARGET', 'name': '% Target'},
                {'value':'PERCENT_REALIZADO', 'name': '% Realizado'},
                {'value':'DIAS_FALTAS', 'name': 'Faltas'},              
                {'value':'DATAS_FALTAS', 'name': 'Datas das Faltas'},
                {'value':'DEFLA_FALTAS', 'name': '% Deflator Faltas'},
                {'value':'DIAS_AFAST', 'name': 'Dias de Afastamento'},
                {'value':'DATAS_AFAST', 'name': 'Datas de Afastamento'},
                {'value':'DEFLA_AFAST', 'name': '% Deflator Afastamento'},
                {'value':'DIAS_ATESTADO', 'name': 'Dias de Atestado'},              
                {'value':'DATAS_ATESTADO', 'name': 'Datas de Atestado'},
                {'value':'DEFLA_ATESTADO', 'name': '% Deflator Atestados'},
                {'value':'DIAS_FERIAS', 'name': 'Dias de Férias'},
                {'value':'DATAS_FERIAS', 'name': 'Datas de Férias'},
                {'value':'DEFLA_FERIAS', 'name': '% Deflator Férias'},
                {'value':'DIAS_ADMISSAO', 'name': 'Dias até Admissão'},
                {'value':'DATAS_ADMISSAO', 'name': 'Datas até Admissão'},
                {'value':'DEFLA_ADMISSAO', 'name': '% Deflator de Admissão'},
                {'value':'DIAS_DEMISSAO', 'name': 'Dias até Demissão'},
                {'value':'DATAS_DEMISSAO', 'name': 'Datas até Demissão'},
                {'value':'DEFLA_DEMISSAO', 'name': '% Deflator de Demissão'},
                {'value':'DIAS_PERDIDOS', 'name': 'Total de Dias Perdidos'},
                {'value':'DIAS_DE_DIREITO', 'name': 'Total de Dias de Direito'},
                {'value':'PERCENT_DEFLATOR', 'name': '% Deflator Total'},
                {'value':'PERCENT_FINAL', 'name': '% Final'},
                {'value':'VALOR_BASE', 'name': 'Valor Base'},
                {'value':'VALOR_A_RECEBER', 'name': 'Valor a Receber'},
                {'value':'OBS', 'name': 'Observação'},
                {'value':'DATA_CRIACAO', 'name': 'Data da Criação'},
                {'value':'CHAPA_REQUISITOR', 'name': 'Chapa Usuário Solicitante'},
                {'value':'NOME_REQUISITOR', 'name': 'Nome do Usuário Solicitante'},
                {'value':'DATA_APROV_REPROV', 'name': 'Data de Aprovação/Reprovação'},
                {'value':'CHAPA_APROV_REPROV', 'name': 'Chapa do Aprovador/Reprovador'},
                {'value':'NOME_APROV_REPROV', 'name': 'Nome do Aprovador/Reprovador'},
                {'value':'MOTIVO_REPROV', 'name': 'Motivo Reprovação'},
                {'value':'DATA_RH_APROV_REPROV', 'name': 'Data de Aprovação/Reprovação RH'},
                {'value':'CHAPA_RH_APROV_REPROV', 'name': 'Chapa do RH Aprovador/Reprovador'},
                {'value':'NOME_RH_APROV_REPROV', 'name': 'Nome do RH Aprovador/Reprovador'},
                {'value':'DATA_SINCRONISMO', 'name': 'Data de Sincronismo'},
                {'value':'EVENTO_SINCRONISMO', 'name': 'Evento de Sincronismo'},
                {'value':'PERIODO_SINCRONISMO', 'name': 'Período de Sincronismo'},
                {'value':'MES_COMPETENCIA', 'name': 'Mês de competência'},
                {'value':'ANO_COMPETENCIA', 'name': 'Ano de competência'},
                {'value':'ID_REQUISICAO', 'name': 'Número da Requisição'},
                {'value':'CHAPA_APROVADOR', 'name': 'Chapa do Aprovador'},
                {'value':'NOME_APROVADOR', 'name': 'Nome do Aprovador'},
                {'value':'CHAPA_GESTOR', 'name': 'Chapa do Gestor'},
                {'value':'NOME_GESTOR', 'name': 'Nome do Gestor'}
            ];
        break;

        case '27':
            colunas = [
                {'value':'NOME_PREMIO', 'name': 'Prêmio'},
                {'value':'TIPO_REQUISICAO', 'name': 'Tipo de Prêmio'},
                {'value':'DT_INICIO_PONTO', 'name': 'Início do Ponto'},
                {'value':'DT_FIM_PONTO', 'name': 'Fim do Ponto'},
                {'value':'CODFILIAL_COLAB', 'name': 'Filial'},
                {'value':'CHAPA_COLAB', 'name': 'Chapa Colaborador'},
                {'value':'NOME_COLAB', 'name': 'Nome Colaborador'},
                {'value':'FUNCAO_COLAB', 'name': 'Função'},
                {'value':'DT_ADMISSAO_COLAB', 'name': 'Data de Admissão'},
                {'value':'CCUSTO_COLAB', 'name': 'Centro de Custo'},
                {'value':'CODSECAO_COLAB', 'name': 'Codigo da Seção'},
                {'value':'SECAO_COLAB', 'name': 'Seção'},
                {'value':'CODSITUACAO_COLAB', 'name': 'Situação'},
                {'value':'DIAS_PERDIDOS', 'name': 'Total de Dias Perdidos'},
                {'value':'PERCENT_FINAL', 'name': '% Final'},
                {'value':'VALOR_A_RECEBER', 'name': 'Valor a Receber'},
                {'value':'DIAS_PERDIDOS_MES_ANT', 'name': 'Total de Dias Perdidos do mês anterior'},
                {'value':'PERCENT_FINAL_MES_ANT', 'name': '% Final do mês anterior'},
                {'value':'VALOR_A_RECEBER_MES_ANT', 'name': 'Valor a Receber do mês anterior'},
                {'value':'PERCENT_VARIACAO', 'name': '% de variação entre R$ do mês anterior x R$ do mês atual'}
            ];
        break;

        case '28':
            colunas = [
                {'value':'NOME_PREMIO', 'name': 'Prêmio'},
                {'value':'TIPO_REQUISICAO', 'name': 'Tipo de Prêmio'},
                {'value':'DT_INICIO_PONTO', 'name': 'Início do Ponto'},
                {'value':'DT_FIM_PONTO', 'name': 'Fim do Ponto'},
                {'value':'CODFILIAL_COLAB', 'name': 'Filial'},
                {'value':'CHAPA_COLAB', 'name': 'Chapa Colaborador'},
                {'value':'NOME_COLAB', 'name': 'Nome Colaborador'},
                {'value':'FUNCAO_COLAB', 'name': 'Função'},
                {'value':'DT_ADMISSAO_COLAB', 'name': 'Data de Admissão'},
                {'value':'CCUSTO_COLAB', 'name': 'Centro de Custo'},
                {'value':'CODSECAO_COLAB', 'name': 'Codigo da Seção'},
                {'value':'SECAO_COLAB', 'name': 'Seção'},
                {'value':'CODSITUACAO_COLAB', 'name': 'Situação'},
                {'value':'DIAS_FALTAS', 'name': 'Faltas'},              
                {'value':'DATAS_FALTAS', 'name': 'Datas das Faltas'},
                {'value':'DIAS_AFAST', 'name': 'Dias de Afastamento'},
                {'value':'DATAS_AFAST', 'name': 'Datas de Afastamento'},
                {'value':'DIAS_ATESTADO', 'name': 'Dias de Atestado'},              
                {'value':'DATAS_ATESTADO', 'name': 'Datas de Atestado'},
                {'value':'DIAS_FERIAS', 'name': 'Dias de Férias'},
                {'value':'DATAS_FERIAS', 'name': 'Datas de Férias'},
                {'value':'DIAS_ADMISSAO', 'name': 'Dias até Admissão'},
                {'value':'DATAS_ADMISSAO', 'name': 'Datas até Admissão'},
                {'value':'DIAS_DEMISSAO', 'name': 'Dias até Demissão'},
                {'value':'DATAS_DEMISSAO', 'name': 'Datas até Demissão'},
                {'value':'DIAS_DEFLATORES', 'name': 'Total de Dias Deflatores'},
                {'value':'DIAS_DE_DIREITO', 'name': 'Total de Dias de Direito'}
            ];
        break;
        case '29':
            colunas = [
                {'value': 'ID', 'name': 'ID'},
                {'value': 'COD_COLIGADA', 'name': 'Cod. Coligada'},
                {'value': 'COD_FILIAL', 'name': 'Cód. Filial'},
                {'value': 'CHAPA', 'name': 'Chapa'},
                {'value': 'NOME', 'name': 'Nome'},
                {'value': 'SITUACAO', 'name': 'Situação'},
                {'value': 'COD_FUNCAO', 'name': 'Cód. Função'},
                {'value': 'FUNCAO', 'name': 'Função'},
                {'value': 'COD_SECAO', 'name': 'Cod. Secao'},
                {'value': 'SECAO', 'name': 'Seção'},
                {'value': 'CODIGO_CENTRO_CUSTO', 'name': 'Cod. Centro de Custo'},
                {'value': 'CENTRO_CUSTO', 'name': 'Centro de Custo'},
                {'value': 'PRODUTO', 'name': 'Produto'},
                {'value': 'PROCESSO', 'name': 'Processo'},
                {'value': 'STATUS', 'name': 'Status'},
                {'value': 'DATA_SOLICITACAO', 'name': 'Data solicitação'},
                {'value': 'USUARIO_SOLICITANTE', 'name': 'Usuário solicitação'},
                {'value': 'DATA_APROVACAO_REPROVACAO', 'name': 'Data Aprovação/Reprovação'},
                {'value': 'JUSTIFICATIVA', 'name': 'Justificativa'},
                {'value': 'USUARIO_GESTOR', 'name': 'Usuário Gestor'},
                {'value': 'USUARIO_RH', 'name': 'Usuário RH'},
            ]
            
        break;
        case '30':
                colunas = [
                    {'value': '[ID]', 'name': 'ID'},
                    {'value': '[TIPO]', 'name': 'Tipo'},
                    {'value': '[STATUS]', 'name': 'Status'},
                    {'value': '[COD.COLIGADA]', 'name': 'Cód. Coligada'},
                    {'value': '[COD.FILIAL]', 'name': 'Cód. Filial'},
                    {'value': '[CHAPA]', 'name': 'Chapa'},
                    {'value': '[NOME]', 'name': 'Nome'},
                    {'value': '[COD.FUNÇÃO]', 'name': 'Cód. Função'},
                    {'value': '[FUNÇÃO]', 'name': 'Função'},
                    {'value': '[DT ADMISSÃO]', 'name': 'Data Admissão'},
                    {'value': '[SITUAÇÃO]', 'name': 'Situação'},
                    {'value': '[COD.CUSTO]', 'name': 'Cód. Custo'},
                    {'value': '[DESC.CUSTO]', 'name': 'Custo'},
                    {'value': '[COD.SEÇÃO]', 'name': 'Cód. Seção'},
                    {'value': '[DESC.SEÇÃO]', 'name': 'Seção'},
                    {'value': '[DATA]', 'name': 'Data'},
                    {'value': '[ANTES - TROCA]', 'name': 'Antes Troca'},
                    {'value': '[APÓS - TROCA]', 'name': 'Após Troca'},
                    {'value': '[ANTES - FOLGA]', 'name': 'Antes Troca Folga'},
                    {'value': '[APÓS - FOLGA]', 'name': 'Após Troca Folga'},
                    {'value': '[DATA SOLICITAÇÃO]', 'name': 'Data Solicitação'},
                    {'value': '[USUÁRIO - SOLICITANTE]', 'name': 'Solicitante'},
                    {'value': '[JUSTIFICATIVA SOLICITAÇÃO]', 'name': 'Justificativa Solicitação'},
                    {'value': '[DATA APROVAÇÃO]', 'name': 'Data Aprovação'},
                    {'value': '[DATA REPROVAÇÃO]', 'name': 'Data Reprovação'},
                    {'value': '[JUSTIFICATIVA REPROVAÇÃO]', 'name': 'Justificativa Reprovação'},
                    {'value': '[USUÁRIO - GESTOR]', 'name': 'Aprovador'},
                    {'value': '[USUÁRIO - RH]', 'name': 'Aprovador RH'},
                ]
                
        break;
        case '40':
                colunas = [
                    {'value': '[ID_REQ]', 'name': 'ID da Requisição'},
                    {'value': '[ID_REQ_ORIGINAL]', 'name': 'ID Original da Requisição'},
                    {'value': '[STATUS_REQ]', 'name': 'Status Requisição'},
                    {'value': '[CODCOLIGADA]', 'name': 'Cód. Coligada'},
                    {'value': '[CODFILIAL]', 'name': 'Cód. Filial'},
                    {'value': '[DATA_REQ]', 'name': 'Data da Requisição'},
                    {'value': '[ID_REQ_CHAPA]', 'name': 'ID da Chapa na Requisição'},
                    {'value': '[STATUS_COLAB]', 'name': 'Status do Colaborador na Requisição'},
                    {'value': '[CHAPA_COLAB]', 'name': 'Chapa do Colaborador'},
                    {'value': '[NOME_COLAB]', 'name': 'Nome do Colaborador'},
                    {'value': '[CODSITUACAO_COLAB]', 'name': 'Cód. Situação Colaborador'},
                    {'value': '[CODFUNCAO_COLAB]', 'name': 'Cód. Função'},
                    {'value': '[FUNCAO_COLAB]', 'name': 'Função do Colaborador'},
                    {'value': '[CENTRO_DE_CUSTO]', 'name': 'Centro de Custo'},
                    {'value': '[DESC_CCUSTO]', 'name': 'Desc. Centro de Custo'},
                    {'value': '[CODSECAO_COLAB]', 'name': 'Cód. Seção'},
                    {'value': '[DESC_SECAO]', 'name': 'Seção do Colaborador'},
                    {'value': '[AREA]', 'name': 'Área'},
                    {'value': '[DIRETORIA]', 'name': 'Diretoria'},
                    {'value': '[CHAPA_GESTOR]', 'name': 'Chapa do Gestor'},
                    {'value': '[NOME_GESTOR]', 'name': 'Nome do Gestor'},
                    {'value': '[CHAPA_REQUISITOR]', 'name': 'Chapa do Requisitor'},
                    {'value': '[NOME_REQUISITOR]', 'name': 'Nome do Requisitor'},
                    {'value': '[ID_JUSTIFICATIVA]', 'name': 'ID Justificativa'},
                    {'value': '[DESC_JUSTIFICATIVA]', 'name': 'Desc. Justificativa'},
                    {'value': '[OBSERVACAO]', 'name': 'Observação'},
                    {'value': '[CHAPA_APROV_REPROV]', 'name': 'Chapa do Aprovador ou Reprovador'},
                    {'value': '[NOME_APROV_REPROV]', 'name': 'Nome do Aprovador ou Reprovador'},
                    {'value': '[CHAPA_RH_APROV_REPROV]', 'name': 'Chapa do RH Aprovador ou Reprovador'},
                    {'value': '[NOME_RH_APROV_REPROV]', 'name': 'Nome do RH Aprovador ou Reprovador'},
                    {'value': '[DATA_APROV_REPROV]', 'name': 'Data da Aprovação ou Reprovação'},
                    {'value': '[JUSTIFICATIVA_REPROVACAO]', 'name': 'Justificativa da Reprovação'},
                    {'value': '[DATA_INICIO_PONTO]', 'name': 'Data Inicial do Ponto'},
                    {'value': '[DATA_FIM_PONTO]', 'name': 'Data Final do Ponto'},
                    {'value': '[DATA_PONTO]', 'name': 'Data do Ponto'},
                    {'value': '[CODHORARIO]', 'name': 'Código do Horario'},
                    {'value': '[INDICE]', 'name': 'Indíce'},
                    {'value': '[HORAS_EXTRAS_ORIGINAIS]', 'name': 'Horas Extras Originais'},
                    {'value': '[CODEVENTO_ORIGINAL]', 'name': 'Código do Evento Original'},
                    {'value': '[HORAS_EXTRAS_NORMAIS]', 'name': 'Horas Extras Normais'},
                    {'value': '[CODEVENTO_ART61]', 'name': 'Código do Evento Artigo.61'},
                    {'value': '[HORAS_EXTRAS_ART61]', 'name': 'Horas Extras Artigo.61'},
                    {'value': '[TOT_EVENTO_PERIODO]', 'name': 'Total de Horas do Evento no Período'},
                ]
                
        break;
        case '41':
                colunas = [
                    {'value': '[COLIGADA]', 'name': 'Cód. Coligada'},
                    {'value': '[CHAPA_GESTOR]', 'name': 'Chapa do Gestor'},
                    {'value': '[NOME_GESTOR]', 'name': 'Nome do Gestor'},
                    {'value': '[FILIAL_GESTOR]', 'name': 'Filial do Gestor'},
                    {'value': '[CODFUNCAO_GESTOR]', 'name': 'Código da Função do Gestor'},
                    {'value': '[FUNCAO_GESTOR]', 'name': 'Função do Gestor'},
                    {'value': '[CODSECAO_GESTOR]', 'name': 'Código da Seção do Gestor'},
                    {'value': '[SECAO_GESTOR]', 'name': 'Seção do Gestor'},
                    {'value': '[CENTRO_DE_CUSTO_GESTOR]', 'name': 'Centro de Custo do Gestor'},
                    {'value': '[NOME_CCUSTO_GESTOR]', 'name': 'Nome do Centro de Custo do Gestor'},
                    {'value': '[AREA_GESTOR]', 'name': 'Área do Gestor'},
                    {'value': '[DIRETORIA_GESTOR]', 'name': 'Diretoria do Gestor'},
                    {'value': '[CHAPA_SUBSTITUTO]', 'name': 'Chapa do Gestor Substituto'},
                    {'value': '[NOME_SUBSTITUTO]', 'name': 'Nome do Gestor Substituto'},
                    {'value': '[DATA_INICIAL]', 'name': 'Data Inicial da Substituição'},
                    {'value': '[DATA_FINAL]', 'name': 'Data Final da Substituição'},
                    {'value': '[CODIGOS_MODULOS]', 'name': 'Códigos dos Módulos'},
                    {'value': '[NOMES_MODULOS]', 'name': 'Nomes do Módulos'},
                ]
                
        break;
        case '42':
                colunas = [
                    {'value': '[COLIGADA]', 'name': 'Cód. Coligada'},
                    {'value': '[CHAPA]', 'name': 'Chapa do Colaborador'},
                    {'value': '[NOME]', 'name': 'Nome'},
                    {'value': '[FILIAL]', 'name': 'Filial'},
                    {'value': '[CODFUNCAO]', 'name': 'Código da Função'},
                    {'value': '[FUNCAO]', 'name': 'Função'},
                    {'value': '[CODSECAO]', 'name': 'Código da Seção'},
                    {'value': '[SECAO]', 'name': 'Seção'},
                    {'value': '[CENTRO_DE_CUSTO]', 'name': 'Centro de Custo'},
                    {'value': '[NOME_CCUSTO]', 'name': 'Nome do Centro de Custo'},
                    {'value': '[AREA]', 'name': 'Área'},
                    {'value': '[DIRETORIA]', 'name': 'Diretoria'},
                    {'value': '[DATA_ALTERACAO]', 'name': 'Data da Mudança'},
                    {'value': '[CODHORARIO_ANTERIOR]', 'name': 'Código do Horário Anterior'},
                    {'value': '[HORARIO_ANTERIOR]', 'name': 'Horário Anterior'},
                    {'value': '[CODHORARIO_ATUAL]', 'name': 'Código do Horário Atual'},
                    {'value': '[HORARIO_ATUAL]', 'name': 'Horário Atual'},
                    {'value': '[COD_SINDICATO_ATUAL]', 'name': 'Código do Sindicato Atual'},
                    {'value': '[SINDICATO_ATUAL]', 'name': 'Sindicato Atual'},
                ]
                
        break;
        case '43':
                colunas = [
                    {'value': '[CODCOLIGADA]', 'name': 'Cód. Coligada'},
                    {'value': '[CHAPA]', 'name': 'Chapa do Colaborador'},
                    {'value': '[NOME]', 'name': 'Nome'},
                    {'value': '[FILIAL]', 'name': 'Filial'},
                    {'value': '[CODFUNCAO]', 'name': 'Código da Função'},
                    {'value': '[FUNCAO]', 'name': 'Função'},
                    {'value': '[CODSECAO]', 'name': 'Código da Seção'},
                    {'value': '[SECAO]', 'name': 'Seção'},
                    {'value': '[CENTRO_DE_CUSTO]', 'name': 'Centro de Custo'},
                    {'value': '[NOME_CCUSTO]', 'name': 'Nome do Centro de Custo'},
                    {'value': '[INICIO_FALTAS]', 'name': 'Data de Início das Faltas'},
                    {'value': '[FALTAS_CONSECUTIVAS]', 'name': 'Faltas Consecutivas'},
                    {'value': '[STATUS]', 'name': 'Status do Workflow'},
                    {'value': '[DATA_ENVIO_GESTOR1]', 'name': 'Envio para Gestor 1'},
                    {'value': '[CHAPA_GESTOR1]', 'name': 'Chapa do Gestor 1'},
                    {'value': '[NOME_GESTOR1]', 'name': 'Nome do Gestor 1'},
                    {'value': '[CHAPA_SUB_GESTOR1]', 'name': 'Chapa do Substituto do Gestor 1'},
                    {'value': '[NOME_SUB_GESTOR1]', 'name': 'Nome do Substituto do Gestor 1'},
                    {'value': '[DATA_ENVIO_GESTOR2]', 'name': 'Envio para Gestor 2'},
                    {'value': '[CHAPA_GESTOR2]', 'name': 'Chapa do Gestor 2'},
                    {'value': '[NOME_GESTOR2]', 'name': 'Nome do Gestor 2'},
                    {'value': '[CHAPA_SUB_GESTOR2]', 'name': 'Chapa do Substituto do Gestor 2'},
                    {'value': '[NOME_SUB_GESTOR2]', 'name': 'Nome do Substituto do Gestor 2'},
                    {'value': '[DATA_CONFIRMACAO]', 'name': 'Data que foi Confirmado o Envio'},
                    {'value': '[CHAPA_CONFIRMOU]', 'name': 'Chapa que Confirmou o Envio'},
                    {'value': '[DATA_RECUSA]', 'name': 'Data que foi Recusado o Envio'},
                    {'value': '[CHAPA_RECUSOU]', 'name': 'Chapa que Recusou o Envio'},
                    {'value': '[MOTIVO_RECUSA]', 'name': 'Motivo que foi Recusado o Envio'}
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


    let rel = $('#relatorio').val();
    let dtfim = $('#dataFim').val();
    let dtini = $('#dataIni').val();

    if(rel == 2 && (dtfim.length < 1  || dtini.length < 1)){ exibeAlerta('warning', 'É necessário selecionar uma data inicio e fim para filtrar chapas nesse relatório.'); return false; }

    if(keyword.length < 4){ exibeAlerta('warning', 'Digite pelo menos 4 caracteres para pesquisar.'); return false; }

    openLoading();
    
    $("select[name=chapa]").html('');

    $.ajax({
        url: base_url + '/relatorio/gerar/action/lista_funcionarios',
        type: 'POST',
        data: {
            'keyword'  : keyword,
            'rel'      : rel,
            'dtfim'    : dtfim
        },
        success: function(result) {

            openLoading(true);

            try {
                var response = JSON.parse(result);

                $("select[name=chapa]").append('<option value="">Selecione o Colaborador ('+response.length+')</option>');
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
    if((relatorio == 13 || relatorio == 16 || relatorio == 26 || relatorio == 27 || relatorio == 28 || relatorio == 40 || relatorio == 42 || relatorio == 43) && dataI == "" && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }

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
    if((relatorio == 13 || relatorio == 16 || relatorio == 26 || relatorio == 27 || relatorio == 28 || relatorio == 40 || relatorio == 42 || relatorio == 43) && dataI == "" && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    
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
    if((relatorio == 13 || relatorio == 16 || relatorio == 26 || relatorio == 27 || relatorio == 28 || relatorio == 40 || relatorio == 42 || relatorio == 43) && dataI == "" && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }

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
    if((relatorio == 13 || relatorio == 16 || relatorio == 26 || relatorio == 27 || relatorio == 28) && dataI == "" && dataF == ""){ exibeAlerta('warning', 'Selecione as datas necessárias.'); return false; }
    
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