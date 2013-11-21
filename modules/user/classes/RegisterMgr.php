<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Copyright (c) 2010, Demian Turner                                         |
// | All rights reserved.                                                      |
// |                                                                           |
// | Redistribution and use in source and binary forms, with or without        |
// | modification, are permitted provided that the following conditions        |
// | are met:                                                                  |
// |                                                                           |
// | o Redistributions of source code must retain the above copyright          |
// |   notice, this list of conditions and the following disclaimer.           |
// | o Redistributions in binary form must reproduce the above copyright       |
// |   notice, this list of conditions and the following disclaimer in the     |
// |   documentation and/or other materials provided with the distribution.    |
// | o The names of the authors may not be used to endorse or promote          |
// |   products derived from this software without specific prior written      |
// |   permission.                                                             |
// |                                                                           |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS       |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT         |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR     |
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,     |
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT          |
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,     |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE     |
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.      |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Seagull 1.0                                                               |
// +---------------------------------------------------------------------------+
// | RegisterMgr.php                                                           |
// +---------------------------------------------------------------------------+
// | Author: Demian Turner <demian@phpkitchen.com>                             |
// +---------------------------------------------------------------------------+
// $Id: RegisterMgr.php,v 1.38 2005/06/05 23:14:43 demian Exp $

require_once SGL_MOD_DIR . '/user/classes/LoginMgr.php';
require_once SGL_MOD_DIR . '/user/classes/UserDAO.php';
require_once SGL_CORE_DIR . '/Observer.php';
require_once SGL_CORE_DIR . '/Emailer.php';
require_once 'Validate.php';
require_once 'DB/DataObject.php';
include_once SGL_MOD_DIR  . '/user/classes/Image.php';
include_once SGL_CORE_DIR . '/Image.php';

/**
 * Manages User objects.
 *
 * @package User
 * @author  Demian Turner <demian@phpkitchen.com>
 * @version $Revision: 1.38 $
 */
class RegisterMgr extends SGL_Manager
{
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle    = 'Register';
        $this->template		= 'individual.html';
        $this->da           =  UserDAO::singleton();
        $this->institutionalTheme = 'institutional';
        $this->individualTheme = 'individual';

