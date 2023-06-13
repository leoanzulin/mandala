<?php

/**
 * This is the model class for table "tcc".
 *
 * The followings are the available columns in table 'tcc':
 * @property int $id
 * @property int $inscricao_id
 * @property int $habilitacao_id
 * @property string $titulo
 * @property string $tipo
 * @property string $validacao_arquivo
 * @property string $validacao_data_entrega
 * @property string $validacao_orientador_cpf
 * @property string $validacao_consideracoes
 * @property string $validacao_tem_pendencias
 * @property string $banca_arquivo
 * @property string $banca_data_entrega
 * @property string $banca_data_apresentacao
 * @property string $banca_membro1_cpf
 * @property string $banca_membro2_cpf
 * @property string $banca_membro3_cpf
 * @property string $banca_membro1_eh_tutor
 * @property string $banca_membro2_eh_tutor
 * @property string $banca_membro3_eh_tutor
 * @property string $banca_membro1_consideracoes
 * @property string $banca_membro2_consideracoes
 * @property string $banca_membro3_consideracoes
 * @property string $banca_tem_pendencias
 * @property string $final_arquivo_doc
 * @property string $final_arquivo_pdf
 * @property string $final_data_entrega
 * @property string $final_orientador_cpf
 * @property string $final_coorientador_cpf
 * @property string $aprovado
 * 
 * Estes campos correspondem ao item 3 do TCC:
 * 3. Caracterização do especialista 
 * 3.1. Perfil profissional do especialista (quem é esse especialista?)
 * 3.2. Importância da formação desse profissional (em que esse especialista contribui?)
 * 3.3. Principais saberes e competências do profissional (o que esse especialista deve saber para realizar suas atividades com qualidade?)
 * 3.4. Tipos de atividades e funções principais do profissional (qual é o campo de atuação desse especialista?)
 * 3.5. Principais desafios e dificuldades comuns do profissional
 * @property string $caracterizacao_especialista_perfil
 * @property string $caracterizacao_especialista_importancia
 * @property string $caracterizacao_especialista_saberes
 * @property string $caracterizacao_especialista_atividades
 * @property string $caracterizacao_especialista_desafios
 *
 * The followings are the available model relations:
 * @property Inscricao $inscricao
 * @property Habilitacao $habilitacao
 * @property SinteseComponente[] $sinteses_componentes
 * @property PropostaPedagogica[] $propostas_pedagogicas
 * @property Docente $orientador_provisorio
 * @property Docente|TUtor $membro_banca1
 * @property Docente|TUtor $membro_banca2
 * @property Docente|TUtor $membro_banca3
 * @property Docente $orientador_final
 * @property Docente $coorientador_final
 */
class Tcc extends ActiveRecord
{
    const FASES = [
        // 0 => 'TCC não iniciado',
        1 => 'Título definido',
        2 => 'Versão para validação entregue',
        3 => 'Pré-orientador atribuído',
        4 => 'Aprovado pelo pré-orientador',
        5 => 'Versão da banca entregue',
        6 => 'Banca atribuída',
        7 => 'Aprovado pela banca',
        8 => 'Versão final entregue',
        9 => 'Orientador final atribuído',
        10 => 'TCC aprovado',
    ];
    const FASE_TCC_NAO_INICIADO = 0;
    const FASE_TITULO_DEFINIDO = 1;
    const FASE_VERSAO_PARA_VALIDACAO_ENTREGUE = 2;
    const FASE_PRE_ORIENTADOR_ATRIBUIDO = 3;
    const FASE_APROVADO_PELO_PRE_ORIENTADOR = 4;
    const FASE_VERSAO_DA_BANCA_ENTREGUE = 5;
    const FASE_BANCA_ATRIBUIDA = 6;
    const FASE_APROVADO_PELA_BANCA = 7;
    const FASE_VERSAO_FINAL_ENTREGUE = 8;
    const FASE_ORIENTADOR_FINAL_ATRIBUIDO = 9;
    const FASE_TCC_APROVADO = 10;

