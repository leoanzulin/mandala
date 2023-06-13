<?php

/**
 * This is the model class for table "inscricao_oferta".
 *
 * The followings are the available columns in table 'inscricao_oferta':
 * @property string $oferta_id
 * @property string $inscricao_id
 * @property numeric $nota_virtual
 * @property numeric $nota_presencial
 * @property numeric $frequencia
 * @property numeric $media
 * @property string $status
 * @property boolean $confirmada
 */
class InscricaoOferta extends ActiveRecord
{

    const STATUS_APROVADO = 'Aprovado';
    const STATUS_REPROVADO = 'Reprovado';
    const STATUS_TRANCADO = 'Trancado';

    public function tableName()
    {
        return 'inscricao_oferta';
    }

    public function rules()
    {
        return array(
            array('oferta_id, inscricao_id', 'required'),
            array('status', 'length', 'max' => 256),
            array('oferta_id, inscricao_id, nota_virtual, nota_presencial, frequencia, media, status', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'oferta' => array(self::BELONGS_TO, 'Oferta', 'oferta_id'),
            'inscricao' => array(self::BELONGS_TO, 'Inscricao', 'inscricao_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'nota_virtual' => 'Nota virtual',
            'nota_presencial' => 'Nota presencial',
            'frequencia' => 'Frequência',
            'media' => 'Média',
            'oferta_id' => 'Oferta',
            'inscricao_id' => 'Inscricao',
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function asArray()
    {
        return array(
            'oferta_id' => $this->oferta_id,
            'inscricao_id' => $this->inscricao_id,
            'nota_virtual' => $this->nota_virtual,
            'nota_presencial' => $this->nota_presencial,
            'media' => $this->media,
            'frequencia' => $this->frequencia,
            'status' => $this->status,
        );
    }

    /**
     * Muda as vírgulas das notas para pontos se for necessário
     * 
     * @return boolean
     */
    protected function beforeValidate()
    {
        $this->media = $this->transformarVirgulaEmPonto($this->media);
        $this->nota_virtual = $this->transformarVirgulaEmPonto($this->nota_virtual);
        $this->nota_presencial = $this->transformarVirgulaEmPonto($this->nota_presencial);
        $this->frequencia = $this->transformarVirgulaEmPonto($this->frequencia);
        return parent::beforeValidate();
    }

    protected function afterFind()
    {
        $this->media = $this->transformarPontoEmVirgula($this->media);
        $this->nota_virtual = $this->transformarPontoEmVirgula($this->nota_virtual);
        $this->nota_presencial = $this->transformarPontoEmVirgula($this->nota_presencial);
        $this->frequencia = $this->transformarPontoEmVirgula($this->frequencia);
        return parent::afterFind();
    }

    public static function ordenarPorNome(&$inscricoesOferta)
    {
        @usort($inscricoesOferta, function($inscricaoOfertaA, $inscricaoOfertaB) {
            return strcmp(
                $inscricaoOfertaA->inscricao->nomeCompleto, //
                $inscricaoOfertaB->inscricao->nomeCompleto
            );
        });
    }

    public function estaNoBanco()
    {
        return InscricaoOferta::model()->findByPk(array(
            'inscricao_id' => $this->inscricao_id,
            'oferta_id' => $this->oferta_id,
        )) != null;
    }

    /**
     * Encontra uma inscrição em oferta a partir de um número UFSCar de aluno e do
     * código da disciplina no Moodle. Pode existir mais de uma oferta com o mesmo
     * código Moodle, então mais de uma InscricaoOferta pode ser retornada.
     * 
     * @param int $numeroUfscar
     * @param string $codigoMoodle
     * @return InscricaoOferta[]
     */
    public static function encontrarInscricaoOfertas($numeroUfscar, $codigoMoodle)
    {
        $inscricao = Inscricao::model()->findByNumeroUfscar($numeroUfscar);
        $ofertas = Oferta::model()->findAllByCodigoMoodle($codigoMoodle);

        if (empty($inscricao)) {
            Yii::log("Inscrição de número UFSCar {$numeroUfscar} não foi encontrada.", 'error', 'system.models.InscricaoOferta');
            return null;
        }
        if (empty($ofertas)) {
            Yii::log("Oferta com código moodle {$codigoMoodle} não foi encontrada.", 'error', 'system.models.InscricaoOferta');
            return null;
        }

        $inscricaoOfertas = [];
        foreach ($ofertas as $oferta) {
            $inscricaoOferta = InscricaoOferta::model()->findByAttributes(array(
                'oferta_id' => $oferta->id,
                'inscricao_id' => $inscricao->id,
            ));
            if (!empty($inscricaoOferta)) {
                $inscricaoOfertas[] = $inscricaoOferta;
            }
        }

        if (empty($inscricaoOfertas)) {
            Yii::log("{$inscricao} na oferta de código {$codigoMoodle} {$oferta} não foi encontrada.", 'error', 'system.models.InscricaoOferta');
            return null;
        }

        return $inscricaoOfertas;
    }

    public function ehAprovada()
    {
        return $this->media >= Constantes::MEDIA_MINIMA && $this->frequencia >= Constantes::FREQUENCIA_MINIMA;
    }

    public static function foiAprovada($media, $frequencia)
    {
        return $media >= Constantes::MEDIA_MINIMA && $frequencia >= Constantes::FREQUENCIA_MINIMA;
    }

    public static function foiAprovadaStatus($status)
    {
        return $status === self::STATUS_APROVADO;
    }

    public static function STATUS_POSSIVEIS()
    {
        return array(
            '',
            self::STATUS_APROVADO,
            self::STATUS_REPROVADO,
            self::STATUS_TRANCADO
        );
    }

    public function estaNoPassado()
    {
        $oferta = $this->oferta;
        return CalendarioHelper::estaNoPassado($oferta->mes, $oferta->ano);
    }

    public function trancar()
    {
        $this->status = self::STATUS_TRANCADO;
        $this->save();
    }

}