        $this->_aActionsMapping =  array(
            'add'       => array('add'),
            'insert'    => array('insert', 'redirectToDefault'),
        );
    }

    function validate($req, $input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $this->validated    = true;
        $input->error       = array();
        $input->template	= $this->template;
        $input->pageTitle   = $this->pageTitle;
        $input->masterTemplate = $this->masterTemplate;
        $input->sortBy      = SGL_Util::getSortBy($req->get('frmSortBy'), SGL_SORTBY_USER);
        $input->sortOrder   = SGL_Util::getSortOrder($req->get('frmSortOrder'));
        $input->action      = ($req->get('action')) ? $req->get('action') : 'add';
        $input->submitted   = $req->get('submitted');
        $input->userID      = $req->get('frmUserID');
        $input->aDelete     = $req->get('frmDelete');
        $input->user        = (object)$req->get('user');
        $input->regGrp      = $req->get('regGrp');
        $input->role        = $req->get('role');
        $input->aImage 		= $req->get('fImage');
        $input->aLogo 		= $req->get('fLogo');
        $input->branch      = (object)$req->get('branch');
        $input->aBranchImg	= $req->get('fBranchImage');
        
        
        //  get referer details if present
        $input->redir = $req->get('redir');

        $aErrors = array();
        if (($input->submitted && $input->action != 'changeUserStatus')
                || in_array($input->action, array('insert', 'update'))) {
            $v = new Validate();
            if (empty($input->user->username)) {
                $input->user->username = $input->user->email;
            } /* else {
                //  username must be at least 5 chars
                if (!$v->string($input->user->username, array(
                        'format' => VALIDATE_NUM . VALIDATE_ALPHA, 'min_length' => 5 ))) {
                    $aErrors['username'] = 'username min length';
                }
                //  username must be unique
                $msg = 'This username already exist in the DB, please choose another';
                //      on insert
                if ($input->action == 'insert'
                        && !$this->da->isUniqueUsername($input->user->username)) {
                    $aErrors['username'] = $msg;
                }
                //      on update
                if ($input->action == 'update'
                        && !empty($input->user->username_orig)
                        && $input->user->username_orig != $input->user->username
                        && !$this->da->isUniqueUsername($input->user->username)) {
                    $aErrors['username'] = $msg;
                }
            }
            //  only verify password and uniqueness of username/email on inserts
            if ($input->action != 'update') {
                if (empty($input->user->passwd)) {
                    $aErrors['passwd'] = 'You must enter a password';
                } elseif (!$v->string($input->user->passwd, array('min_length' => 5, 'max_length' => 10 ))) {
                    $aErrors['passwd'] = 'Password must be between 5 to 10 characters';
                }
                if (empty($input->user->password_confirm)) {
                    $aErrors['password_confirm'] = 'Please confirm password';
                } elseif ($input->user->passwd != $input->user->password_confirm) {
                    $aErrors['password_confirm'] = 'Passwords are not the same';
                }
            } */

            $emailNotUniqueMsg = 'This email already exist in the DB, please choose another';
            if (empty($input->user->email)) {
                $aErrors['email'] = 'You must enter your email';
            } elseif (!$v->email($input->user->email)) {
                $aErrors['email'] = 'Your email is not correctly formatted';

            //  email must be unique
            } elseif ($input->action == 'insert'
                    && !$this->da->isUniqueEmail($input->user->email)) {
                $aErrors['email'] = $emailNotUniqueMsg;
            } elseif ($input->action == 'update'
                    && !empty($input->user->email_orig)
                    && $input->user->email_orig != $input->user->email
                    && !$this->da->isUniqueEmail($input->user->email)) {
                $aErrors['email'] = $emailNotUniqueMsg;
            }
            // check for mail header injection
            if (!empty($input->user->email)) {
                $input->user->email =
                    SGL_Emailer::cleanMailInjection($input->user->email);
            }
            // check for mail header injection
            if (!empty($input->user->email_2)) {
            	$input->user->email_2 =
            	SGL_Emailer::cleanMailInjection($input->user->email);
            }

            //echo '<pre>';print_r($aErrors);echo '</pre>';die;
            //  check for hacks - only admin user can set certain attributes
            if ((SGL_Session::getRoleId() != SGL_ADMIN
                    && count(array_filter(array_flip($req->get('user')), array($this, 'containsDisallowedKeys'))))) {
                $msg = 'Hack attempted by ' .$_SERVER['REMOTE_ADDR'] . ', IP logged';
                if (SGL_Session::getRoleId() > SGL_GUEST) {
                    $msg .= ', user id ' . SGL_Session::getUid();
                }
                SGL_Session::destroy();
                SGL::raiseMsg($msg, false);
                SGL::logMessage($msg, PEAR_LOG_CRIT);

                $input->template = 'error.html';
                $this->validated = false;
                return false;
            }
        }
        
        //  if errors have occured
        if (is_array($aErrors) && count($aErrors)) {
            SGL::raiseMsg('Please fill in the indicated fields');
            $input->error = $aErrors;
            $input->template = 'userAdd.html';
            if (isset($input->regGrp))
            	switch ($input->regGrp)
            	{
            		case SGL_INSTITUTIONAL:
            			$input->template = $this->institutionalTheme . '.html';
            			break;
            		case SGL_INDIVIDUAL:
            			$input->template = $this->individualTheme . '.html';
            			break;
            	}
            	
            $this->validated = false;
        }

        //  check if reg disabled
        if (!SGL_Config::get('RegisterMgr.enabled')
                && strtolower(get_class($this)) == strtolower(__CLASS__)) {
            SGL::raiseMsg('Registration has been disabled');
            $input->template = 'error.html';
            $this->validated = false;
        }
    }

    function containsDisallowedKeys($var)
    {
        $disAllowedKeys = array('role_id', 'organisation_id', 'is_acct_active');
        return in_array($var, $disAllowedKeys);
    }

    function display($output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        //  set flag to we can share add/edit templates
        if ($output->action == 'add' || $output->action == 'insert') {
            $output->isAdd = true;
        }

        //  build country/state select boxes unless any of following methods
        $aDisallowedMethods = array('list', 'reset', 'passwdEdit', 'passwdUpdate',
            'requestPasswordReset', 'editPrefs');
        if (!in_array($output->action, $aDisallowedMethods)) {
            $output->states = SGL::loadRegionList('states');
            $output->countries = SGL::loadRegionList('countries');
            $output->aSecurityQuestions = SGL_String::translate('aSecurityQuestions');
        }

        $sessId = SGL_Session::getId();
        $output->addJavascriptFile(array(
            'js/scriptaculous/lib/prototype.js',
            'js/scriptaculous/src/effects.js'
        ));
    }

    function _cmd_add($input, $output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->template = $this->template;
        $output->role = SGL_INDIVIDUAL;
        $output->institutional = SGL_INSTITUTIONAL;
        $output->individual = SGL_INDIVIDUAL;
        
        if (isset($input->role))
        	$output->role = $input->role;
        if (isset($input->regGrp))
        {
           	switch ($input->regGrp)
            {
            	case SGL_INSTITUTIONAL:
            		$output->template = $this->institutionalTheme . '.html';
            		$output->pageTitle = ucfirst($this->institutionalTheme);
            		$output->role = SGL_INSTITUTIONAL;
            		break;
            	case SGL_INDIVIDUAL:
            		$output->template = $this->individualTheme . '.html';
            		$output->pageTitle = ucfirst($this->individualTheme);
            		$output->role = SGL_INDIVIDUAL;
            		break;
            }
        }
        $output->user = DB_DataObject::factory($this->conf['table']['user']);
        $output->user->password_confirm = (isset($input->user->password_confirm)) ?
            $input->user->password_confirm : '';
		$this->_editDisplay($input, $output);
    }

    function _cmd_insert($input, $output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $addUser = new User_AddUser($input, $output);
        $aObservers = explode(',', $this->conf['RegisterMgr']['observers']);
        foreach ($aObservers as $observer) {
            $path = SGL_MOD_DIR . "/user/classes/observers/$observer.php";
            if (is_file($path)) {
                require_once $path;
                $addUser->attach(new $observer());
            }
        }
        //  returns id for new user
        $output->uid = $addUser->run();
        SGL_HTTP::redirect(array('moduleName' => 'default', 'managerName' => 'default'));
    }
    
    function _editDisplay($input, $output)
    {
    	$output->aCats 		= array('cat1', 'cat2', 'cat3');
    	$output->aVills 	= array('vill1', 'vill2', 'vill3');
    	$output->aCity 		= array('city1', 'city2', 'city3');
    	$output->aRegion 	= array('region1', 'region2', 'region3');
    }
}

