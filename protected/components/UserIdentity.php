<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	private $_id;
	public function authenticate()
	{
			#$record=User::model()->findByAttributes(array('user_name'=>$this->username));
			$record=User::model()->find('LOWER(user_name)=?',array(strtolower($this->username)));
			
			if($record===null)
				$this->errorCode=self::ERROR_USERNAME_INVALID;
			else if(!$record->validatePassword($this->password))
				$this->errorCode=self::ERROR_PASSWORD_INVALID;
			else
			{
				$this->_id=$record->user_id;
				# save some useful information
				$this->setState('lastlogin', $record->user_lastlogin);
				$this->setState('username', $record->user_name);
				
				$this->errorCode=self::ERROR_NONE;
				
			}
			return !$this->errorCode;
		}
	 
		public function getId()
		{
			return $this->_id;
		}
}
