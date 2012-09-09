<?php

/**
 * This is the model base class for the table "finished_assignment".
 *
 * Columns in table "finished_assignment" available as properties of the model:
 * @property string $id
 * @property string $hitId
 * @property string $guid
 * @property string $created
 *
 * There are no model relations.
 */
abstract class BaseFinishedAssignment extends CActiveRecord
{

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'finished_assignment';
	}

	public function rules()
	{
		return array(
			array('hitId', 'unique'),
			array('hitId', 'identificationColumnValidator'),
			array('hitId, guid, created', 'default', 'setOnEmpty' => true, 'value' => null),
			array('hitId, guid, created', 'length', 'max' => 45),
			array('id, hitId, guid, created', 'safe', 'on' => 'search'),
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
			'id' => Yii::t('app', 'ID'),
			'hitId' => Yii::t('app', 'Hit'),
			'guid' => Yii::t('app', 'Guid'),
			'created' => Yii::t('app', 'Created'),
		);
	}

	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('t.hitId', $this->hitId, true);
		$criteria->compare('t.guid', $this->guid, true);
		$criteria->compare('t.created', $this->created, true);

		return new CActiveDataProvider(get_class($this), array(
			    'criteria' => $criteria,
		    ));
	}

	public function get_label()
	{
		return '#' . $this->id;
	}

}