class User_AddUser extends SGL_Observable
{
	var $cImage;
    function User_AddUser($input, $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->cImage = Image::singleton();
    }

    function run()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        //  get default values for new users
        $this->conf = $this->input->getConfig();
        $defaultRoleId = $this->conf['RegisterMgr']['defaultRoleId'];
        $defaultOrgId  = $this->conf['RegisterMgr']['defaultOrgId'];

        $da =  UserDAO::singleton();
        $oUser = $da->getUserById();
        $oUser->setFrom($this->input->user);
        $oUser->passwdClear = $pass = $this->generatePassword();
        $oUser->passwd = md5($pass);
        $oUser->temp_pass = $pass;

        if ($this->conf['RegisterMgr']['autoEnable']) {
            $oUser->is_acct_active = 1;
        }
        
        $oUser->has_branch = (isset($this->input->user->has_branch)) ? 1 : 0;
        $oUser->role_id = (isset($this->input->role)) ? $this->input->role : $defaultRoleId;
        $oUser->organisation_id = $defaultOrgId;
        $oUser->date_created = $oUser->last_updated = SGL_Date::getTime();
        $oUser->telephone_1 = str_replace(' ', '', $this->input->user->telephone_1);
        $oUser->telephone_2 = str_replace(' ', '', $this->input->user->telephone_2);
        $oUser->fax = str_replace(' ', '', $this->input->user->fax);
        $oUser->mobile = str_replace(' ', '', $this->input->user->mobile);
        