    // De acordo com a reunião de 17/07/2020, todos os TCCs agora serão deste tipo
    public $tipo = 'Síntese reflexiva';
    // Atributos setados apenas no momento em que cada etapa do TCC foi entregue pela primeira vez
    public $entregou_validacao;
    public $entregou_banca;
    public $entregou_final;
    public $atribuiu_pre_orientador;
    public $atribuiu_banca;
    public $atribuiu_orientador_final;

    public $inscricao_nome_completo_search;
    public $fase_search;

    public function tableName()
    {
        return 'tcc';
    }

    public function rules()
    {
        return array(
            ['inscricao_id, habilitacao_id, titulo', 'required'],
            ['titulo, tipo', 'length', 'max' => 256],
            ['id, inscricao_id, habilitacao_id, titulo, tipo, caracterizacao_especialista_perfil, caracterizacao_especialista_importancia, caracterizacao_especialista_saberes, caracterizacao_especialista_atividades, caracterizacao_especialista_desafios, validacao_data_entrega, validacao_orientador_cpf, validacao_consideracoes, validacao_tem_pendencias, banca_data_entrega, banca_data_apresentacao, banca_membro1_cpf, banca_membro2_cpf, banca_membro3_cpf, banca_membro1_eh_tutor, banca_membro2_eh_tutor, banca_membro3_eh_tutor, banca_membro1_consideracoes, banca_membro2_consideracoes, banca_membro3_consideracoes, banca_tem_pendencias, final_data_entrega, final_orientador_cpf, final_coorientador_cpf, aprovado', 'safe'],
            ['id, inscricao_id, habilitacao_id, titulo, tipo, caracterizacao_especialista_perfil, caracterizacao_especialista_importancia, caracterizacao_especialista_saberes, caracterizacao_especialista_atividades, caracterizacao_especialista_desafios, validacao_data_entrega, validacao_orientador_cpf, validacao_consideracoes, validacao_tem_pendencias, banca_data_entrega, banca_data_apresentacao, banca_membro1_cpf, banca_membro2_cpf, banca_membro3_cpf, banca_membro1_eh_tutor, banca_membro2_eh_tutor, banca_membro3_eh_tutor, banca_membro1_consideracoes, banca_membro2_consideracoes, banca_membro3_consideracoes, banca_tem_pendencias, final_data_entrega, final_orientador_cpf, final_coorientador_cpf, aprovado, fase_search, inscricao_nome_completo_search', 'safe', 'on' => 'search'],
            ['validacao_arquivo, banca_arquivo',
                'file',
                'types' => 'doc, docx, pdf',
                'maxSize' => 1024 * 1024 * 5,
                'tooLarge' => 'Arquivo deve ter menos de 5MB',
                'safe' => false,
                'allowEmpty' => true],
            ['final_arquivo_doc',
                'file',
                'types' => 'doc, docx',
                'maxSize' => 1024 * 1024 * 5,
                'tooLarge' => 'Arquivo deve ter menos de 5MB',
                'safe' => false,
                'allowEmpty' => true],
            ['final_arquivo_pdf',
                'file',
                'types' => 'pdf',
                'maxSize' => 1024 * 1024 * 5,
                'tooLarge' => 'Arquivo deve ter menos de 5MB',
                'safe' => false,
                'allowEmpty' => true],
        );
    }

    public function relations()
    {
        return [
            'inscricao' => [self::BELONGS_TO, 'Inscricao', 'inscricao_id'],
            'habilitacao' => [self::BELONGS_TO, 'Habilitacao', 'habilitacao_id'],
            'sinteses_componentes' => [self::HAS_MANY, 'SinteseComponente', 'tcc_id', 'order' => 'ordem ASC'],
            'propostas_pedagogicas' => [self::HAS_MANY, 'PropostaPedagogica', 'tcc_id', 'order' => 'ordem ASC'],
            'orientador_provisorio' => [self::BELONGS_TO, 'Docente', 'validacao_orientador_cpf'],
            'orientador_final' => [self::BELONGS_TO, 'Docente', 'final_orientador_cpf'],
            'coorientador_final' => [self::BELONGS_TO, 'Docente', 'final_coorientador_cpf'],
        ];
    }

