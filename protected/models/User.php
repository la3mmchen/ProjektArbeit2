<?php

/**
 * This is the model class for table "tbl_user".
 *
 * The followings are the available columns in table 'tbl_user':
 * @property integer $user_id
 * @property string $user_lastname
 * @property string $user_firstname
 * @property string $user_name
 * @property string $user_livingplace
 * @property string $user_password
 * @property string $user_email
 * @property string $user_role
 * @property integer $user_created
 * @property integer $user_changed
 * @property integer $user_lastlogin
 * @property string $user_picture
 * @property integer $user_notification
 * @property integer $user_publicProfile
 *
 * The followings are the available model relations:
 * @property UserRelations[] $userRelations
 * @property UserRelations[] $userRelations1
 */
class User extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_name, user_password, user_email', 'required'),
			array('user_created, user_changed, user_lastlogin, user_notification, user_publicProfile', 'numerical', 'integerOnly'=>true),
			array('user_lastname, user_firstname, user_name, user_livingplace, user_email', 'length', 'max'=>200),
			array('user_password', 'length', 'max'=>100),
			array('user_role', 'length', 'max'=>30),
			array('user_picture', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, user_lastname, user_firstname, user_name, user_livingplace, user_password, user_email, user_role, user_created, user_changed, user_lastlogin, user_picture, user_notification, user_publicProfile', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'userRel' => array(self::HAS_MANY, 'UserRelations', 'userRel_user1'),
			'userRel2' => array(self::HAS_MANY, 'UserRelations', 'userRel_user2'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id' => 'User',
			'user_lastname' => 'User Lastname',
			'user_firstname' => 'User Firstname',
			'user_name' => 'User Name',
			'user_livingplace' => 'User Livingplace',
			'user_password' => 'User Password',
			'user_email' => 'User Email',
			'user_role' => 'User Role',
			'user_created' => 'User Created',
			'user_changed' => 'User Changed',
			'user_lastlogin' => 'User Lastlogin',
			'user_picture' => 'User Picture',
			'user_notification' => 'User Notification',
			'user_publicProfile' => 'User Public Profile',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('user_lastname',$this->user_lastname,true);
		$criteria->compare('user_firstname',$this->user_firstname,true);
		$criteria->compare('user_name',$this->user_name,true);
		$criteria->compare('user_livingplace',$this->user_livingplace,true);
		$criteria->compare('user_password',$this->user_password,true);
		$criteria->compare('user_email',$this->user_email,true);
		$criteria->compare('user_role',$this->user_role,true);
		$criteria->compare('user_created',$this->user_created);
		$criteria->compare('user_changed',$this->user_changed);
		$criteria->compare('user_lastlogin',$this->user_lastlogin);
		$criteria->compare('user_picture',$this->user_picture,true);
		$criteria->compare('user_notification',$this->user_notification);
		$criteria->compare('user_publicProfile',$this->user_publicProfile);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	/**
	 * Compare passwords
	 */
	protected function beforeSave()
    {
        // If new password is empty, it means it's an update with blank password,
        // so we leave it untouched. 
        // If it contains some value, then let's update the actual password
        if( ! empty( $this->psswd ) )
        {
            $this->user_password = md5($this->psswd);
        }
        return parent::beforeSave();
    }
	
	 /**
     * Validate password
     */
     public function validatePassword($pw) {
		 if ($this->user_password == md5($pw)) 
			return true;
		else 
			return false;
	 }
	
}