        if(isset($this->input->aImage['name']) && $this->input->aImage['name'] != "") {
        	$this->input->aImage['name'] = $this->cImage->generateUniqueFileName($this->input->aImage['name']);
        	$this->cImage->uploadImage($this->input->aImage['name'], $this->input->aImage['tmp_name']);
        	$oUser->image = $this->input->aImage['name'];
        }
        if(isset($this->input->aLogo['name']) && $this->input->aLogo['name'] != "") {
        	$this->input->aLogo['name'] = $this->cImage->generateUniqueFileName($this->input->aLogo['name']);
        	$this->cImage->uploadImage($this->input->aLogo['name'], $this->input->aLogo['tmp_name']);
        	$oUser->logo = $this->input->aLogo['name'];
        }
        
        $success = $da->addUser($oUser);

        //  make user object available to observers
        $this->oUser = $oUser;

        if ($success) {
            //  set user id for use in observers
            $this->oUser->usr_id = $success;
            //  invoke observers
            $this->notify();
            $ret = $success;
            SGL::raiseMsg('user successfully registered', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('There was a problem inserting the record',
                SGL_ERROR_NOAFFECTEDROWS);
            $ret = false;
        }
        return $ret;
    }
    
    
    function addBranches()
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	//  get default values for new users
    	$this->conf = $this->input->getConfig();
    	$defaultRoleId = $this->conf['RegisterMgr']['defaultRoleId'];
    	$defaultOrgId  = $this->conf['RegisterMgr']['defaultOrgId'];
    
    	$da =  UserDAO::singleton();
    	$oUser = $da->getUserById();
    	$oUser->setFrom($this->input->user);
    	$oUser->passwdClear = $pass = $this->generatePassword();
    	$oUser->passwd = md5($pass);
    
    	if ($this->conf['RegisterMgr']['autoEnable']) {
    		$oUser->is_acct_active = 1;
    	}
    	$oUser->role_id = $defaultRoleId;
    	$oUser->organisation_id = $defaultOrgId;
    	$oUser->date_created = $oUser->last_updated = SGL_Date::getTime();
    
    	if(isset($input->aImage['name']) && $input->aImage['name'] != "") {
    		$input->aImage['name'] = $this->cImage->generateUniqueFileName($input->aImage['name']);
    		$this->cImage->uploadImage($input->aImage['name'], $input->aImage['tmp_name']);
    		$oUser->image = $input->aImage['name'];
    	}
    	if(isset($input->aLogo['name']) && $input->aLogo['name'] != "") {
    		$input->aLogo['name'] = $this->cImage->generateUniqueFileName($input->aLogo['name']);
    		$this->cImage->uploadImage($input->aLogo['name'], $input->aLogo['tmp_name']);
    		$oUser->logo = $input->aLogo['name'];
    	}
    
    	$success = $da->addUser($oUser);
    
    	//  make user object available to observers
    	$this->oUser = $oUser;
    
    	if ($success) {
    		//  set user id for use in observers
    		$this->oUser->usr_id = $success;
    		//  invoke observers
    		$this->notify();
    		$ret = $success;
    		SGL::raiseMsg('user successfully registered', true, SGL_MESSAGE_INFO);
    	} else {
    		SGL::raiseError('There was a problem inserting the record',
    				SGL_ERROR_NOAFFECTEDROWS);
    		$ret = false;
    	}
    	return $ret;
    }
	
    function generatePassword($length=9, $strength=0) {
    	$vowels = 'aeuy';
    	$consonants = 'bdghjmnpqrstvz';
    	if ($strength & 1) {
    		$consonants .= 'BDGHJLMNPQRSTVWXZ';
    	}
    	if ($strength & 2) {
    		$vowels .= "AEUY";
    	}
    	if ($strength & 4) {
    		$consonants .= '23456789';
    	}
    	if ($strength & 8) {
    		$consonants .= '@#$%';
    	}
    
    	$password = '';
    	$alt = time() % 2;
    	for ($i = 0; $i < $length; $i++) {
    		if ($alt == 1) {
    			$password .= $consonants[(rand() % strlen($consonants))];
    			$alt = 0;
    		} else {
    			$password .= $vowels[(rand() % strlen($vowels))];
    			$alt = 1;
    		}
    	}
    	return $password;
    }

}
?>
