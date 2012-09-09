<?php

/**
 * This is the model base class for the table "evernote_authorization".
 *
 * Columns in table "evernote_authorization" available as properties of the model:
 * @property string $id
 * @property string $requestToken
 * @property string $requestTokenSecret
 * @property string $oauthVerifier
 * @property string $accessToken
 * @property string $noteStoreUrl
 * @property string $webApiUrlPrefix
 * @property string $tokenExpires
 * @property string $user_id
 * @property string $created
 *
 * Relations of table "evernote_authorization" available as properties of the model:
 * @property User $user
 */
abstract class BaseEvernoteAuthorization extends CActiveRecord
{

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'evernote_authorization';
	}

	public function rules()
	{
		return array(
			array('user_id', 'required'),
			array('requestToken, requestTokenSecret, oauthVerifier, accessToken, noteStoreUrl, webApiUrlPrefix, tokenExpires, created', 'default', 'setOnEmpty' => true, 'value' => null),
			array('requestToken, accessToken, noteStoreUrl, webApiUrlPrefix', 'length', 'max' => 255),
			array('requestTokenSecret, oauthVerifier', 'length', 'max' => 45),
			array('tokenExpires, user_id', 'length', 'max' => 20),
			array('created', 'safe'),
			array('id, requestToken, requestTokenSecret, oauthVerifier, accessToken, noteStoreUrl, webApiUrlPrefix, tokenExpires, user_id, created', 'safe', 'on' => 'search'),
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
			'requestToken' => Yii::t('app', 'Request Token'),
			'requestTokenSecret' => Yii::t('app', 'Request Token Secret'),
			'oauthVerifier' => Yii::t('app', 'Oauth Verifier'),
			'accessToken' => Yii::t('app', 'Access Token'),
			'noteStoreUrl' => Yii::t('app', 'Note Store Url'),
			'webApiUrlPrefix' => Yii::t('app', 'Web Api Url Prefix'),
			'tokenExpires' => Yii::t('app', 'Token Expires'),
			'user_id' => Yii::t('app', 'User'),
			'created' => Yii::t('app', 'Created'),
		);
	}

	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('t.requestToken', $this->requestToken, true);
		$criteria->compare('t.requestTokenSecret', $this->requestTokenSecret, true);
		$criteria->compare('t.oauthVerifier', $this->oauthVerifier, true);
		$criteria->compare('t.accessToken', $this->accessToken, true);
		$criteria->compare('t.noteStoreUrl', $this->noteStoreUrl, true);
		$criteria->compare('t.webApiUrlPrefix', $this->webApiUrlPrefix, true);
		$criteria->compare('t.tokenExpires', $this->tokenExpires, true);
		$criteria->compare('t.user_id', $this->user_id);
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
