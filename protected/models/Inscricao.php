<?php

/**
 * This is the model class for table "inscricao".
 *
 * The followings are the available columns in table 'inscricao':
 * @property string $id
 * @property string $cpf
 * @property string $ra
 * @property string $numero_ufscar
 * @property string $nome
 * @property string $sobrenome
 * @property string $sexo
 * @property string $email
 * @property string $data_nascimento
 * @property string $naturalidade
 * @property string $nome_mae
 * @property string $nome_pai
 * @property string $estado_civil
 * @property string $telefone_fixo
 * @property string $telefone_celular
 * @property string $telefone_alternativo
 * @property string $cep
 * @property string $endereco
 * @property string $numero
 * @property string $complemento
 * @property string $cidade
 * @property string $estado
 * @property string $cargo_atual
 * @property string $empresa
 * @property string $telefone_comercial
 * 
 * @property string $documento_cpf
 * @property string $documento_rg
 * @property string $documento_diploma
 * @property string $documento_comprovante_residencia
 * @property string $documento_curriculo
 * 
 * @property string $whatsapp
 * @property string $skype
 * @property string $tipo_identidade
 * @property string $identidade
 * @property string $orgao_expedidor
 * @property string $data_matricula
 * 
 * @property string $comentarios
 * @property string $modalidade Distância, presencial ou misto
 * @property boolean $candidato_a_bolsa
 * @property boolean $recebe_bolsa Se o candidato recebe bolsa de fato
 * @property string $observacoes Pode ser usado para dizer de quantos % a bolsa é
 * @property string $tipo_bolsa Tipo de bolsa (substitui o campo observacoes)
 * 
 * @property integer $status
 * @property string $curso_id
 * @property string $status_aluno
 * @property integer $tipo_curso
 * 
 * @property string $data_conclusao
 * @property string $processo_proex
 *
 * The followings are the available model relations:
 * @property Habilitacao[] $habilitacoes
 * @property Curso $curso
 * @property Formacao[] $formacoes
 * @property Ofertas[] $ofertas
 * @property Inscricao[] $inscricoes
 * @property PagamentoAluno[] $pagamentos
 */
class Inscricao extends ActiveRecord
{

    // Status possíveis para as inscrições
    const STATUS_PENDENTE = 0;
    const STATUS_DOCUMENTOS_SENDO_ANALISADOS = 1;
    const STATUS_DOCUMENTOS_VERIFICADOS = 2;
    const STATUS_MATRICULADO = 3;
    // Status de aluno
    const STATUS_ALUNO_ATIVO = 'Ativo';
    const STATUS_ALUNO_FORMADO = 'Formado';
    const STATUS_ALUNO_DESISTENTE = 'Desistente';
    // Tipos de curso possíveis
    const TIPO_CURSO_EXTENSAO = 0;
    const TIPO_CURSO_APERFEICOAMENTO = 1;
    const TIPO_CURSO_ESPECIALIZACAO = 2;
    const TIPO_CURSO_EXTENSAO_STRING = 'Extensão';
    const TIPO_CURSO_APERFEICOAMENTO_STRING = 'Aperfeiçoamento';
    const TIPO_CURSO_ESPECIALIZACAO_STRING = 'Especialização';
    // Valores default
    public $curso_id = 1;
    public $modalidade = 'distancia';
    public $candidato_a_bolsa = 'nao';
    public $tipo_curso = self::TIPO_CURSO_ESPECIALIZACAO;
    // Campos extras
    public $confirmarEmail = '';
    public $formacao = array();
    public $habilitacoesEscolhidas = array();
    public $pagou_inscricao = false;
    public $pagou_matricula = false;
    public $candidato_a_bolsa_search;
    public $recebe_bolsa_search;
    public $cpf_search;
    public $tcc_search;

    public function tableName()
    {
        return 'inscricao';
    }

