<?php

/**
 * This is the model class for table "oferta".
 *
 * The followings are the available columns in table 'oferta':
 * @property string $id
 * @property string $componente_curricular_id
 * @property integer $ano
 * @property integer $mes
 * @property string $data_inicio
 * @property string $link_moodle
 * @property string $codigo_moodle
 * @property integer $turma
 *
 * The followings are the available model relations:
 * @property ComponenteCurricular $componenteCurricular
 * @property Inscricao $inscricoes
 * 
 * Ao adicionar campos à esta classe, lembre-se de atualizar o RecuperadorDeOfertas
 */
class Oferta extends CActiveRecord
{

    // Atributos utilizados no salvamento de ofertas para armazenar
    // temporariamente os CPFs de docentes e tutores associados à esta
    // oferta
    public $docentesArray;
    public $tutoresArray;

    public function tableName()
    {
        return 'oferta';
    }

    public function rules()
    {
        return array(
            array('componente_curricular_id, ano, mes', 'required'),
            array('ano, mes, turma', 'numerical', 'integerOnly' => true),
            array('link_moodle, codigo_moodle', 'length', 'max' => 256),
            array('data_inicio', 'safe'),
            array('id, componente_curricular_id, ano, mes, data_inicio, link_moodle, codigo_moodle', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'componenteCurricular' => array(self::BELONGS_TO, 'ComponenteCurricular', 'componente_curricular_id'),
            'inscricoes' => array(self::MANY_MANY, 'Inscricao', 'inscricao_oferta(inscricao_id, oferta_id)', 'order' => 'nome ASC, sobrenome ASC'),
            'docentes' => array(self::MANY_MANY, 'Docente', 'docente_oferta(oferta_id, docente_cpf)'),
            'tutores' => array(self::MANY_MANY, 'Tutor', 'tutor_oferta(oferta_id, tutor_cpf)'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'componente_curricular_id' => 'Componente Curricular',
            'ano' => 'Ano',
            'mes' => 'Mês',
            'data_inicio' => 'Data de início',
            'link_moodle' => 'Link da oferta no Moodle',
            'codigo_moodle' => 'Código da oferta no Moodle',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id);
        $criteria->compare('componente_curricular_id', $this->componente_curricular_id);
        $criteria->compare('ano', $this->ano);
        $criteria->compare('mes', $this->mes);
        $criteria->compare('data_inicio', $this->data_inicio);
        $criteria->compare('link_moodle', $this->link_moodle);
        $criteria->compare('codigo_moodle', $this->codigo_moodle);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function asArray($semCampos = array())
    {
        $haInscricoesNestaOferta = count($this->inscricoes) > 0;

        $docentesArray = array();
        if (!in_array('docentes', $semCampos)) {
            $docentesArray = array_map(function($docente) {
                return $docente->asArraySemOfertasEBolsas();
            }, $this->docentes);
        }

        $tutoresArray = array();
        if (!in_array('tutores', $semCampos)) {
            $tutoresArray = array_map(function($tutor) {
                return $tutor->asArraySemOfertasEBolsas();
            }, $this->tutores);
        }

        $inscricoesArray = array();
        if (!in_array('inscricoes', $semCampos)) {
            $inscricoesArray = array_map(function($inscricao) {
                return $inscricao->asNome();
            }, $this->inscricoes);
        }

        return array(
            'id' => $this->id,
            'ano' => $this->ano,
            'mes' => $this->mes,
            'nome' => $this->recuperarNome(),
            'turma' => $this->turma,
            'data_inicio' => $this->data_inicio,
            'linkMoodle' => $this->link_moodle,
            'codigoMoodle' => $this->codigo_moodle,

            // Campos adicionais
            'podeSerDeletada' => !$haInscricoesNestaOferta,
            // Uma oferta é bloqueada se seu mês já passou
            'bloqueada' => $this->antesDeHoje($this->mes, $this->ano),
            'nomesDocentes' => $this->formatarNomesDocentes(),
            'selecionada' => false,

            // Campos relacionados
            'componente' => $this->componenteCurricular->asArray(),
            'docentes' => $docentesArray,
            'tutores' => $tutoresArray,
            'inscricoes' => $inscricoesArray,
            'numeroDeInscricoesAtivas' => $this->numeroDeInscricoesAtivas(),
        );
    }

    private function formatarNomesDocentes()
    {
        $nomesDocentes = array_map(function($docente) {
            return $docente->nomeCompleto;
        }, $this->docentes);
        return implode(', ', $nomesDocentes);
    }

    public function asArraySemInscricoes()
    {
        return $this->asArray(array('inscricoes'));
    }

    public function asArraySemDadosRelacionados()
    {
        return $this->asArray(array('inscricoes', 'docentes', 'tutores'));
    }

    /**
     * Retorna verdadeiro se uma determinada data vem antes do mês autal.
     *
     * @return boolean
     */
    private function antesDeHoje($mes, $ano)
    {
        $mesesHoje = date("Y") * 12 + date("m");
        $mesesData = $ano * 12 + $mes;
        return $mesesData < $mesesHoje;
    }

    public function estaNoPassado()
    {
        $mesesHoje = date("Y") * 12 + date("m");
        $mesesData = $this->ano * 12 + $this->mes;
        return $mesesData < $mesesHoje;
    }

    public static function fromJson($stringJson)
    {
        $objetoJson = json_decode($stringJson);

        $oferta = new Oferta();
        foreach ($oferta->getAttributes() as $atributo => $valor) {
            if (isset($objetoJson->$atributo)) {
                $oferta->$atributo = $objetoJson->$atributo;
            }
        }

        $outrosAtributos = array(
            'dataInicio' => 'data_inicio',
            'codigoMoodle' => 'codigo_moodle',
            'linkMoodle' => 'link_moodle',
        );
        foreach ($outrosAtributos as $atributoInterface => $atributoClasse) {
            if (isset($objetoJson->$atributoInterface)) {
                $oferta->$atributoClasse = $objetoJson->$atributoInterface;
            }
        }

        $oferta->componente_curricular_id = $objetoJson->componente->id;
        $oferta->docentesArray = array_map(function($docente) {
            return $docente->cpf;
        }, $objetoJson->docentes);
        $oferta->tutoresArray = array_map(function($tutor) {
            return $tutor->cpf;
        }, $objetoJson->tutores);

        return $oferta;
    }

    public function recuperarNomesDeDocentes()
    {
        $nomesDocentes = array_map(function($docente) {
            return "{$docente->nome} {$docente->sobrenome}";
        }, $this->docentes);
        return implode(', ', $nomesDocentes);
    }

    public function recuperarNome()
    {
        return "{$this->componenteCurricular->nome} {$this->mes}/{$this->ano}";
    }

    public function recuperarNomeParaArquivo()
    {
        return str_replace(' ', '_', $this->recuperarNome());
    }

    /**
     * Mais de uma oferta pode ter o mesmo código no Moodle (p. ex. Metodologia de Pesquisa 1),
     * então temos que buscar todas as ofertas que tenham o mesmo código.
     */
    public function findAllByCodigoMoodle($codigoMoodle)
    {
        return Oferta::model()->findAllByAttributes(array(
            'codigo_moodle' => $codigoMoodle
        ));
    }

    public function __toString()
    {
        return "[{$this->componenteCurricular->nome} {$this->mes}/{$this->ano}]";
    }

    public function associarDocente(Docente $docente)
    {
        $docenteOferta = new DocenteOferta();
        $docenteOferta->docente_cpf = $docente->cpf;
        $docenteOferta->oferta_id = $this->id;
        return $docenteOferta->estaNoBanco() ? false : $docenteOferta->save();
    }

    public function associarDocenteCpf($docenteCpf)
    {
        $docenteOferta = new DocenteOferta();
        $docenteOferta->docente_cpf = $docenteCpf;
        $docenteOferta->oferta_id = $this->id;
        return $docenteOferta->estaNoBanco() ? false : $docenteOferta->save();
    }

    public function associarTutor(Tutor $tutor)
    {
        $tutorOferta = new TutorOferta();
        $tutorOferta->tutor_cpf = $tutor->cpf;
        $tutorOferta->oferta_id = $this->id;
        return $tutorOferta->estaNoBanco() ? false : $tutorOferta->save();
    }

    public function associarTutorCpf($tutorCpf)
    {
        $tutorOferta = new TutorOferta();
        $tutorOferta->tutor_cpf = $tutorCpf;
        $tutorOferta->oferta_id = $this->id;
        return $tutorOferta->estaNoBanco() ? false : $tutorOferta->save();
    }

    public function inscreverAluno(Inscricao $inscricao)
    {
        $inscricaoOferta = new InscricaoOferta();
        $inscricaoOferta->oferta_id = $this->id;
        $inscricaoOferta->inscricao_id = $inscricao->id;
        return $inscricaoOferta->estaNoBanco() ? false : $inscricaoOferta->save();
    }

    public function numeroDeInscricoesAtivas()
    {
        return array_reduce($this->inscricoes, function($carry, $inscricao) {
            return $carry + ($inscricao->statusAlunoAtivo() ? 1 : 0);
        }, 0);
    }

    public function recuperarPeriodo()
    {
        $mes = $this->mes < 10 ? '0' . $this->mes : $this->mes;
        return "01/{$mes}/{$this->ano} a 31/{$mes}/{$this->ano}";
    }

    public function ehTutorNestaOferta($cpf)
    {
        $cpfTutores = array_map(function($tutor) {
            return $tutor->cpf;
        }, $this->tutores);
        return in_array($cpf, $cpfTutores);
    }

    public function recuperarDataInicio()
    {
        if ($this->data_inicio) return $this->data_inicio;
        $mes = $this->mes < 10 ? '0' . $this->mes : $this->mes;
        return "{$this->ano}-{$mes}-01";
    }

    public function recuperarDataFinal()
    {
        $dataInicioOferta = date('Y-m-d', strtotime($this->recuperarDataInicio()));
        $dataFim = date('Y-m-d', strtotime($dataInicioOferta . ' + 1 month'));
        $dataFim = date('Y-m-d', strtotime($dataFim . ' - 1 day'));
        return $dataFim;
    }

    public function ehProjetoIntegrador()
    {
        return $this->componenteCurricular->ehProjetoIntegrador();
    }

    public function recuperarOfertaProjetoIntegrador()
    {
        $componenteProjetoIntegrador = ComponenteCurricular::model()->recuperarComponenteProjetoIntegrador();
        return Oferta::model()->findByAttributes(['componente_curricular_id' => $componenteProjetoIntegrador->id]);
    }

    public function recuperarOfertasDoPresenteEFuturo()
    {
        [ $mes, $ano ] = CalendarioHelper::mesEAnoAtuais();
        $mesesAtual = $mes + $ano * 12;
        $criteria = new CDbCriteria();
        $criteria->addCondition("mes + ano * 12 >= {$mesesAtual}");
        return Oferta::model()->findAllByAttributes([], $criteria);
    }
}
