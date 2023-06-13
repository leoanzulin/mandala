<?php

/**
 * This is the model class for table "inscricao_simulador".
 *
 * The followings are the available columns in table 'inscricao_simulador':
 * @property string $oferta_id
 * @property string $inscricao_id
 */
class InscricaoSimulador extends CActiveRecord
{

    public function tableName()
    {
        return 'inscricao_simulador';
    }

    public function rules()
    {
        return array(
            array('oferta_id, inscricao_id', 'required'),
            array('oferta_id, inscricao_id', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'oferta' => array(self::BELONGS_TO, 'Oferta', 'oferta_id'),
            'inscricao' => array(self::BELONGS_TO, 'Inscricao', 'inscricao_id'),
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('oferta_id', $this->oferta_id, true);
        $criteria->compare('inscricao_id', $this->inscricao_id, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function asArray()
    {
        return array(
            'oferta' => $this->oferta->asArraySemDadosRelacionados(),
            'mes' => $this->oferta->mes,
            'ano' => $this->oferta->ano,
            'inscricao_id' => $this->inscricao_id,
            'bloqueada' => $this->antesDeHoje($this->oferta->mes, $this->oferta->ano),
        );
    }

    /**
     * Retorna verdadeiro se uma determinada data vem antes do mês autal.
     * 
     * @return boolean
     */
    private function antesDeHoje($mes, $ano)
    {
        $diaHoje = date("d");
        $mesHoje = date("m");
        $anoHoje = date("Y");
        $mesesHoje = $anoHoje * 12 + $mesHoje;
        $mesesData = $ano * 12 + $mes;
        if ($mesesData < $mesesHoje) {
            return true;
        }
        if ($mesesData == $mesesHoje) {
            return $diaHoje > 8;
        }
        return false;
    }

    public static function deletarOfertasDaInscricao($inscricao)
    {
        $inscricoesSimulador = InscricaoSimulador::model()->findAllByAttributes(array(
            'inscricao_id' => $inscricao->id,
        ));
        foreach ($inscricoesSimulador as $inscricaoSimulador) {
            $oferta = $inscricaoSimulador->oferta;
            if ($oferta->turma == $inscricao->turma) {
                $inscricaoSimulador->delete();
            }            
        }
    }

}
