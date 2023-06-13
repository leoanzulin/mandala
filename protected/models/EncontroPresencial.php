<?php

/**
 * This is the model class for table "encontro_presencial".
 *
 * The followings are the available columns in table 'encontro_presencial':
 * @property int $id
 * @property string $tipo
 * @property string $local
 * @property string $data
 * @property string $atividades
 * @property boolean $ativo
 * @property string $criado_em
 * @property string $atualizado_em
 * @property string $deletado_em
 *
 * The followings are the available model relations:
 * @property Inscricao[] $alunos
 */
class EncontroPresencial extends ActiveRecord
{
    public function tableName()
    {
        return 'encontro_presencial';
    }

    public function rules()
    {
        return array(
            array('tipo, local, data, atividades', 'required'),
            array('tipo, local, data', 'length', 'max' => 256),
            array('id, tipo, local, data, atividades, ativo', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'alunos' => array(self::MANY_MANY, 'Inscricao', 'encontro_presencial_inscricao(encontro_presencial_id, inscricao_id)', 'order' => 'nome, sobrenome'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('tipo', $this->tipo, true);
        $criteria->compare('local', $this->local, true);
        $criteria->compare('data', $this->data, true);
        $criteria->compare('atividades', $this->atividades, true);
        $criteria->compare('ativo', true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 50
            ),
            'sort' => array(
                'attributes' => array(
                    'data' => array(
                        'asc' => 'data',
                        'desc' => 'data DESC',
                    ),
                ),
                'defaultOrder' => array('data' => false),
            ),
        ));
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function __toString()
    {
        return "[Encontro presencial {$this->id} {$this->local} {$this->data}]";
    }

    public function getResponsaveis()
    {
        $responsaveis = [];
        $relacionados = EncontroPresencialResponsavel::model()
            ->findAllByAttributes([ 'encontro_presencial_id' => $this->id ]);
        foreach ($relacionados as $responsavel) {
            if ($responsavel->docente) {
                $responsaveis[] = [
                    'id' => "docente_{$responsavel->docente->cpf}",
                    'nome' => $responsavel->docente->nomeCompleto,
                ];
            } else if ($responsavel->tutor) {
                $responsaveis[] = [
                    'id' => "tutor_{$responsavel->tutor->cpf}",
                    'nome' => $responsavel->tutor->nomeCompleto,
                ];
            } else if ($responsavel->r_colaborador) {
                $responsaveis[] = [
                    'id' => "colaborador_{$responsavel->r_colaborador->cpf}",
                    'nome' => $responsavel->r_colaborador->nomeCompleto,
                ];
            }
        }
        return $responsaveis;
    }

    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            $this->atualizado_em = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    public function desativar()
    {
        $this->deletado_em = date('Y-m-d H:i:s');
        $this->ativo = false;
        $this->save();
    }

}
