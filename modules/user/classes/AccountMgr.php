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
// | AccountMgr.php                                                            |
// +---------------------------------------------------------------------------+
// | Author: Demian Turner <demian@phpkitchen.com>                             |
// +---------------------------------------------------------------------------+
// $Id: AccountMgr.php,v 1.25 2005/05/17 23:54:52 demian Exp $

require_once SGL_MOD_DIR . '/user/classes/RegisterMgr.php';
require_once SGL_MOD_DIR . '/user/classes/UserDAO.php';
require_once 'DB/DataObject.php';

/**
 * Manages User's account.
 *
 * @package User
 * @author  Demian Turner <demian@phpkitchen.com>
 */
class AccountMgr extends RegisterMgr
{
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle = 'My Account';
        $this->da =  UserDAO::singleton();
        $this->institutionalTheme = 'institutional';
        $this->individualTheme = 'individual';

        $this->_aActionsMapping =  array(
            'edit'          => array('edit'),
            'update'        => array('update', 'redirectToDefault'),
            'viewProfile'   => array('viewProfile'),
            'summary'       => array('summary'),
        );
    }

    function validate($req, &$input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        parent::validate($req, $input);
        $input->action = ($req->get('action')) ? $req->get('action') : 'viewProfile';
        $input->user->is_email_public = (isset($input->user->is_email_public)) ? 1 : 0;
    }

    function display($output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::display($output);

        //  set user's country
        if (isset($output->user) && $output->action == 'viewProfile') {
            $output->user->country = $GLOBALS['_SGL']['COUNTRIES'][$output->user->country];
            $output->user->region = $GLOBALS['_SGL']['STATES'][$output->user->region];
        }
        if ($this->conf['OrgMgr']['enabled']) {
            $output->aOrgs = $this->da->getOrgs();
        }
        $output->aRoles = $this->da->getRoles();
        $output->isAcctActive = (@$output->user->is_acct_active) ? ' checked' : '';
    }

    function _cmd_edit($input, $output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->pageTitle = 'My Profile :: Edit';
        $output->template = 'userAdd.html';
        switch (SGL_Session::getRoleId())
       	{
       		case SGL_INSTITUTIONAL:
       			$output->template = $this->institutionalTheme . '.html';
       			$output->pageTitle = ucfirst($this->institutionalTheme) . ' Profile Edit';
       			$output->role = SGL_INSTITUTIONAL;
       			break;
       		case SGL_INDIVIDUAL:
       			$output->template = $this->individualTheme . '.html';
        		$output->pageTitle = ucfirst($this->individualTheme) . ' Profile Edit';
        		$output->role = SGL_INDIVIDUAL;
       			break;
       	}
        $oUser = DB_DataObject::factory($this->conf['table']['user']);
        $oUser->get(SGL_Session::getUid());
        $output->user = $oUser;
        $output->user->username_orig = $oUser->username;
        $output->user->email_orig = $oUser->email;
        $this->_editDisplay($input, $output);
    }

    function _cmd_update($input, $output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $oUser = DB_DataObject::factory($this->conf['table']['user']);
        $oUser->get(SGL_Session::getUid());
        $original = clone($oUser);
        $oUser->setFrom($input->user);
        $oUser->last_updated = SGL_Date::getTime();
        $oUser->has_branch = (isset($input->user->has_branch)) ? 1 : 0;
        $oUser->telephone_1 = str_replace(' ', '', $input->user->telephone_1);
        $oUser->telephone_2 = str_replace(' ', '', $input->user->telephone_2);
        $oUser->fax = str_replace(' ', '', $input->user->fax);
        $oUser->mobile = str_replace(' ', '', $input->user->mobile);
        
        //echo '<pre>';print_r($oUser);echo '</pre>';die;
        $success = $oUser->update($original);

        if ($success !== false) {
            SGL::raiseMsg('profile successfully updated', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('There was a problem inserting the record',
                SGL_ERROR_NOAFFECTEDROWS);
        }
    }

    function _cmd_viewProfile($input, $output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->template = 'account.html';
        $output->masterTemplate = 'masterLeftCol.html';
        $output->masterLayout = 'layout-navtop-2col_localleft.css';
        switch (SGL_Session::getRoleId())
        {
        	case SGL_INSTITUTIONAL:
        		$output->template = $this->institutionalTheme . 'Profile.html';
        		$output->pageTitle = ucfirst($this->institutionalTheme);
        		$output->role = SGL_INSTITUTIONAL;
        		break;
        	case SGL_INDIVIDUAL:
        		$output->template = $this->individualTheme . 'Profile.html';
        		$output->pageTitle = ucfirst($this->individualTheme);
        		$output->role = SGL_INDIVIDUAL;
        		break;
        }
        
        $userId = SGL_Session::getUid();
    	$query = "
    			SELECT usr.*, c.title AS city, v.title AS village, r.title AS region
    			FROM {$this->conf['table']['user']} AS usr
    			LEFT JOIN {$this->conf['table']['city']} AS c
    			ON c.city_id = usr.city_id
    			LEFT JOIN {$this->conf['table']['region']} AS r
    			ON r.region_id = usr.region_id
    			LEFT JOIN {$this->conf['table']['village']} AS v
    			ON v.village_id = usr.village_id
    			WHERE usr.usr_id = $userId
    		";
    	$oUser = $this->dbh->getRow($query);
    	
    	$oUser->telephone_1 = substr_replace(substr_replace($oUser->telephone_1, ' ', 6, 0), ' ', 3, 0) ;
    	$oUser->telephone_2 = substr_replace(substr_replace($oUser->telephone_2, ' ', 6, 0), ' ', 3, 0) ;
    	$oUser->fax = substr_replace(substr_replace($oUser->fax, ' ', 6, 0), ' ', 3, 0) ;
    	$oUser->mobile = substr_replace(substr_replace($oUser->mobile, ' ', 6, 0), ' ', 3, 0) ;
    	
        $output->user = $oUser;
        $output->region = $oUser->region;
        
        //echo '<pre>';print_r($oUser);echo '</pre>';die;
    }

    function _cmd_summary($input, $output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->template = 'accountSummary.html';
        $currentUid = SGL_Session::getUid();
        $oUser = DB_DataObject::factory($this->conf['table']['user']);
        $oUser->get($currentUid);

        //  get current remote IP
        $output->remote_ip = $_SERVER['REMOTE_ADDR'];
        $output->login = $this->da->getLastLogin();
        $output->user = $oUser;
        $output->user->role_name = $this->da->getRoleNameById(SGL_Session::getRoleId());
    }
}
?>
