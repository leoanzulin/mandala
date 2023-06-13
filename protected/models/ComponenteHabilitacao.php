<?php

/**
 * This is the model class for table "componente_habilitacao".
 *
 * The followings are the available columns in table 'componente_habilitacao':
 * @property string $componente_curricular_id
 * @property string $habilitacao_id
 * @property integer $prioridade
 */
class ComponenteHabilitacao extends CActiveRecord
{

    public function tableName()
    {
        return 'componente_habilitacao';
    }

    public function rules()
    {
        return array(
            array('componente_curricular_id, habilitacao_id, prioridade', 'required'),
            array('prioridade', 'numerical', 'integerOnly' => true),
        );
    }

    public function relations()
    {
        return array(
        );
    }

    public function attributeLabels()
    {
        return array(
            'componente_curricular_id' => 'Componente Curricular',
            'habilitacao_id' => 'Habilitacao',
            'prioridade' => 'Prioridade',
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function findByComponente($componenteId)
    {
        $sql = "
            SELECT
                c_h.habilitacao_id,
                c_h.prioridade,
                h.nome AS habilitacao_nome,
                h.cor
            FROM
                componente_habilitacao c_h
                JOIN habilitacao h ON h.id = c_h.habilitacao_id
            WHERE
                c_h.componente_curricular_id = {$componenteId}
            ;
        ";
        $componenteHabilitacoes = Yii::app()->db->createCommand($sql)->queryAll();

        usort($componenteHabilitacoes, function($a, $b) {
            $order = [ 3, 4, 1, 2, 5 ];
            foreach ($order as $id) {
                if ($a['habilitacao_id'] == $id) return -1;
                if ($b['habilitacao_id'] == $id) return 1;
            }
            return $a['habilitacao_id'] < $b['habilitacao_id'] ? -1 : 1;
        });

        foreach ($componenteHabilitacoes as &$componenteHabilitacao) {
            $prioridade = (int)$componenteHabilitacao['prioridade'];

            $componenteHabilitacao['letra'] = Constantes::PRIORIDADE_PARA_LETRA($prioridade);
            if ($prioridade > Constantes::PRIORIDADE_OPTATIVA) {
                $componenteHabilitacao['cor'] = '#FFFFFF';
            }
        }

        return $componenteHabilitacoes;
    }

}
