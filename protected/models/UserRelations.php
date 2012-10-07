<?php

/**
 * This is the model class for table "tbl_userRelations".
 *
 * The followings are the available columns in table 'tbl_userRelations':
 * @property integer $userRel_id
 * @property integer $userRel_user1
 * @property integer $userRel_user2
 * @property integer $userRel_created
 *
 * The followings are the available model relations:
 * @property User $userRelUser1
 * @property User $userRelUser2
 */
class UserRelations extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserRelations the static model class
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
		return 'tbl_userRelations';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userRel_user1, userRel_user2', 'required'),
			array('userRel_user1, userRel_user2, userRel_created', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('userRel_id, userRel_user1, userRel_user2, userRel_created', 'safe', 'on'=>'search'),
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
			'userRelUser1' => array(self::BELONGS_TO, 'User', 'userRel_user1'),
			'userRelUser2' => array(self::BELONGS_TO, 'User', 'userRel_user2'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'userRel_id' => 'User Rel',
			'userRel_user1' => 'User Rel User1',
			'userRel_user2' => 'User Rel User2',
			'userRel_created' => 'User Rel Created',
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

		$criteria->compare('userRel_id',$this->userRel_id);
		$criteria->compare('userRel_user1',$this->userRel_user1);
		$criteria->compare('userRel_user2',$this->userRel_user2);
		$criteria->compare('userRel_created',$this->userRel_created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}