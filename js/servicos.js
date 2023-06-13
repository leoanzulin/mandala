angular.module('servicos', ['ngResource']);

angular.module('servicos').factory("Componente", ["$resource", function ($resource) {
        return $resource("index.php?r=componenteCurricular/get&id=:id", {
            id: "@id"
        }, {
            query: {
                url: "index.php?r=componenteCurricular/todos",
                method: "GET",
                isArray: true,
            },
        });
    }]);

angular.module('servicos').factory("Habilitacao", ["$resource", function ($resource) {
        return $resource("index.php?r=habilitacao/get&cpf=:cpf", {
            cpf: "@cpf"
        }, {
            get: {
                isArray: true,
            },
            daInscricao: {
                url: "index.php?r=habilitacao/daInscricao&id=:id",
                isArray: true,
            }
        });
    }]);

angular.module('servicos').factory("Oferta", ["$resource", function ($resource) {
        return $resource("index.php?r=oferta/get&id=:id", {
            id: "@id"
        }, {
            query: {
                url: "index.php?r=oferta/todos",
                method: "GET",
                isArray: true,
            },
            porPeriodo: {
                url: "index.php?r=oferta/porPeriodo",
                method: "GET",
                isArray: true,
            },
            porPeriodoDoUsuario: {
                url: "index.php?r=oferta/porPeriodo&inscricaoId=:inscricaoId",
                params: {
                    inscricaoId: '@inscricaoId'
                },
                method: "GET",
                isArray: true,
            },
            todasPorTurmaEPeriodo: {
                url: "index.php?r=oferta/todasPorTurmaEPeriodo",
                method: "GET",
                isArray: true,
            },
            todasPorTurmaEPeriodoComInscricoes: {
                url: "index.php?r=oferta/todasPorTurmaEPeriodo&comInscricoes=1",
                method: "GET",
                isArray: true,
            },
            alterarPeriodo: {
                url: "index.php?r=oferta/alterarPeriodo&id=:id&ano=:ano&mes=:mes",
                params: {
                    id: '@id',
                    ano: '@ano',
                    mes: '@mes'
                },
                method: "GET",
            }
        });
    }]);

angular.module('servicos').factory("Docente", ["$resource", function ($resource) {
        return $resource("index.php?r=docente/get&id=:id", {
            id: "@id"
        }, {
            query: {
                url: "index.php?r=docente/todos",
                method: "GET",
                isArray: true,
            },
        });
    }]);

angular.module('servicos').factory("Tutor", ["$resource", function ($resource) {
        return $resource("index.php?r=tutor/get&id=:id", {
            id: "@id"
        }, {
            query: {
                url: "index.php?r=tutor/todos",
                method: "GET",
                isArray: true,
            },
        });
    }]);

angular.module('servicos').factory("Formacao", ["$resource", function ($resource) {
        return $resource("index.php?r=formacao/get&id=:id", {
            id: "@id"
        }, {
            get: {
                isArray: true,
            }
        });
    }]);

angular.module('servicos').factory("Bolsa", ["$resource", function ($resource) {
        return $resource("index.php?r=bolsa/get&id=:id", {
            id: "@id"
        }, {
            query: {
                url: "index.php?r=bolsa/todas",
                method: "GET",
                isArray: true,
            },
            proximoId: {
                url: "index.php?r=bolsa/proximoId",
                method: "GET",
                isArray: true,
            }
        });
    }]);

angular.module('servicos').factory("Aluno", ["$resource", function ($resource) {
        return $resource("index.php?r=aluno/get&id=:id", {
            id: "@id"
        }, {
            get: {
                isArray: true,
            },
            recuperarAlunos: {
                url: "index.php?r=aluno/recuperarAlunos",
                method: "GET",
                isArray: true,
            },
            recuperarAlunosPagamento: {
                url: "index.php?r=aluno/recuperarAlunosPagamento",
                method: "GET",
                isArray: true,
            }
        });
    }]);

angular.module('servicos').factory("Constantes", ["$resource", function ($resource) {
        return $resource("index.php?r=constantes&id=:id", {
        }, {
            todas: {
                url: "index.php?r=constantes",
                method: "GET",
            }
        });
    }]);
