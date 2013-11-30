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
// | companyMgr.php                                                    		   |
// +---------------------------------------------------------------------------+
// | Author: Siavash Habil Amirkhiz <amirkhiz@gmail.com>                       |
// +---------------------------------------------------------------------------+
// $Id: ManagerTemplate.html,v 1.2 2005/04/17 02:15:02 demian Exp $

require_once 'DB/DataObject.php';
require_once SGL_MOD_DIR . '/user/classes/UserDAO.php';
include_once SGL_MOD_DIR  . '/company/classes/CompanyImage.php';

/**
 * Type your class description here ...
 *
 * @package company
 * @author  Siavash Habil Amirkhiz <amirkhiz@gmail.com>
 */
class CompanyMgr extends SGL_Manager
{
	var $cImage;
	
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle    = 'Company Manager';
        $this->template     = 'companyList.html';
        $this->institutionalTheme = 'institutional';
        $this->individualTheme = 'individual';
        $this->cImage = CompanyImage::singleton();

        $this->_aActionsMapping =  array(
            'add'         => array('add'),
            'insert'      => array('insert', 'redirectToDefault'),
            'edit'        => array('edit'), 
            'update'      => array('update', 'redirectToDefault'),
            'list'        => array('list'),
        	'viewProfile' => array('viewProfile'),
        );
    }

    function validate($req, &$input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $this->validated    = true;
        $input->error       = array();
        $input->pageTitle   = $this->pageTitle;
        $input->masterTemplate = $this->masterTemplate;
        $input->template    = $this->template;
        $input->action      = ($req->get('action')) ? $req->get('action') : 'viewProfile';
        $input->aDelete     = $req->get('frmDelete');
        $input->submitted   = $req->get('submitted');
        $input->company = (object)$req->get('company');
        $input->companyId = $req->get('frmCompanyID');
        $input->regGrp      = $req->get('regGrp');
        $input->role        = $req->get('role');
        $input->aImage 		= $req->get('fImage');
        $input->aLogo 		= $req->get('fLogo');
        $input->branch      = (object)$req->get('branch');
        $input->aBranchImg	= $req->get('fBranchImage');
        
        //  if errors have occured
        if (isset($aErrors) && count($aErrors)) {
            SGL::raiseMsg('Please fill in the indicated fields');
            $input->template = 'companyEdit.html';
            $input->error = $aErrors;
            $this->validated = false;
        	    
        	if ($input->action == 'insert') {
                $input->pageTitle .= ' :: Add';
            } elseif ($input->action == 'update') {
                $input->pageTitle .= ' :: Edit';
            }
        }
    }

    function display($output)
    {
    	//  set flag to we can share add/edit templates
    	if ($output->action == 'add' || $output->action == 'insert') {
    		$output->isAdd = true;
    	}
    	
        $city = DB_DataObject::factory($this->conf['table']['city']);
        $city->whereAdd('status = 1');
        $city->find();
        $aCity = array();
        while ($city->fetch())
        {
        	$aCity[$city->city_id] = $city->title;
        }
        $output->aCity = $aCity;
        if (!$output->isAdd)
        {
	        $query = "
			        SELECT
				        c.city_id AS cId, c.Title AS cTitle,
				        r.region_id AS rId, r.Title AS rTitle,
				        v.village_id AS vId, v.Title AS vTitle
			        FROM {$this->conf['table']['city']} AS c
			        RIGHT JOIN {$this->conf['table']['region']} AS r
			        	ON r.city_id = c.city_id
			        RIGHT JOIN {$this->conf['table']['village']} AS v
			        	ON v.region_id = r.region_id
			        WHERE c.city_id = {$output->company->city_id}
		        ";
	        
	        $lists = $this->dbh->getAll($query);
	        $aRegion = array();
	        $aVillage = array();
	        foreach ($lists as $key => $value)
	        {
		        $aRegion[$value->rId] = $value->rTitle;
		        $aVillage[$value->vId] = $value->vTitle;
	        }
	        $output->aRegion = $aRegion;
        	$output->aVillage = $aVillage;
        }
        //echo '<pre>';print_r($usrInfo);echo '</pre>';die;
         
        $output->aCats = array('cat1', 'cat2', 'cat3');
    }


    function _cmd_add(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = $this->individualTheme . '.html';
        $output->pageTitle = $this->individualTheme;
        $output->action    = 'insert';
        $output->wysiwyg   = true;    
        $output->role = SGL_INDIVIDUAL;
        $output->institutional = SGL_INSTITUTIONAL;
        $output->individual = SGL_INDIVIDUAL;
        
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
    }

    function _cmd_insert(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        
        //echo '<pre>';print_r($input->company->email);echo '</pre>';die;
        
        require_once 'Text/Password.php';
        $oPassword = new Text_Password();
        $passwd = $oPassword->create(10, 'unpronounceable', '');
         
        $oUser = DB_DataObject::factory($this->conf['table']['user']);
        $oUser->setFrom($input->company);
        $oUser->usr_id = $usrId = $this->dbh->nextId($this->conf['table']['user']);
        $oUser->role_id = $input->role;
        $oUser->username = $oUser->email = $input->company->email;
        $oUser->passwdClear = $passwd;
        $oUser->passwd = md5($passwd);
        $oUser->temp_pass = $passwd;
        $oUser->date_created = $oUser->last_updated = SGL_Date::getTime();
        $oUser->created_by = SGL_Session::getRoleId();
        $oUser->is_acct_active = 1;
         
        $usrSuccess = $oUser->insert();

        if ($usrSuccess !== false) {
	        $oCompany = DB_DataObject::factory($this->conf['table']['company']);
	        $oCompany->setFrom($input->company);
	        $oCompany->company_id = $this->dbh->nextId($this->conf['table']['company']);
	        $oCompany->usr_id = $usrId;
	        $oCompany->has_branch = (isset($input->company->has_branch)) ? 1 : 0;
	        $oCompany->date_created = $oCompany->last_updated = SGL_Date::getTime();
	        $oCompany->telephone_1 = (isset($input->company->telephone_1)) ? str_replace(' ', '', $input->company->telephone_1) : 0;
	        $oCompany->telephone_2 = (isset($input->company->telephone_2)) ? str_replace(' ', '', $input->company->telephone_2) : 0;
	        $oCompany->fax = (isset($input->company->fax)) ? str_replace(' ', '', $input->company->fax) : 0;
	        $oCompany->mobile = str_replace(' ', '', $input->company->mobile);
	        
	        if(isset($input->aImage['name']) && $input->aImage['name'] != "") {
	        	$input->aImage['name'] = $this->cImage->generateUniqueFileName($input->aImage['name']);
	        	$this->cImage->uploadImage($input->aImage['name'], $input->aImage['tmp_name']);
	        	$oCompany->image = $input->aImage['name'];
	        }
	        if(isset($input->aLogo['name']) && $input->aLogo['name'] != "") {
	        	$input->aLogo['name'] = $this->cImage->generateUniqueFileName($input->aLogo['name']);
	        	$this->cImage->uploadImage($input->aLogo['name'], $input->aLogo['tmp_name']);
	        	$oCompany->logo = $input->aLogo['name'];
	        }
	        
	        $success = $oCompany->insert();
	        
	        if ($success !== false) {
	            SGL::raiseMsg('company insert successfull', false, SGL_MESSAGE_INFO);
	        } else {
	            SGL::raiseError('company insert NOT successfull',
	                SGL_ERROR_NOAFFECTEDROWS);
	        }
	        
        } else {
        	SGL::raiseError('User insert NOT successfull',
        			SGL_ERROR_NOAFFECTEDROWS);
        }
        SGL_HTTP::redirect(array('moduleName' => 'default', 'managerName' => 'default'));
    }

    function _cmd_edit(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'companyEdit.html';
        $output->pageTitle = 'CompanyMgr :: Edit';
        $output->action    = 'update';
        $output->wysiwyg   = true;
        
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

        $company = DB_DataObject::factory($this->conf['table']['company']);
        $company->whereAdd('usr_id = ' . SGL_Session::getUid());
        $company->find(true);
        $output->company = $company;
        $output->hasBranch = empty($company->has_branch) ? '' : 'checked';
    }

    function _cmd_update(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $oCompany = DB_DataObject::factory($this->conf['table']['company']);
        $oCompany->whereAdd('usr_id = ' . SGL_Session::getUid());
        $oCompany->find(true);
        $oCompany->setFrom($input->company);
        
        $oCompany->has_branch = (isset($input->company->has_branch)) ? 1 : 0;
        $oCompany->telephone_1 = (isset($input->company->telephone_1)) ? str_replace(' ', '', $input->company->telephone_1) : 0;
        $oCompany->telephone_2 = (isset($input->company->telephone_2)) ? str_replace(' ', '', $input->company->telephone_2) : 0;
        $oCompany->fax = (isset($input->company->fax)) ? str_replace(' ', '', $input->company->fax) : 0;
        $oCompany->mobile = str_replace(' ', '', $input->company->mobile);
        $oCompany->last_updated = SGL_Date::getTime();
        
        if(isset($input->aImage['name']) && $input->aImage['name'] != "") {
        	$input->aImage['name'] = $this->cImage->generateUniqueFileName($input->aImage['name']);
        	$this->cImage->uploadImage($input->aImage['name'], $input->aImage['tmp_name']);
        	$oCompany->image = $input->aImage['name'];
        }
        if(isset($input->aLogo['name']) && $input->aLogo['name'] != "") {
        	$input->aLogo['name'] = $this->cImage->generateUniqueFileName($input->aLogo['name']);
        	$this->cImage->uploadImage($input->aLogo['name'], $input->aLogo['tmp_name']);
        	$oCompany->logo = $input->aLogo['name'];
        }
        
        $success = $oCompany->update();

        if ($success !== false) {
            SGL::raiseMsg('company update successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('company update NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }    }

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'companyList.html';
        $output->pageTitle = 'CompanyMgr :: List';

        //  only execute if CRUD option selected
        if (true) {
            $query = "  SELECT
                             *
                        FROM {$this->conf['table']['company']}
                        ";

            $limit = $_SESSION['aPrefs']['resPerPage'];
            $pagerOptions = array(
                'mode'      => 'Sliding',
                'delta'     => 3,
                'perPage'   => $limit,
            );
            $aPagedData = SGL_DB::getPagedData($this->dbh, $query, $pagerOptions);
            if (PEAR::isError($aPagedData)) {
                return false;
            }
            $output->aPagedData = $aPagedData;
            $output->totalItems = $aPagedData['totalItems'];

            if (is_array($aPagedData['data']) && count($aPagedData['data'])) {
                $output->pager = ($aPagedData['totalItems'] <= $limit) ? false : true;
            }
        }
    }
    
    function _cmd_viewProfile($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	$output->masterTemplate = 'masterLeftCol.html';
    	$output->masterLayout = 'layout-navtop-2col_localleft.css';
    	
    	/* if (isset($input->companyId))
    	{
    		$oCompany = DB_DataObject::factory($this->conf['table']['company']);
    		$query = "
    			SELECT c.company_id, c.
    			
    			";
    		$roleId = 
    	} */
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
		    	SELECT cmp.*, c.title AS city, v.title AS village, r.title AS region
		    	FROM {$this->conf['table']['company']} AS cmp
		    	LEFT JOIN {$this->conf['table']['city']} AS c
		    	ON c.city_id = cmp.city_id
		    	LEFT JOIN {$this->conf['table']['region']} AS r
		    	ON r.region_id = cmp.region_id
		    	LEFT JOIN {$this->conf['table']['village']} AS v
		    	ON v.village_id = cmp.village_id
		    	WHERE cmp.usr_id = $userId
	    	";
    	$oCompany = $this->dbh->getRow($query);
    	 
    	$oCompany->telephone_1 = substr_replace(substr_replace($oCompany->telephone_1, ' ', 6, 0), ' ', 3, 0) ;
    	$oCompany->telephone_2 = substr_replace(substr_replace($oCompany->telephone_2, ' ', 6, 0), ' ', 3, 0) ;
    	$oCompany->fax = substr_replace(substr_replace($oCompany->fax, ' ', 6, 0), ' ', 3, 0) ;
    	$oCompany->mobile = substr_replace(substr_replace($oCompany->mobile, ' ', 6, 0), ' ', 3, 0) ;
    	$oCompany->keywords = explode(',', $oCompany->keywords);
    	 
    	$output->company = $oCompany;
    	$output->isOwner = (SGL_Session::getUid() == $oCompany->usr_id) ? 1 : 0;    
    	//echo '<pre>';print_r($oCompany);echo '</pre>';die;
    }
}
?>