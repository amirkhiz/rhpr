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
// | companyMgr.php                                                    |
// +---------------------------------------------------------------------------+
// | Author: Sina Saderi <sina.saderi@gmail.com>                                  |
// +---------------------------------------------------------------------------+
// $Id: ManagerTemplate.html,v 1.2 2005/04/17 02:15:02 demian Exp $

require_once 'DB/DataObject.php';
/**
 * Type your class description here ...
 *
 * @package company
 * @author  Sina Saderi <sina.saderi@gmail.com>
 */
class AdminCompanyMgr extends SGL_Manager
{
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle    = 'CompanyMgr';
        $this->template     = 'companyList.html';

        $this->_aActionsMapping =  array(
            'add'       	=> array('add'),
            'insert'    	=> array('insert', 'redirectToDefault'),
            'edit'      	=> array('edit'), 
            'update'    	=> array('update', 'redirectToDefault'),
            'list'      	=> array('list'),
            'delete'    	=> array('delete', 'redirectToDefault'),
        	'reorder'       => array('reorder'),
            'reorderUpdate' => array('reorderUpdate', 'redirectToDefault'),
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
        $input->action      = ($req->get('action')) ? $req->get('action') : 'list';
        $input->aDelete     = $req->get('frmDelete');
        $input->submitted   = $req->get('submitted');
        $input->company 		= (object)$req->get('company');
        $input->companyId 		= $req->get('frmCompanyID');
        $input->items     = $req->get('_items');
        
    	if (($input->action == 'insert' or $input->action == 'update') && $input->action != 'reorder' ) {
            // validate input data
            if (empty($input->company->question)) {
                $aErrors['question'] = 'Please fill in a question';
            }
            
    		if (empty($input->company->answer)) {
                $aErrors['answer'] = 'Please fill in a answer';
            }
            
    	}
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
        if ($this->conf['CompanyMgr']['showUntranslated'] == false) {
            $c = SGL_Config::singleton();
            $c->set('debug', array('showUntranslated' => false));
        }
    }


    function _cmd_add(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'companyEdit.html';
        $output->pageTitle = 'CompanyMgr :: Add';
        $output->action    = 'insert';
        $output->wysiwyg   = true;    }

    function _cmd_insert(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        
        SGL_DB::setConnection();
        //  get new order number
        $company = DB_DataObject::factory($this->conf['table']['user']);
        $company->selectAdd();
        $company->selectAdd('MAX(item_order) AS new_order');
        $company->groupBy('item_order');
        $maxItemOrder = $company->find(true);
        unset($company);
        
        $company = DB_DataObject::factory($this->conf['table']['user']);
        $company->setFrom($input->company);
        $company->usr_id = $this->dbh->nextId($this->conf['table']['user']);
        $company->date_created 	= SGL_Date::getTime(true);
        $company->date_updated 	= SGL_Date::getTime(true);
        $company->created_by 	= SGL_Session::getUid();
        $company->updated_by 	= SGL_Session::getUid();
        $company->item_order 	= $maxItemOrder;
        $success = $company->insert();

        if ($success !== false) {
            SGL::raiseMsg('company insert successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError("company insert NOT successfull",
                SGL_ERROR_NOAFFECTEDROWS);
        }
    }

    function _cmd_edit(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'companyEdit.html';
        $output->pageTitle = 'CompanyMgr :: Edit';
        $output->action    = 'update';
        $output->wysiwyg   = true;

        $company = DB_DataObject::factory($this->conf['table']['user']);
        $company->get($input->companyId);
        $output->company = $company;    }

    function _cmd_update(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $company = DB_DataObject::factory($this->conf['table']['user']);
        $company->usr_id = $input->companyId;
        $company->find(true);
        $company->setFrom($input->company);
        $company->date_updated 	= SGL_Date::getTime(true);
        $company->updated_by 	= SGL_Session::getUid();
        $success = $company->update();

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
                        FROM {$this->conf['table']['user']}
                        order by item_order 
                        ";

            $limit = $_SESSION['aPrefs']['resPerPage'];
            $pagerOptions = array(
                'mode'      => 'Sliding',
                'delta'     => 8,
                'perPage'   => 10, 

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
            //echo "<pre>"; print_r($aPagedData); echo "</pre>";
        }
    }

    function _cmd_delete(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        if (is_array($input->aDelete)) {
            foreach ($input->aDelete as $index => $companyId) {
                $company = DB_DataObject::factory($this->conf['table']['user']);
                $company->get($companyId);
                $company->delete();
                unset($company);
            }
            SGL::raiseMsg('company delete successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('company delete NOT successfull ' .
                __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }    
    }
    
    function _cmd_reorder(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->pageTitle = $this->pageTitle . ' :: Reorder';
        $output->template  = 'companyReorder.html';
        $output->action    = 'reorderUpdate';
        $companyList = DB_DataObject::factory($this->conf['table']['user']);
        $companyList->orderBy('item_order');
        $result = $companyList->find();
        if ($result > 0) {
            $aCompanys = array();
            while ($companyList->fetch()) {
                $aCompanys[$companyList->usr_id] = SGL_String::summarise($companyList->question, 40);
            }
            $output->aCompanys = $aCompanys;
        }
    }
    
	function _cmd_reorderUpdate(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        if (!empty($input->items)) {
            $aNewOrder = explode(',', $input->items);
            //  reorder elements
            $pos = 1;
            foreach ($aNewOrder as $companyId) {
                $company = DB_DataObject::factory($this->conf['table']['user']);
                $company->get($companyId);
                $company->item_order = $pos;
                $success = $company->update();
                unset($company);
                $pos++;
            }
            SGL::raiseMsg('companys reordered successfully', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('Incorrect parameter passed to ' . __CLASS__ . '::' .
                __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
    }


}
?>