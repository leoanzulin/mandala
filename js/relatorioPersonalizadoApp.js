
var app = angular.module("relatorioPersonalizadoApp", []);

app.controller('controlador', ['$scope',
    function ($scope) {

        $scope.statusInscricao = ['inscrito', 'matriculado'];
        $scope.statusAluno = ['ativo', 'cancelado', 'trancado'];
        $scope.conjuntoDeCamposSelecionado = 'default';
        $scope.conjuntosDeCampos = ['default', 'todos'];

        $scope.conjuntosDeCamposColunas = {
            'default': ['cpf', 'nome', 'sobrenome', 'email', 'telefone_fixo', 'telefone_celular', 'telefone_alternativo', 'cidade', 'estado', 'habilitacao1', 'habilitacao2', 'candidato_a_bolsa', 'modalidade', 'recebe_bolsa', 'data_matricula', 'status_aluno'],
            'todos': ['cpf', 'nome', 'sobrenome', 'sexo', 'email', 'data_nascimento', 'naturalidade', 'nome_mae', 'nome_pai', 'estado_civil', 'telefone_fixo', 'telefone_celular', 'telefone_alternativo', 'cep', 'endereco', 'numero', 'complemento', 'cidade', 'estado', 'cargo_atual', 'empresa', 'telefone_comercial', 'whatsapp', 'skype', 'tipo_identidade', 'identidade', 'orgao_expedidor', 'habilitacao1', 'habilitacao2', 'candidato_a_bolsa', 'modalidade', 'comentarios', 'recebe_bolsa', 'observacoes', 'data_matricula', 'status_aluno']
        };

        $scope.campos = {
            'cpf': {'label': 'CPF'},
            'nome': {'label': 'Nome'},
            'sobrenome': {'label': 'Sobrenome'},
            'sexo': {'label': 'Sexo'},
            'email': {'label': 'E-mail'},
            'data_nascimento': {'label': 'Data de nascimento'},
            'naturalidade': {'label': 'Naturalidade'},
            'nome_mae': {'label': 'Nome da mãe'},
            'nome_pai': {'label': 'Nome do pai'},
            'estado_civil': {'label': 'Estado civil'},
            'telefone_fixo': {'label': 'Telefone fixo'},
            'telefone_celular': {'label': 'Telefone celular'},
            'telefone_alternativo': {'label': 'Telefone alternativo'},
            'cep': {'label': 'CEP'},
            'endereco': {'label': 'Endereço'},
            'numero': {'label': 'Número'},
            'complemento': {'label': 'Complemento'},
            'cidade': {'label': 'Cidade'},
            'estado': {'label': 'Estado'},
            'cargo_atual': {'label': 'Cargo atual'},
            'empresa': {'label': 'Empresa'},
            'telefone_comercial': {'label': 'Telefone comercial'},
//            'status': {'label': 'Status'},
//            'documento_cpf': {'label': 'CPF (documento)'},
//            'documento_rg': {'label': 'RG (documento)'},
//            'documento_diploma': {'label': 'Diploma (documento)'},
//            'documento_comprovante_residencia': {'label': 'Comprovante de residência (documento)'},
//            'documento_curriculo': {'label': 'Currículo (documento)'},
//            'documento_justificativa': {'label': 'Justificativa de bolsa (documento)'},
            'whatsapp': {'label': 'Whatsapp'},
            'skype': {'label': 'Skype'},
            'tipo_identidade': {'label': 'Tipo de identidade'},
            'identidade': {'label': 'Identidade'},
            'orgao_expedidor': {'label': 'Órgão expedidor'},
            'habilitacao1': {'label': 'Habilitação 1'},
            'habilitacao2': {'label': 'Habilitação 2'},
            'candidato_a_bolsa': {'label': 'Candidato à bolsa'},
            'modalidade': {'label': 'Modalidade'},
            'comentarios': {'label': 'Comentários'},
            'recebe_bolsa': {'label': 'Recebe bolsa?'},
            'observacoes': {'label': 'Observações'},
            'data_matricula': {'label': 'Data de matrícula'},
            'status_aluno': {'label': 'Status do aluno'}
//            'turma': {'label': 'Turma'}
        };

        $scope.atualizarCampos = function () {
            for (var campo in $scope.campos) {
                $scope.campos[campo].selecionado = false;
            }
            for (var campo in $scope.conjuntosDeCamposColunas[$scope.conjuntoDeCamposSelecionado]) {
                var c = $scope.conjuntosDeCamposColunas[$scope.conjuntoDeCamposSelecionado][campo];
                $scope.campos[c].selecionado = true;
            }
        };

        $scope.selecionar = function (campo) {
            $scope.campos[campo].selecionado = !$scope.campos[campo].selecionado;
        };

        $scope.gerar = function () {
            var parametros = construirParametrosUrl();
            var urlAtual = window.location.href;
            var urlExportador = urlAtual.replace(/admin\/relatorios/, 'exportador\/relatorioPersonalizado&' + parametros);
            window.location.href = urlExportador;
        };

        function construirParametrosUrl() {
            var url = 'parametros=';
            var virgula = '%2C';
            var camposSelecionados = [];
            for (var campo in $scope.campos) {
                if ($scope.campos[campo].selecionado) {
                    camposSelecionados.push(campo);
                }
            }
            return url + camposSelecionados.join(virgula);
        }

        $scope.atualizarCampos();

    }]);

/*
 Status da inscrição: [inscrito] [matriculado]
 Status do aluno: [ativo] [cancelado] [trancado]
 * 
 */
