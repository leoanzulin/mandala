<?php

/**
 * This is the model class for table "offer".
 *
 * The followings are the available columns in table 'offer':
 * @property integer $offer_discipline_id
 * @property string $offer_teacher_id
 * @property string $offer_start_date
 * @property string $offer_end_date
 *
 * The followings are the available model relations:
 * @property Users $teacher
 * @property Discipline $discipline
 * @property OfferStudent[] $offerStudents
 * @property OfferStudent[] $offerStudents1
 * @property OfferStudent[] $offerStudents2
 * @property OfferStudent[] $offerStudents3
 */
class Offer extends ActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'offer';
    }

    public function rules()
    {
        return array(
            array('offer_discipline_id, offer_teacher_id, offer_start_date, offer_end_date', 'required'),
            array('offer_discipline_id', 'numerical', 'integerOnly' => true),
            array('offer_teacher_id', 'length', 'max' => 11),
            array('offer_discipline_id, offer_teacher_id, offer_start_date, offer_end_date', 'safe', 'on' => 'search'),
            
            array('offer_start_date, offer_end_date', 'date', 'format' => 'dd/MM/yyyy'),
            
            array('offer_start_date', 'comparaDatas',
                'atributoComparacao' => 'offer_end_date',
                'operador' => '<=',
                'mensagem' => 'A data de início deve ser menor ou igual a data de término'
            ),
        );
    }

    public function relations()
    {
        return array(
            'teacher' => array(self::BELONGS_TO, 'Users', 'offer_teacher_id'),
            'discipline' => array(self::BELONGS_TO, 'Discipline', 'offer_discipline_id'),
            'offerStudents' => array(self::HAS_MANY, 'OfferStudent', 'os_offer_start_date'),
            'offerStudents1' => array(self::HAS_MANY, 'OfferStudent', 'os_offer_teacher_id'),
            'offerStudents2' => array(self::HAS_MANY, 'OfferStudent', 'os_offer_discipline_id'),
            'offerStudents3' => array(self::HAS_MANY, 'OfferStudent', 'os_offer_end_date'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'offer_discipline_id' => 'Disciplina',
            'offer_teacher_id' => 'Docente',
            'offer_start_date' => 'Data de início',
            'offer_end_date' => 'Data de término',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('offer_discipline_id', $this->offer_discipline_id);
        $criteria->compare('offer_teacher_id', $this->offer_teacher_id, true);
        $criteria->compare('offer_start_date', $this->offer_start_date, true);
        $criteria->compare('offer_end_date', $this->offer_end_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function beforeSave()
    {
        // Verificar se, para a mesma disciplina e docente, já existe uma oferta
        // dentro do período de tempo
        $offers = Offer::model()->findAllByAttributes(array(
            'offer_teacher_id' => $this->offer_teacher_id,
            'offer_discipline_id' => $this->offer_discipline_id,
        ));

        foreach ($offers as $offer) {
            if ($this->offerIntersectsWithCurrentOffer($offer)) {
                // adicionar erro ao $model->errors
                $this->addError('offer_start_date', 'Já existe uma oferta do mesmo professor dentro do período informado.');
                $this->addERror('offer_end_date', 'Já existe uma oferta do mesmo professor dentro do período informado.');
                return false;
            }
        }
        
        return parent::beforeSave();
    }
    
    // testar este método
    private function offerIntersectsWithCurrentOffer(Offer $offer)
    {
        $startDate = $this->convertDateToDatabaseFormat($this->offer_start_date);
        $endDate = $this->convertDateToDatabaseFormat($this->offer_end_date);
        $offerStartDate = $this->convertDateToDatabaseFormat($offer->offer_start_date);
        $offerEndDate = $this->convertDateToDatabaseFormat($offer->offer_end_date);

//        die("$startDate $endDate $offerStartDate $offerEndDate");
        
        if (
                ($offerStartDate <= $startDate && $startDate <= $offerEndDate && $offerEndDate <= $endDate) ||
                ($startDate <= $offerStartDate && $offerStartDate <= $endDate && $endDate <= $offerEndDate) ||
                // contido
                ($startDate <= $offerStartDate && $offerEndDate <= $endDate) ||
                ($offerStartDate <= $startDate && $endDate <= $offerEndDate)
            ) {
            return true;
        }
        return false;
    }

    private function convertDateToDatabaseFormat($date) {
        $parts = explode('/', $date);
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    
}
