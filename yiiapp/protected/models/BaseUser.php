<?php

/**
 * This is the model base class for the table "user".
 *
 * Columns in table "user" available as properties of the model:
 * @property string $id
 * @property string $created
 *
 * Relations of table "user" available as properties of the model:
 * @property EvernoteAuthorization[] $evernoteAuthorizations
 * @property EvernoteNotification[] $evernoteNotifications
 */
abstract class BaseUser extends CActiveRecord
{

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'user';
	}

	public function rules()
	{
		return array(
			array('id', 'unique'),
			array('id', 'identificationColumnValidator'),
			array('id', 'required'),
			array('created', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id', 'length', 'max' => 20),
			array('created', 'safe'),
			array('id, created', 'safe', 'on' => 'search'),
		);
	}

	public function relations()
	{
		return array(
			'evernoteAuthorizations' => array(self::HAS_MANY, 'EvernoteAuthorization', 'user_id'),
			'evernoteNotifications' => array(self::HAS_MANY, 'EvernoteNotification', 'user_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('app', 'ID'),
			'created' => Yii::t('app', 'Created'),
		);
	}

	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id, true);
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
