<?php

/**
 * This is the model class for table "authassignment".
 *
 * The followings are the available columns in table 'authassignment':
 * @property string $itemname
 * @property string $userid
 * @property string $bizrule
 * @property string $data
 *
 * The followings are the available model relations:
 * @property Authitem $itemname0
 */
class Authassignment extends CActiveRecord
{

    public function tableName()
    {
        return 'authassignment';
    }

    public function rules()
    {
        return array(
            array('itemname, userid', 'required'),
            array('itemname, userid', 'length', 'max' => 64),
            array('bizrule, data', 'safe'),
            array('itemname, userid, bizrule, data', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'itemname0' => array(self::BELONGS_TO, 'Authitem', 'itemname'),
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}