    public function rules()
    {
        return array(
            array('candidato_a_bolsa, recebe_bolsa', 'default', 'setOnEmpty' => true, 'value' => false),
            array('cpf, nome, sobrenome, sexo, email, confirmarEmail, data_nascimento, naturalidade, estado_civil, cep, endereco, numero, cidade, estado, curso_id, tipo_identidade, identidade, orgao_expedidor, telefone_celular, modalidade, candidato_a_bolsa, tipo_curso', 'required', 'on' => 'insert'),
            array('cpf, nome, sobrenome, sexo, email,                 data_nascimento, naturalidade, estado_civil, cep, endereco, numero, cidade, estado, curso_id, tipo_identidade, identidade, orgao_expedidor, telefone_celular, modalidade, candidato_a_bolsa, tipo_curso', 'required', 'on' => 'update'),
            array('status, turma, tipo_curso', 'numerical', 'integerOnly' => true),
            array('candidato_a_bolsa', 'boolean'),
            array('comentarios, observacoes', 'length', 'max' => 10485760),
            array('nome, sobrenome, email, confirmarEmail, naturalidade, nome_mae, nome_pai, estado_civil, endereco, complemento, cidade, cargo_atual, empresa, skype, tipo_identidade, identidade, orgao_expedidor, modalidade, data_matricula, data_conclusao, processo_proex', 'length', 'max' => 256),
            array('cpf, telefone_fixo, telefone_celular, telefone_alternativo, numero, telefone_comercial', 'length', 'max' => 30),
            array('whatsapp, ra, numero_ufscar', 'length', 'max' => 50),
            array('cep', 'length', 'max' => 8),
            array('estado', 'length', 'max' => 2),
            array('sexo', 'length', 'max' => 1),
            array('formacao, cargo_atual, empresa, status_aluno, tipo_bolsa', 'safe'),
            array('cpf', 'validadorCpf'),
            array('email, confirmarEmail', 'email'),
            array('confirmarEmail', 'compare', 'compareAttribute' => 'email', 'on' => 'insert'),
            array('formacao', 'peloMenosUmaFormacao', 'on' => 'insert'),
            //
            array('documento_cpf, documento_rg, documento_diploma, documento_comprovante_residencia',
                'file',
                'types' => 'pdf, jpg, jpeg, gif, png',
                'maxSize' => 1024 * 1024 * 2,
                'tooLarge' => 'Arquivo deve ter menos de 2MB',
                'safe' => false,
                'on' => 'insert'),
            array('documento_curriculo',
                'file',
                'types' => 'pdf, jpg, jpeg, gif, png, doc, docx',
                'maxSize' => 1024 * 1024 * 2,
                'tooLarge' => 'Arquivo deve ter menos de 2MB',
                'safe' => false,
                'on' => 'insert'),
            array('documento_justificativa',
                'file',
                'types' => 'pdf, jpg, jpeg, gif, png, doc, docx, zip',
                'maxSize' => 1024 * 1024 * 5,
                'tooLarge' => 'Arquivo deve ter menos de 5MB',
                'safe' => false,
                'allowEmpty' => true),
            //
            array('pagou_inscricao, pagou_matricula', 'boolean'),
            array('email', 'multipleUnique', 'on' => 'insert'),
            array('id, cpf, nome, sobrenome, email, data_nascimento, nome_mae, nome_pai, estado_civil, telefone_fixo, telefone_celular, telefone_alternativo, cep, endereco, numero, complemento, cidade, estado, cargo_atual, empresa, telefone_comercial, status, curso_id, candidato_a_bolsa_search, recebe_bolsa_search, cpf_search, data_matricula, data_inscricao', 'safe', 'on' => 'search'),
        );
    }

    /**
     * Validador que verifica se o usuário forneceu pelo menos uma formação
     * acadêmica.
     */
    public function peloMenosUmaFormacao($attribute, $params)
    {
        if (count($this->$attribute) < 1) {
            $this->addError($attribute, 'Informe pelo menos uma formação acadêmica');
        }
    }

    /**
     * Verifica se o e-mail já foi cadastrado e, se foi, se pertence ao mesmo CPF da inscrição anterior
     */
    public function multipleUnique($attribute, $params)
    {
        $model = Inscricao::model()->findByAttributes(array(
            'email' => $this->email,
        ));
        if (!empty($model) && $model->cpf != $this->cpf) {
            $this->addError($attribute, 'Este ' . $attribute . ' já está inscrito com outro CPF.');
        }
    }

