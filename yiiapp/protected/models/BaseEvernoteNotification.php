<?php

/**
 * This is the model base class for the table "evernote_notification".
 *
 * Columns in table "evernote_notification" available as properties of the model:
 * @property string $id
 * @property string $guid
 * @property string $reason
 * @property string $user_id
 *
 * Relations of table "evernote_notification" available as properties of the model:
 * @property User $user
 */
abstract class BaseEvernoteNotification extends CActiveRecord
{

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'evernote_notification';
	}

	public function rules()
	{
		return array(
			array('guid', 'unique'),
			array('guid', 'identificationColumnValidator'),
			array('guid, reason', 'default', 'setOnEmpty' => true, 'value' => null),
			array('guid, reason', 'length', 'max' => 45),
			array('id, guid, reason, user_id', 'safe', 'on' => 'search'),
		);
	}

	public function relations()
	{
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('app', 'ID'),
			'guid' => Yii::t('app', 'Guid'),
			'reason' => Yii::t('app', 'Reason'),
			'user_id' => Yii::t('app', 'User'),
		);
	}

	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('t.guid', $this->guid, true);
		$criteria->compare('t.reason', $this->reason, true);
		$criteria->compare('t.user_id', $this->user_id);

		return new CActiveDataProvider(get_class($this), array(
			    'criteria' => $criteria,
		    ));
	}

	public function get_label()
	{
		return '#' . $this->id;
		return '#' . $this->user_id;
	}

}