    public function getBanca_membro1()
    {
        $cpf = $this->banca_membro1_cpf;
        return $this->banca_membro1_eh_tutor ? Tutor::model()->findByPk($cpf) : Docente::model()->findByPk($cpf);
    }

    public function getBanca_membro2()
    {
        $cpf = $this->banca_membro2_cpf;
        return $this->banca_membro2_eh_tutor ? Tutor::model()->findByPk($cpf) : Docente::model()->findByPk($cpf);
    }

    public function getBanca_membro3()
    {
        $cpf = $this->banca_membro3_cpf;
        return $this->banca_membro3_eh_tutor ? Tutor::model()->findByPk($cpf) : Docente::model()->findByPk($cpf);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inscricao_id' => 'Aluno',
            'habilitacao_id' => 'Habilitação',
            'titulo' => 'Título',
            'tipo' => 'Tipo de pesquisa',
            'caracterizacao_especialista_perfil' => 'Perfil profissional do especialista',
            'caracterizacao_especialista_importancia' => 'Importância da formação desse profissional',
            'caracterizacao_especialista_saberes' => 'Principais saberes e competências do profissional',
            'caracterizacao_especialista_atividades' => 'Tipos de atividades e funções principais do profissional',
            'caracterizacao_especialista_desafios' => 'Principais desafios e dificuldades comuns do profissional',
            'validacao_arquivo' => 'Arquivo versão validação',
            'validacao_data_entrega' => 'Data de entrega da versão de validação',
            'validacao_orientador_cpf' => 'Pré-orientador',
            'validacao_consideracoes' => 'Considerações do pré-orientador',
            'validacao_tem_pendencias' => 'Validação foi aprovada com pendências?',
            'banca_arquivo' => 'Arquivo versão banca',
            'banca_data_entrega' => 'Data de entrega da versão da banca',
            'banca_data_apresentacao' => 'Data de apresentação do TCC',
            'banca_membro1_cpf' => 'Membro da banca 1',
            'banca_membro2_cpf' => 'Membro da banca 2',
            'banca_membro3_cpf' => 'Membro da banca 3',
            'banca_membro1_eh_tutor' => 'Membro da banca 1 é tutor?',
            'banca_membro2_eh_tutor' => 'Membro da banca 2 é tutor?',
            'banca_membro3_eh_tutor' => 'Membro da banca 3 é tutor?',
            'banca_membro1_consideracoes' => 'Considerações do membro da banca 1',
            'banca_membro2_consideracoes' => 'Considerações do membro da banca 2',
            'banca_membro3_consideracoes' => 'Considerações do membro da banca 3',
            'banca_tem_pendencias' => 'Banca aprovou com pendências?',
            'final_arquivo_doc' => 'Arquivo final DOC',
            'final_arquivo_pdf' => 'Arquivo final PDF',
            'final_data_entrega' => 'Data de entrega da versão final',
            'final_orientador_cpf' => 'Orientador final',
            'final_coorientador_cpf' => 'Coorientador final',
            'aprovado' => 'Aprovado?',
        ];
    }

    public function search($faseTcc = null)
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('inscricao_id', $this->inscricao_id, true);
        $criteria->compare('habilitacao_id', $this->habilitacao_id, true);
        $criteria->compare('titulo', $this->titulo, true);
        $criteria->compare('tipo', $this->tipo, true);
        $criteria->compare('caracterizacao_especialista_perfil', $this->caracterizacao_especialista_perfil, true);
        $criteria->compare('caracterizacao_especialista_importancia', $this->caracterizacao_especialista_importancia, true);
        $criteria->compare('caracterizacao_especialista_saberes', $this->caracterizacao_especialista_saberes, true);
        $criteria->compare('caracterizacao_especialista_atividades', $this->caracterizacao_especialista_atividades, true);
        $criteria->compare('caracterizacao_especialista_desafios', $this->caracterizacao_especialista_desafios, true);
        $criteria->compare('validacao_arquivo', $this->validacao_arquivo, true);
        $criteria->compare('validacao_data_entrega', $this->validacao_data_entrega, true);
        $criteria->compare('validacao_orientador_cpf', $this->validacao_orientador_cpf, true);
        $criteria->compare('validacao_consideracoes', $this->validacao_consideracoes, true);
        $criteria->compare('validacao_tem_pendencias', $this->validacao_tem_pendencias, true);
        $criteria->compare('banca_arquivo', $this->banca_arquivo, true);
        $criteria->compare('banca_data_entrega', $this->banca_data_entrega, true);
        $criteria->compare('banca_data_apresentacao', $this->banca_data_apresentacao, true);
        $criteria->compare('banca_membro1_cpf', $this->banca_membro1_cpf, true);
        $criteria->compare('banca_membro2_cpf', $this->banca_membro2_cpf, true);
        $criteria->compare('banca_membro3_cpf', $this->banca_membro3_cpf, true);
        $criteria->compare('banca_membro1_eh_tutor', $this->banca_membro1_eh_tutor, true);
        $criteria->compare('banca_membro2_eh_tutor', $this->banca_membro2_eh_tutor, true);
        $criteria->compare('banca_membro3_eh_tutor', $this->banca_membro3_eh_tutor, true);
        $criteria->compare('banca_membro1_consideracoes', $this->banca_membro1_consideracoes, true);
        $criteria->compare('banca_membro2_consideracoes', $this->banca_membro2_consideracoes, true);
        $criteria->compare('banca_membro3_consideracoes', $this->banca_membro3_consideracoes, true);
        $criteria->compare('banca_tem_pendencias', $this->banca_tem_pendencias, true);
        $criteria->compare('final_arquivo_doc', $this->final_arquivo_doc, true);
        $criteria->compare('final_arquivo_pdf', $this->final_arquivo_pdf, true);
        $criteria->compare('final_data_entrega', $this->final_data_entrega, true);
        $criteria->compare('final_orientador_cpf', $this->final_orientador_cpf, true);
        $criteria->compare('final_coorientador_cpf', $this->final_coorientador_cpf, true);
        $criteria->compare('aprovado', $this->aprovado, true);

        $criteria->with = ['inscricao'];
        if (!empty($this->inscricao_nome_completo_search)) {
            // https://stackoverflow.com/questions/19841264/yii-cdbcriteria-select-a-field-from-a-related-object-from-another-related-object
            $criteria->addCondition("CONCAT(TRIM(inscricao.nome), ' ', TRIM(inscricao.sobrenome)) ILIKE :nome");
            $criteria->params = array_merge($criteria->params, [
                ':nome' => "%{$this->inscricao_nome_completo_search}%",
            ]);
        }

        if (!empty($this->fase_search) || !empty($faseTcc)) {
            $fase = $this->fase_search ?? $faseTcc;
            switch ($fase) {
                case self::FASE_TITULO_DEFINIDO:
                    $criteria->addCondition('titulo IS NOT NULL AND validacao_arquivo IS NULL');
                    break;
                case self::FASE_VERSAO_PARA_VALIDACAO_ENTREGUE:
                    $criteria->addCondition('validacao_arquivo IS NOT NULL AND validacao_orientador_cpf IS NULL');
                    break;
                case self::FASE_PRE_ORIENTADOR_ATRIBUIDO:
                    $criteria->addCondition('validacao_orientador_cpf IS NOT NULL AND validacao_tem_pendencias IS NULL');
                    break;
                case self:: FASE_APROVADO_PELO_PRE_ORIENTADOR:
                    $criteria->addCondition('validacao_tem_pendencias IS NOT NULL AND banca_arquivo IS NULL');
                    break;
                case self::FASE_VERSAO_DA_BANCA_ENTREGUE:
                    $criteria->addCondition('banca_arquivo IS NOT NULL AND banca_membro1_cpf IS NULL');
                    break;
                case self::FASE_BANCA_ATRIBUIDA:
                    $criteria->addCondition('banca_membro1_cpf IS NOT NULL AND banca_tem_pendencias IS NULL');
                    break;
                case self::FASE_APROVADO_PELA_BANCA:
                    $criteria->addCondition('banca_tem_pendencias IS NOT NULL AND final_arquivo_doc IS NULL');
                    break;
                case self::FASE_VERSAO_FINAL_ENTREGUE:
                    $criteria->addCondition('final_arquivo_doc IS NOT NULL AND final_orientador_cpf IS NULL');
                    break;
                case self::FASE_ORIENTADOR_FINAL_ATRIBUIDO:
                    $criteria->addCondition('final_orientador_cpf IS NOT NULL AND aprovado IS NULL');
                    break;
                case self::FASE_TCC_APROVADO:
                    $criteria->addCondition('aprovado IS TRUE');
                    break;
            }
        }

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'sort' => [
                'defaultOrder' => 'titulo ASC',
                'attributes' => [
                    'inscricao_nome_completo_search' => [
                        'asc' => 'inscricao.sobrenome, inscricao.sobrenome',
                        'desc' => 'inscricao.sobrenome DESC, inscricao.sobrenome DESC',
                    ],
                    '*',
                ],
            ],
        ]);
    }

    public function searchTccsQueOriento($cpf)
    {
        $criteria = new CDbCriteria;
        $criteria->compare('validacao_orientador_cpf', $cpf, true, 'OR');
        $criteria->compare('banca_membro1_cpf', $cpf, true, 'OR');
        $criteria->compare('banca_membro2_cpf', $cpf, true, 'OR');
        $criteria->compare('banca_membro3_cpf', $cpf, true, 'OR');
        $criteria->compare('final_orientador_cpf', $cpf, true, 'OR');
        $criteria->compare('final_coorientador_cpf', $cpf, true, 'OR');

        $criteria->with = ['inscricao'];
        if (!empty($this->inscricao_nome_completo_search)) {
            // https://stackoverflow.com/questions/19841264/yii-cdbcriteria-select-a-field-from-a-related-object-from-another-related-object
            $criteria->addCondition("CONCAT(TRIM(inscricao.nome), ' ', TRIM(inscricao.sobrenome)) ILIKE :nome");
            $criteria->params = array_merge($criteria->params, [
                ':nome' => "%{$this->inscricao_nome_completo_search}%",
            ]);
        }

        if (!empty($this->fase_search)) {
            $fase = $this->fase_search;
            switch ($fase) {
                case self::FASE_TITULO_DEFINIDO:
                    $criteria->addCondition('titulo IS NOT NULL AND validacao_arquivo IS NULL');
                    break;
                case self::FASE_VERSAO_PARA_VALIDACAO_ENTREGUE:
                    $criteria->addCondition('validacao_arquivo IS NOT NULL AND validacao_orientador_cpf IS NULL');
                    break;
                case self::FASE_PRE_ORIENTADOR_ATRIBUIDO:
                    $criteria->addCondition('validacao_orientador_cpf IS NOT NULL AND validacao_tem_pendencias IS NULL');
                    break;
                case self:: FASE_APROVADO_PELO_PRE_ORIENTADOR:
                    $criteria->addCondition('validacao_tem_pendencias IS NOT NULL AND banca_arquivo IS NULL');
                    break;
                case self::FASE_VERSAO_DA_BANCA_ENTREGUE:
                    $criteria->addCondition('banca_arquivo IS NOT NULL AND banca_membro1_cpf IS NULL');
                    break;
                case self::FASE_BANCA_ATRIBUIDA:
                    $criteria->addCondition('banca_membro1_cpf IS NOT NULL AND banca_tem_pendencias IS NULL');
                    break;
                case self::FASE_APROVADO_PELA_BANCA:
                    $criteria->addCondition('banca_tem_pendencias IS NOT NULL AND final_arquivo_doc IS NULL');
                    break;
                case self::FASE_VERSAO_FINAL_ENTREGUE:
                    $criteria->addCondition('final_arquivo_doc IS NOT NULL AND final_orientador_cpf IS NULL');
                    break;
                case self::FASE_ORIENTADOR_FINAL_ATRIBUIDO:
                    $criteria->addCondition('final_orientador_cpf IS NOT NULL AND aprovado IS NULL');
                    break;
                case self::FASE_TCC_APROVADO:
                    $criteria->addCondition('aprovado IS TRUE');
                    break;
            }
        }

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'sort' => [
                'defaultOrder' => 'titulo ASC',
                'attributes' => [
                    'inscricao_nome_completo_search' => [
                        'asc' => 'inscricao.sobrenome, inscricao.sobrenome',
                        'desc' => 'inscricao.sobrenome DESC, inscricao.sobrenome DESC',
                    ],
                    '*',
                ],
            ],
        ]);
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getOrientadores()
    {
        $orientadores = '';
        if ($this->final_orientador_cpf) {
            $orientadores = $this->orientador_final->nomeCompleto;
        }
        if ($this->final_coorientador_cpf) {
            $orientadores .= ', ' . $this->coorientador_final->nomeCompleto;
        }
        return $orientadores;
    }

    public function __toString()
    {
        return "TCC \"{$this->titulo}\" de {$this->inscricao->nomeCompleto} ($this->inscricao->id)";
    }

    protected function beforeSave()
    {
        $camposQuePodemSerNulos = [
            'validacao_data_entrega',
            'validacao_orientador_cpf',
            'banca_membro1_cpf',
            'banca_membro2_cpf',
            'banca_membro3_cpf',
            'banca_data_entrega',
            'banca_data_apresentacao',
            'final_data_entrega',
            'final_orientador_cpf',
            'final_coorientador_cpf',
        ];

        if (parent::beforeSave()) {
            foreach ($camposQuePodemSerNulos as $chave) {
                if (empty($this[$chave])) $this[$chave] = null;
            }
            $this->atualizado_em = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    public function recuperarStatus()
    {
        if ($this->aprovado) return self::FASE_TCC_APROVADO;
        if (!empty($this->final_orientador_cpf)) return self::FASE_ORIENTADOR_FINAL_ATRIBUIDO;
        if (!empty($this->final_arquivo_doc)) return self::FASE_VERSAO_FINAL_ENTREGUE;
        if ($this->banca_tem_pendencias !== null) return self::FASE_APROVADO_PELA_BANCA;
        if (!empty($this->banca_membro1_cpf)) return self::FASE_BANCA_ATRIBUIDA;
        if (!empty($this->banca_data_entrega)) return self::FASE_VERSAO_DA_BANCA_ENTREGUE;
        if ($this->validacao_tem_pendencias !== null) return self::FASE_APROVADO_PELO_PRE_ORIENTADOR;
        if (!empty($this->validacao_orientador_cpf)) return self::FASE_PRE_ORIENTADOR_ATRIBUIDO;
        if (!empty($this->validacao_arquivo)) return self::FASE_VERSAO_PARA_VALIDACAO_ENTREGUE;
        if (!empty($this->titulo)) return TCC::FASE_TITULO_DEFINIDO;
        return self::FASE_TCC_NAO_INICIADO;
    }

    public function recuperarArquivoValidacao()
    {
        if (empty($this->validacao_arquivo)) return '';
        $caminho = Yii::app()->baseUrl . '/tccs/';
        $extensao = $this->recuperarExtensao($this->validacao_arquivo);
        return "{$caminho}tcc_{$this->id}_validacao.{$extensao}";
    }

    private function recuperarExtensao($nomeArquivo)
    {
        $partes = pathinfo($nomeArquivo);
        return $partes['extension'];
    }

    public function recuperarArquivoBanca()
    {
        if (empty($this->banca_arquivo)) return '';
        $caminho = Yii::app()->baseUrl . '/tccs/';
        $extensao = $this->recuperarExtensao($this->banca_arquivo);
        return "{$caminho}tcc_{$this->id}_banca.{$extensao}";
    }

    public function recuperarArquivoFinalDoc()
    {
        if (empty($this->final_arquivo_doc)) return '';
        $caminho = Yii::app()->baseUrl . '/tccs/';
        return "{$caminho}tcc_{$this->id}_final.doc";
    }

    public function recuperarArquivoFinalPdf()
    {
        if (empty($this->final_arquivo_pdf)) return '';
        $caminho = Yii::app()->baseUrl . '/tccs/';
        return "{$caminho}tcc_{$this->id}_final.pdf";
    }

    public function podeEntregarVersaoValidacao()
    {
        $status = $this->recuperarStatus();
        return self::FASE_TITULO_DEFINIDO <= $status && $status <= self::FASE_VERSAO_PARA_VALIDACAO_ENTREGUE;
    }

    public function podeEntregarVersaoBanca()
    {
        $status = $this->recuperarStatus();
        return self::FASE_APROVADO_PELO_PRE_ORIENTADOR <= $status && $status <= self::FASE_VERSAO_DA_BANCA_ENTREGUE;
    }

    public function podeEntregarVersaoFinal()
    {
        $status = $this->recuperarStatus();
        return self::FASE_APROVADO_PELA_BANCA <= $status && $status <= self::FASE_VERSAO_FINAL_ENTREGUE;
    }

    public function atribuirPreOrientador($cpf)
    {
        if (empty($this->validacao_orientador_cpf) && !empty($cpf)) {
            $this->atribuiu_pre_orientador = true;
            $this->validacao_orientador_cpf = $cpf;
        }
    }

    public function atribuirBanca($cpf1, $cpf2, $cpf3 = null)
    {
        if (empty($this->banca_membro1_cpf) && !empty($cpf1)) {
            $this->atribuiu_banca = true;
            $this->banca_membro1_cpf = $cpf1;
            $this->banca_membro2_cpf = $cpf2;
            if (!empty($cpf3)) $this->banca_membro3_cpf = $cpf3;
        }
    }

    public function atribuirOrientadorFinal($cpfOrientador, $cpfCoorientador = null)
    {
        if (empty($this->final_orientador_cpf) && !empty($cpfOrientador)) {
            $this->atribuiu_orientador_final = true;
            $this->final_orientador_cpf = $cpfOrientador;
            if (!empty($cpfCoorientador)) $this->final_coorientador_cpf = $cpfCoorientador;
        }
    }

    public function getFase()
    {
        return self::FASES[ $this->recuperarStatus() ];
    }

    public function recuperarTccsQueOriento($cpf)
    {
        $tccs = Tcc::model()->findAll(
            'validacao_orientador_cpf = :cpf
            OR banca_membro1_cpf = :cpf
            OR banca_membro2_cpf = :cpf
            OR banca_membro3_cpf = :cpf
            OR final_orientador_cpf = :cpf
            OR final_coorientador_cpf = :cpf',
            [ ':cpf' => $cpf ]
        );
        return $tccs;
    }

    public function ehOrientador($cpf)
    {
        return $cpf == $this->validacao_orientador_cpf
            || $cpf == $this->banca_membro1_cpf
            || $cpf == $this->final_orientador_cpf
            || $cpf == $this->final_coorientador_cpf;
    }

    public function ehMembroDaBanca($cpf)
    {
        return $cpf == $this->banca_membro1_cpf
            || $cpf == $this->banca_membro2_cpf
            || $cpf == $this->banca_membro3_cpf;
    }

    public function recuperarAnoDeConclusao()
    {
        if (!empty($this->final_data_entrega)) {
            return substr($this->final_data_entrega, 0, 4);
        }
        return '-';
    }

    public function getAnoDeConclusao()
    {
        return $this->recuperarAnoDeConclusao();
    }

    public function recuperarOrientador()
    {
        return $this->final_orientador_cpf ? $this->orientador_final->nomeCompleto : '';
    }

    public function recuperarProcessoProExWeb()
    {
        $habilitacaoId = $this->habilitacao_id;
        $inscricaoId = $this->inscricao_id;
        $sql = "SELECT processo_proex FROM inscricao_habilitacao WHERE habilitacao_id = {$habilitacaoId} AND inscricao_id = {$inscricaoId}";
        $processo = Yii::app()->db->createCommand($sql)->queryAll();
        return !empty($processo[0]) ? $processo[0]['processo_proex'] : null;
    }
}
