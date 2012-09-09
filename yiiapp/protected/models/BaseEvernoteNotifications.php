<?php

/**
 * This is the model base class for the table "evernote_notifications".
 *
 * Columns in table "evernote_notifications" available as properties of the model:
 * @property string $id
 * @property string $userId
 * @property string $guid
 * @property string $reason
 *
 * There are no model relations.
 */
abstract class BaseEvernoteNotifications extends CActiveRecord
{

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'evernote_notifications';
	}

	public function rules()
	{
		return array(
			array('userId', 'unique'),
			array('userId', 'identificationColumnValidator'),
			array('userId, guid, reason', 'default', 'setOnEmpty' => true, 'value' => null),
			array('userId', 'length', 'max' => 20),
			array('guid, reason', 'length', 'max' => 45),
			array('id, userId, guid, reason', 'safe', 'on' => 'search'),
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
			'userId' => Yii::t('app', 'User'),
			'guid' => Yii::t('app', 'Guid'),
			'reason' => Yii::t('app', 'Reason'),
		);
	}

	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('t.userId', $this->userId, true);
		$criteria->compare('t.guid', $this->guid, true);
		$criteria->compare('t.reason', $this->reason, true);

		return new CActiveDataProvider(get_class($this), array(
			    'criteria' => $criteria,
		    ));
	}

	public function get_label()
	{
		return '#' . $this->id;
	}

}