    public function relations()
    {
        return array(
            'curso' => array(self::BELONGS_TO, 'Curso', 'curso_id'),
            'formacoes' => array(self::HAS_MANY, 'Formacao', 'inscricao_id'),
            'inscricoesOfertas' => array(self::HAS_MANY, 'InscricaoOferta', 'inscricao_id'),
            'habilitacoes' => array(self::MANY_MANY, 'Habilitacao', 'inscricao_habilitacao(inscricao_id, habilitacao_id)'),
            'ofertas' => array(self::MANY_MANY, 'Oferta', 'inscricao_oferta(inscricao_id, oferta_id)', 'order' => 'ano, mes'),
            'tccs' => array(self::HAS_MANY, 'Tcc', 'inscricao_id', 'order' => 'id ASC'),
            'pagamentos' => array(self::HAS_MANY, 'PagamentoAluno', 'inscricao_id'),
            'encontrosPresenciais' => array(self::MANY_MANY, 'EncontroPresencial', 'encontro_presencial_inscricao(encontro_presencial_id, inscricao_id)'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'cpf' => 'CPF',
            'ra' => 'RA',
            'numero_ufscar' => 'Número UFSCar',
            'email' => 'E-mail',
            'confirmarEmail' => 'Confirme seu e-mail',
            'data_nascimento' => 'Data de nascimento',
            'nome_mae' => 'Nome da mãe',
            'nome_pai' => 'Nome do pai',
            'estado_civil' => 'Estado civil',
            'telefone_fixo' => 'Telefone fixo',
            'telefone_celular' => 'Telefone celular',
            'telefone_alternativo' => 'Telefone alternativo',
            'cep' => 'CEP',
            'endereco' => 'Endereço',
            'numero' => 'Número',
            'cargo_atual' => 'Cargo atual',
            'telefone_comercial' => 'Telefone comercial',
            'curso_id' => 'Curso',
            //
            'documento_cpf' => 'Documento - CPF',
            'documento_rg' => 'Documento - RG',
            'documento_diploma' => 'Diploma da maior titulação',
            'documento_comprovante_residencia' => 'Comprovante de residência',
            'documento_curriculo' => 'Currículo',
            'documento_justificativa' => 'Documento bolsa (apenas para bolsista)',
            //
            'pagou_inscricao' => 'Pagou a inscrição?',
            'pagou_matricula' => 'Pagou a matrícula?',
            'whatsapp' => 'Número de Whatsapp',
            'skype' => 'Usuário no Skype',
            'tipo_identidade' => 'Tipo de identidade',
            'orgao_expedidor' => 'Órgão expedidor',
            'candidato_a_bolsa' => 'Candidato à bolsa',
            'candidato_a_bolsa_search' => 'Candidato à bolsa',
            'recebe_bolsa' => 'Recebe bolsa?',
            'recebe_bolsa_search' => 'Recebe bolsa?',
            'observacoes' => 'Observações',
            'tipo_bolsa' => 'Tipo de bolsa',
            'data_matricula' => 'Data de matrícula',
            'data_inscricao' => 'Data de inscrição',
            'tipo_curso' => 'Tipo do curso',

            'data_conclusao' => 'Data de conclusão',
            'processo_proex' => 'Processo ProEx',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('cpf', $this->cpf, true);
        $criteria->addSearchCondition('nome', $this->nome, true, 'AND', 'ILIKE');
        $criteria->addSearchCondition('sobrenome', $this->sobrenome, true, 'AND', 'ILIKE');
        $criteria->compare('sexo', $this->sexo, true);
        $criteria->addSearchCondition('email', $this->email, true, 'AND', 'ILIKE');
        // TODO: Colocar busca por data
//        $criteria->compare('data_nascimento', $this->data_nascimento, true);
        $criteria->compare('naturalidade', $this->naturalidade, true);
        $criteria->compare('nome_mae', $this->nome_mae, true);
        $criteria->compare('nome_pai', $this->nome_pai, true);
        $criteria->compare('estado_civil', $this->estado_civil, true);
        $criteria->compare('telefone_fixo', $this->telefone_fixo, true);
        $criteria->compare('telefone_celular', $this->telefone_celular, true);
        $criteria->compare('telefone_alternativo', $this->telefone_alternativo, true);
        $criteria->compare('cep', $this->cep, true);
        $criteria->compare('endereco', $this->endereco, true);
        $criteria->compare('numero', $this->numero, true);
        $criteria->compare('complemento', $this->complemento, true);
        $criteria->compare('cidade', $this->cidade, true);
        $criteria->compare('estado', $this->estado, true);
        $criteria->compare('cargo_atual', $this->cargo_atual, true);
        $criteria->compare('empresa', $this->empresa, true);
        $criteria->compare('telefone_comercial', $this->telefone_comercial, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('candidato_a_bolsa', $this->booleanSearch($this->candidato_a_bolsa_search));
        $criteria->compare('recebe_bolsa', $this->booleanSearch($this->recebe_bolsa_search));
        $criteria->compare('observacoes', $this->observacoes, true);
        $criteria->compare('tipo_curso', $this->tipo_curso);
        $criteria->compare('data_inscricao', $this->data_inscricao, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 50
            ),
            'sort' => array(
                /*'attributes' => array(
                    'id' => array(
                        'asc' => 'id',
                        'desc' => 'id DESC',
                    ),
                    
                ),*/
		'defaultOrder' =>['id' => SORT_DESC],
            ),
        ));
    }

    public function searchInscritos()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('cpf', $this->cpf, true);
        $criteria->compare('status', $this->status);
        $criteria->addSearchCondition('nome', $this->nome, true, 'AND', 'ILIKE');
        $criteria->addSearchCondition('sobrenome', $this->sobrenome, true, 'AND', 'ILIKE');
        $criteria->compare('candidato_a_bolsa', $this->booleanSearch($this->candidato_a_bolsa_search));
        $criteria->compare('tipo_curso', $this->tipo_curso);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 50
            ),
            'sort' => array(
                'attributes' => array(
                    'candidato_a_bolsa_search' => array(
                        'asc' => 'candidato_a_bolsa',
                        'desc' => 'candidato_a_bolsa DESC',
                    ),
                    'recebe_bolsa_search' => array(
                        'asc' => 'recebe_bolsa',
                        'desc' => 'recebe_bolsa DESC',
                    ),
                    '*',
                ),
                'defaultOrder' => array(
                    'candidato_a_bolsa' => true,
                    'cpf' => false,
                )
            ),
        ));
    }

    public function searchMatriculados()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('cpf', $this->cpf_search, true);
        $criteria->addSearchCondition('nome', $this->nome, true, 'AND', 'ILIKE');
        $criteria->addSearchCondition('sobrenome', $this->sobrenome, true, 'AND', 'ILIKE');
        $criteria->compare('sexo', $this->sexo, true);
        $criteria->addSearchCondition('email', $this->email, true, 'AND', 'ILIKE');
        $criteria->compare('status', $this->status);
        $criteria->compare('status_aluno', $this->status_aluno);
        $criteria->compare('candidato_a_bolsa', $this->booleanSearch($this->candidato_a_bolsa_search));
        $criteria->compare('recebe_bolsa', $this->booleanSearch($this->recebe_bolsa_search));
        $criteria->compare('observacoes', $this->observacoes, true);
        $criteria->compare('tipo_curso', $this->tipo_curso);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 50
            ),
            'sort' => array(
                'attributes' => array(
                    'cpf_search' => array(
                        'asc' => 'cpf',
                        'desc' => 'cpf DESC',
                    ),
                    'candidato_a_bolsa_search' => array(
                        'asc' => 'candidato_a_bolsa',
                        'desc' => 'candidato_a_bolsa DESC',
                    ),
                    'recebe_bolsa_search' => array(
                        'asc' => 'recebe_bolsa',
                        'desc' => 'recebe_bolsa DESC',
                    ),
                    '*',
                )
            ),
        ));
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function recuperarArquivoDocumento($documento, $caminhoCompleto = false)
    {
        if (empty($documento)) {
            return '';
        }

        $nomeAtributo = "documento_{$documento}";
        $atributo = $this->$nomeAtributo;

        if (!$atributo) {
            return '';
        }

        $extensao = $this->recuperarExtensao($atributo);

        $caminho = '';
        if ($caminhoCompleto == true) {
            $caminho = Yii::app()->baseUrl . '/uploads/';
        }

        return "{$caminho}{$this->cpf}_{$documento}.{$extensao}";
    }

    private function recuperarExtensao($nomeArquivo)
    {
        $partes = pathinfo($nomeArquivo);
        return $partes['extension'];
    }

    public function matricular($agora = null)
    {
        $this->validarJaPagouInscricaoOuEnviouDocumentos();

        $this->data_matricula = $agora ? : date('Y-m-d');
        $this->status = self::STATUS_MATRICULADO;
        $this->status_aluno = 'Ativo';
        $this->saveAttributes(array('data_matricula', 'status', 'status_aluno'));

        $this->removerPapel('Inscrito');
        $this->atribuirPapel('Aluno');
        $this->matricularEmProjetoIntegrador();
        Yii::log("{$this} agora é um aluno.", 'info', 'system.models.Inscricao');

        return true;
    }

    private function matricularEmProjetoIntegrador()
    {
        if (!$this->ehAlunoDeEspecializacao()) return;

        $oferta = Oferta::model()->recuperarOfertaProjetoIntegrador();

        $sql = "INSERT INTO inscricao_oferta(inscricao_id, oferta_id) VALUES ({$this->id}, {$oferta->id})";
        Yii::app()->db->createCommand($sql)->execute();

        $sql = "INSERT INTO habilitacao_inscricao_oferta(habilitacao_id, inscricao_id, oferta_id) VALUES ";
        $valores = [];
        foreach ($this->habilitacoes as $habilitacao) {
            $valores[] = "({$habilitacao->id}, {$this->id}, {$oferta->id})";
        }
        $sql .= implode(',', $valores);
        Yii::app()->db->createCommand($sql)->execute();
    }

    private function validarJaPagouInscricaoOuEnviouDocumentos()
    {
        if ($this->status != self::STATUS_DOCUMENTOS_SENDO_ANALISADOS &&
                $this->status != self::STATUS_DOCUMENTOS_VERIFICADOS) {
            throw new CHttpException(500, 'Este aluno não pode ser matriculado pois ainda não pagou a inscrição.');
            Yii::log("Tentativa de matricular {$this}.", 'error', 'system.controllers.AdminController');
        }
    }

    public function transformarEmUsuario($senhaAleatoria = null)
    {
        if ($this->ehUsuario()) {
            return false;
        }

        // A atribuição de senha aleatória por parâmetro é apenas para testes
        $senhaAleatoria = $senhaAleatoria ? : GeradorSenha::pseudoAleatoria();
        $usuario = Usuario::criarUsuario($this, $senhaAleatoria);

        if (!$usuario) {
            return false;
        }

        Yii::log("{$usuario} criado com sucesso.", 'info', 'system.models.Inscricao');
        Yii::app()->authManager->assign('Inscrito', $this->cpf);
        return $senhaAleatoria;
    }

    public function getStatusPorExtenso()
    {
        $statusPorExtenso = array(
            self::STATUS_PENDENTE => "Pré-inscrição realizada",
            self::STATUS_DOCUMENTOS_SENDO_ANALISADOS => "Documentos sendo analisados",
            self::STATUS_DOCUMENTOS_VERIFICADOS => "Documentos validados",
            self::STATUS_MATRICULADO => "Matriculado",
        );
        return $statusPorExtenso[$this->status];
    }

    public function getModalidadePorExtenso()
    {
        $modalidadesPorExtenso = array(
            'distancia' => 'A distância',
            'presencial' => 'Presencial',
            'mista' => 'Mista (distância/presencial)',
        );
        return $modalidadesPorExtenso[$this->modalidade];
    }

    public function recuperarHabilitacao($indice)
    {
        $habilitacoesInscritas = InscricaoHabilitacao::model()->findAllByAttributes(array(
            'inscricao_id' => $this->id,
        ), array(
            'order' => 'ordem'
        ));
        return !empty($habilitacoesInscritas[$indice - 1])
            ? $habilitacoesInscritas[$indice - 1]->habilitacao
            : false;
    }

    public function ehUsuario()
    {
        return Usuario::model()->findByPk($this->cpf) != null;
    }

    public function findByCpf($cpf)
    {
        $cpf = $this->completarCpfComZerosAEsquerda($cpf);
        // Pega sempre a inscrição mais nova
        $criteria = new CDbCriteria(array('order' => 'id DESC'));
        return Inscricao::model()->findByAttributes(array('cpf' => $cpf), $criteria);
    }

    public function findAllByCpf($cpf)
    {
        $cpf = $this->completarCpfComZerosAEsquerda($cpf);
        $criteria = new CDbCriteria([ 'order' => 'id' ]);
        return Inscricao::model()->findAllByAttributes([ 'cpf' => $cpf ], $criteria);
    }

    private function completarCpfComZerosAEsquerda($cpf)
    {
        while (strlen($cpf) < 11) {
            $cpf = '0' . $cpf;
        }
        return $cpf;
    }

    public function findByNumeroUfscar($numeroUfscar)
    {
        return Inscricao::model()->findByAttributes(['numero_ufscar' => $numeroUfscar]);
    }

    public function __toString()
    {
        return "[Inscrição de {$this->nomeCompleto} (CPF {$this->cpf}) (Número UFSCar {$this->numero_ufscar})]";
    }

    /**
     * As listas de inscrições em ofertas não precisam do objeto Inscricao
     * completo, apenas dos nomes dos alunos.
     */
    public function asNome()
    {
        return $this->nome . ' ' . $this->sobrenome . ' (CPF: ' . $this->cpf . ')';
    }

    public function asArray()
    {
        return array(
            'id' => $this->id,
            'cpf' => $this->cpf,
            'nome' => $this->nome,
            'sobrenome' => $this->sobrenome,
            'sexo' => $this->sexo,
            'email' => $this->email,
            'data_nascimento' => $this->data_nascimento,
            'status' => $this->status,
            'status_aluno' => $this->status_aluno,
            'observacoes_bolsa' => $this->observacoes,
        );
        // TODO: Adicionar os demais atributos aqui
    }

    public function asArrayPagamento()
    {
        return array(
            'id' => $this->id,
            'nome' => $this->nome,
            'sobrenome' => $this->sobrenome,
            'status_aluno' => $this->status_aluno,
            'observacoes_bolsa' => $this->observacoes,
            'pagamento' => $this->informacoesDePagamento(),
        );
    }

    /**
     * TODO: Tornar este método mais eficiente
     */
    private function informacoesDePagamento()
    {
        $pagamentos = Pagamento::model()->findAllByAttributes(array(
            'inscricao_id' => $this->id
                ), array(
            'order' => 'tipo',
        ));

        $inscricao = $this->recuperarPagamento($pagamentos, Pagamento::TIPO_PAGAMENTO_INSCRICAO);
        $matricula = $this->recuperarPagamento($pagamentos, Pagamento::TIPO_PAGAMENTO_MATRICULA);
        $pagouAVista = $this->recuperarPagamento($pagamentos, Pagamento::TIPO_PAGAMENTO_PAGOU_A_VISTA);
        $totalPrevisto = $this->recuperarPagamento($pagamentos, Pagamento::TIPO_PAGAMENTO_TOTAL_PREVISTO);
        $parcelas = array();
        for ($i = 1; $i <= 27; $i++) {
            $parcelas[$i] = $this->recuperarPagamento($pagamentos, Pagamento::TIPO_PAGAMENTO_PARCELA($i));
        }

        $extensao = array_map(function($pagamento) {
            return $pagamento->asArray();
        }, Pagamento::model()->findAllByAttributes(array(
                    'inscricao_id' => $this->id,
                    'tipo' => Pagamento::TIPO_PAGAMENTO_CURSO_EXTENSAO,
        )));

        $aperfeicoamento = array_map(function($pagamento) {
            return $pagamento->asArray();
        }, Pagamento::model()->findAllByAttributes(array(
                    'inscricao_id' => $this->id,
                    'tipo' => Pagamento::TIPO_PAGAMENTO_CURSO_APERFEICOAMENTO,
        )));

        return array(
            'inscricao' => $inscricao,
            'matricula' => $matricula,
            'pagouAVista' => $pagouAVista,
            'totalPrevisto' => $totalPrevisto,
            'parcelas' => $parcelas,
            'extensao' => $extensao,
            'aperfeicoamento' => $aperfeicoamento,
        );
    }

    private function recuperarPagamento($pagamentos, $tipo)
    {
        foreach ($pagamentos as $pagamento) {
            if ($pagamento->tipo == $tipo) {
                return $pagamento->asArray();
            }
        }
        // Um pagamento desse tipo não foi feito, então retorna um pagamento vazio
        return Pagamento::asArrayVazio($this->id, $tipo);
    }

    /**
     * Altera o status_aluno da inscrição, atribuindo ou revogando papéis de acordo.
     * 
     * @param string $status
     * @return boolean
     */
    public function alterarStatusAluno($status)
    {
        $this->validarEhAlunoMatriculado();
        $this->validarStatusEhValido($status);

        $statusAntigo = $this->status_aluno;
        $this->status_aluno = $status;
        $this->saveAttributes(array('status_aluno'));

        if ($status == 'Ativo') {
            // Esta é a única situação em que um aluno tem acesso às suas inscrições
            $this->removerPapel('Inscrito');
            $this->atribuirPapel('Aluno');
        } else if ($status == 'Desistente' || $status == 'Trancado' || $status == 'Cancelado' || $status == 'Inscrito') {
            $this->removerPapel('Aluno');
            $this->atribuirPapel('Inscrito');
            if ($status == 'Inscrito') {
                $this->status = Inscricao::STATUS_DOCUMENTOS_VERIFICADOS;
                $this->saveAttributes(array('status'));
            }
        }

        Yii::log("{$this} trocou o status_aluno de '{$statusAntigo}' para {$status}.", 'info', 'system.models.Inscricao');
        return true;
    }

    private function validarEhAlunoMatriculado()
    {
        if ($this->status != Inscricao::STATUS_MATRICULADO) {
            throw new CHttpException(500, "Tentativa de alterar status_aluno de inscrição que não é de aluno matriculado: '{$this}'");
        }
    }

    private function validarStatusEhValido($status)
    {
        $statusPossiveis = array('Alterar status', 'Ativo', 'Inscrito', 'Desistente', 'Trancado', 'Cancelado', 'Formado');
        if (!in_array($status, $statusPossiveis)) {
            throw new CHttpException(500, "Tentativa de atribuir status_aluno inválido: '{$status}'");
        }
    }

    protected function beforeValidate()
    {
        $this->nome = trim($this->nome);
        $this->sobrenome = trim($this->sobrenome);
        $this->cep = str_replace('-', '', $this->cep);
        $this->candidato_a_bolsa = $this->candidato_a_bolsa === 'sim' ? 1 : 0;
        $this->recebe_bolsa = $this->recebe_bolsa === 'sim' ? 1 : 0;
        return parent::beforeValidate();
    }

    protected function beforeSave()
    {
        if ($this->isNewRecord) {
            // TODO: Remover estre trecho se ficar decidido que o mesmo aluno pode se inscrever mais de uma vez
            $inscricaoComEsteCpf = Inscricao::model()->findByCpf($this->cpf);
            if (!empty($inscricaoComEsteCpf)) {
                $this->addError('cpf', 'CPF já cadastrado');
                return false;
            }

            $this->data_inscricao = date('Y-m-d');
            $this->turma = Constantes::TURMA_ABERTA;
        }
        return parent::beforeSave();
    }

    protected function afterValidate()
    {
        // Esta condição ocorre quando uma inscrição deve ser salva mas contém
        // algum erro, então o usuário volta para o formulário de cadastro.
        if (!empty($this->errors)) {
            $this->candidato_a_bolsa = $this->candidato_a_bolsa ? 'sim' : 'nao';
            $this->recebe_bolsa = $this->recebe_bolsa ? 'sim' : 'nao';
        }
        return parent::afterValidate();
    }

    protected function afterFind()
    {
        $this->candidato_a_bolsa = $this->candidato_a_bolsa ? 'sim' : 'nao';
        $this->recebe_bolsa = $this->recebe_bolsa ? 'sim' : 'nao';
        return parent::afterFind();
    }

    public function temPapel($papel)
    {
        $registros = Authassignment::model()->findAllByAttributes(array('userid' => $this->cpf));
        $papeis = array_map(function($registro) {
            return $registro->itemname;
        }, $registros);
        return in_array($papel, $papeis);
    }

    public function removerPapel($papel)
    {
        Yii::app()->authManager->revoke($papel, $this->cpf);
    }

    public function atribuirPapel($papel)
    {
        Yii::app()->authManager->assign($papel, $this->cpf);
    }

    public function getNomeCompleto()
    {
        return $this->nome . ' ' . $this->sobrenome;
    }

    public function recuperarHabilitacoes()
    {
        $habilitacoesInscritas = InscricaoHabilitacao::model()->findAllByAttributes(array(
            'inscricao_id' => $this->id,
        ), array(
            'order' => 'ordem'
        ));
        return array_map(function($habilitacaoInscrita) {
            return Habilitacao::model()->findByPk($habilitacaoInscrita->habilitacao_id);
        }, $habilitacoesInscritas);
    }

    // TODO: Fazer teste unitário
    public function estaFormado()
    {
        foreach ($this->habilitacoes as $habilitacao) {
            if (!$this->cumpriuHabilitacao($habiltiacao)) {
                return false;
            }
        }

        if (!$this->cumpriuTcc()) {
            return false;
        }

        if ($this->cargaHorariaAprovada() < Constantes::NUMERO_MINIMO_DE_HORAS_PARA_SER_ESPECIALISTA) {
            return false;
        }

        return true;
    }

    private function cumpriuHabilitacao(Habilitacao $habiltiacao)
    {
        $inscricoesOfertas = InscricaoOferta::model()->findAllByAttributes(array(
            'inscricao_id' => $this->id,
            'status' => InscricaoOferta::STATUS_APROVADO,
        ));

        $numeroDeComponentesPorPrioridade = array(
            Constantes::PRIORIDADE_NECESSARIA => 0,
            Constantes::PRIORIDADE_OPTATIVA => 0,
            Constantes::PRIORIDADE_LIVRE => 0,
        );

        $componentesHabilitacoes = ComponenteHabilitacao::model()->findAllByAttributes(array(
            'habilitacao_id' => $habiltiacao->id,
        ));

        foreach ($componentesHabilitacoes as $componenteHabilitacao) {
            $componente = $componenteHabilitacao->componenteCurricular;
            $prioridade = $componente->prioridadeParaHabilitacaoNumero($habiltiacao);
            $numeroDeComponentesPorPrioridade[$prioridade] ++;
        }

        return $numeroDeComponentesPorPrioridade[Constantes::PRIORIDADE_NECESSARIA] >= Constantes::NUMERO_MINIMO_DE_COMPONENTES_NECESSARIAS && $numeroDeComponentesPorPrioridade[Constantes::PRIORIDADE_OPTATIVA] >= Constantes::NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS && $numeroDeComponentesPorPrioridade[Constantes::PRIORIDADE_LIVRE] >= Constantes::NUMERO_MINIMO_DE_COMPONENTES_LIVRES;

        die(var_dump($numeroDeComponentesPorPrioridade));
    }

    private function cumpriuTcc()
    {
        
    }

    public function cargaHorariaAprovada()
    {
        $inscricoesOfertas = InscricaoOferta::model()->findAllByAttributes(array(
            'inscricao_id' => $this->id
        ));

        $horasAprovadas = 0;
        foreach ($inscricoesOfertas as $inscricaoOferta) {
            if ($inscricaoOferta->ehAprovada()) {
                $horasAprovadas += $inscricaoOferta->oferta->componenteCurricular->carga_horaria;
            }
        }

        return $horasAprovadas;
    }

    public function statusAlunoAtivo()
    {
        return $this->status_aluno === 'Ativo';
    }

    /**
     * Dependendo do tipo de curso e da quantidade de pagamentos feitos, o aluno
     * tem um determinado número de ofertas em que pode se inscrever.
     */
    public function numeroDeOfertasQuePodeSeInscrever()
    {
        switch ($this->tipo_curso) {
            case self::TIPO_CURSO_EXTENSAO:
                return Constantes::NUMERO_DE_COMPONENTES_OBRIGATORIOAS_PARA_EXTENSAO_E_APERFEICOAMENTO
                    + Constantes::NUMERO_DE_COMPONENTES_LIVRES_PARA_EXTENSAO;

                $pagamentosDeExtensao = Pagamento::model()->findAllByAttributes(array(
                    'inscricao_id' => $this->id,
                    'tipo' => Pagamento::TIPO_PAGAMENTO_CURSO_EXTENSAO,
                ));
                return 3 * count($pagamentosDeExtensao);
            case self::TIPO_CURSO_APERFEICOAMENTO:
                return Constantes::NUMERO_DE_COMPONENTES_OBRIGATORIOAS_PARA_EXTENSAO_E_APERFEICOAMENTO
                    + Constantes::NUMERO_DE_COMPONENTES_LIVRES_PARA_APERFEICOAMENTO;

                $pagamentosDeAperfeicoamento = Pagamento::model()->findAllByAttributes(array(
                    'inscricao_id' => $this->id,
                    'tipo' => Pagamento::TIPO_PAGAMENTO_CURSO_APERFEICOAMENTO,
                ));
                return 9 * count($pagamentosDeAperfeicoamento);
            case self::TIPO_CURSO_ESPECIALIZACAO:
                return 9999;
        }
    }

    public function tipoDeCursoPorExtenso()
    {
        switch ($this->tipo_curso) {
            case self::TIPO_CURSO_EXTENSAO:
                return self::TIPO_CURSO_EXTENSAO_STRING;
            case self::TIPO_CURSO_APERFEICOAMENTO:
                return self::TIPO_CURSO_APERFEICOAMENTO_STRING;
            case self::TIPO_CURSO_ESPECIALIZACAO:
                return self::TIPO_CURSO_ESPECIALIZACAO_STRING;
        }
    }

    public function ehAlunoDeEspecializacao()
    {
        return $this->tipo_curso == self::TIPO_CURSO_ESPECIALIZACAO;
    }

    public function ehAlunoDeExtensao()
    {
        return $this->tipo_curso == self::TIPO_CURSO_EXTENSAO;
    }

    public function ehAlunoDeAperfeicoamento()
    {
        return $this->tipo_curso == self::TIPO_CURSO_APERFEICOAMENTO;
    }

    public function recuperarNumeroDeComponentesLivresQuePodeSeInscrever()
    {
        if ($this->ehAlunoDeExtensao()) {
            return Constantes::NUMERO_DE_COMPONENTES_LIVRES_PARA_EXTENSAO;
        } else if ($this->ehAlunoDeAperfeicoamento()) {
            return Constantes::NUMERO_DE_COMPONENTES_LIVRES_PARA_APERFEICOAMENTO;
        }
        return Constantes::NUMERO_MINIMO_DE_COMPONENTES_LIVRES;
    }

    public function estaInscritoNaHabilitacao($habilitacaoId)
    {
        return InscricaoHabilitacao::model()->findByPk(array(
            'habilitacao_id' => $habilitacaoId,
            'inscricao_id' => $this->id,
        )) != null;
    }

    public function gerarStringDescritiva()
    {
        if ($this->ehAlunoDeExtensao()) {
            return 'Curso de extensão';
        }
        if ($this->ehAlunoDeAperfeicoamento()) {
            return 'Curso de aperfeiçoamento';
        }
        $habilitacoes = $this->habilitacoes;
        $string = "Curso de especialização em {$habilitacoes[0]->nome}";
        for ($i = 1; $i < count($habilitacoes); $i++) {
            $string .= ' e ' . $habilitacoes[$i]->nome;
        }
        return $string;
    }

    public function recuperarRa()
    {
        return $this->ra ?? '';
    }

    public function estaAtivo()
    {
        // return true;
        return $this->status_aluno == self::STATUS_ALUNO_ATIVO
            || $this->status_aluno == self::STATUS_ALUNO_FORMADO;
    }

    public function finalizouCurso()
    {
        // return true;
        return $this->status_aluno == self::STATUS_ALUNO_FORMADO;
    }

    public function recuperarPeriodoInicial()
    {
        $dataMatricula = $this->data_matricula ?? '9999-12-31';
        $primeiraOferta = $this->ofertas[0] ?? null;
        if ($primeiraOferta) {
            $dataPrimeiraOferta = $primeiraOferta->recuperarDataInicio();
        }
        $dataMaisAntiga = min($dataMatricula, $dataPrimeiraOferta ?? '9999-12-31');
        [$ano, $mes, $dia] = explode('-', $dataMaisAntiga);
        return "{$dia}/{$mes}/{$ano}";
    }

    public function recuperarPeriodoFinal()
    {
        $datasTccs = array_map(function($tcc) {
            return $tcc->final_data_entrega ?? '0001-01-01';
        }, $this->tccs);
        $ultimaDataTcc = max($datasTccs);
        $ultimaOferta = $this->ofertas[count($this->ofertas) - 1] ?? null;
        if ($ultimaOferta) {
            $dataUltimaOferta = $ultimaOferta->recuperarDataFinal();
        }
        $ultimaData = max($ultimaDataTcc, $dataUltimaOferta);
        [$ano, $mes, $dia] = explode('-', $ultimaData);
        return "{$dia}/{$mes}/{$ano}";
    }

    public function estaFormadoOuProntoParaFormar()
    {
        return $this->status_aluno == self::STATUS_ALUNO_FORMADO || $this->recuperarDataConclusao() != '';
    }

    public function recuperarDataConclusao()
    {
        $inscricaoHabilitacoes = InscricaoHabilitacao::model()->findAllByAttributes([
            'inscricao_id' => $this->id,
        ]);

        $maiorData = '0001-01-01';
        foreach ($inscricaoHabilitacoes as $inscricaoHabilitacao) {
            if ($inscricaoHabilitacao->data_conclusao > $maiorData) {
                $maiorData = $inscricaoHabilitacao->data_conclusao;
            }
        }

        return $maiorData === '0001-01-01' ? '' : $maiorData;
    }

    public function getAno_inicio()
    {
        // 2019 é só um fallback
        $data = !empty($this->data_matricula) ? $this->data_matricula : $this->data_inscricao;
        $data = !empty($data) ? $data : '01/01/2019';
        [ $_, $_, $ano ] = explode('/', $data);
        return $ano;
    }

    public function getAno_conclusao()
    {
        // 2021 é só um fallback
        $data = !empty($this->data_conclusao) ? $this->data_conclusao : '01/01/2021';
        // die($data);
        [ $_, $_, $ano ] = explode('/', $data);
        return $ano;
    }

}
