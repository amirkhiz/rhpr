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
// | pollMgr.php                                                    		   |
// +---------------------------------------------------------------------------+
// | Author: Siavash Amirkhiz <amirkhiz@gmail.com>                             |
// +---------------------------------------------------------------------------+
// $Id: ManagerTemplate.html,v 1.2 2005/04/17 02:15:02 demian Exp $

require_once 'DB/DataObject.php';
/**
 * Type your class description here ...
 *
 * @package poll
 * @author  Siavash Amirkhiz <amirkhiz@gmail.com>
 */
class AdminPollMgr extends SGL_Manager
{
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle    = 'PollMgr';
        $this->template     = 'pollList.html';
        $this->sortBy 		= 'pq.order_id';

        $this->_aActionsMapping =  array(
            'add'       	=> array('add'),
            'insert'    	=> array('insert', 'redirectToDefault'),
            'edit'      	=> array('edit'), 
            'update'    	=> array('update', 'redirectToDefault'),
            'list'      	=> array('list'),
            'delete'    	=> array('delete', 'redirectToDefault'),
        	'reorder'       => array('reorder'),
            'reorderUpdate' => array('reorderUpdate', 'redirectToDefault'),
        	'results' 		=> array('results'),
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
        $input->poll 		= (object)$req->get('poll');
        $input->pollId 		= $req->get('frmPollID');
        $input->items       = $req->get('_items');
        $input->pollOpt 	= $req->get('pollOpt');
        $input->pollOptExist= $req->get('pollOptExist');
        $input->pollDelOpt 	= $req->get('pollDeletedOpt');
        $input->sortBy      = $this->getSortBy($req->get('frmSortBy'), SGL_SORTBY_USER);
        $input->sortOrder   = SGL_Util::getSortOrder($req->get('frmSortOrder'));
        
        // This will tell HTML_Flexy which key is used to sort data
        $input->{ 'sort_' . $input->sortBy } = true;
        
    	if (($input->action == 'insert' or $input->action == 'update') && $input->action != 'reorder' ) {
            // validate input data
            if (empty($input->poll->title)) {
                $aErrors['question'] = 'Please fill in a question';
            }
    	}
        //  if errors have occured
        if (isset($aErrors) && count($aErrors)) {
            SGL::raiseMsg('Please fill in the indicated fields');
            $input->template = 'pollEdit.html';
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
        if ($this->conf['PollMgr']['showUntranslated'] == false) {
            $c = SGL_Config::singleton();
            $c->set('debug', array('showUntranslated' => false));
        }
    }


    function _cmd_add(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'pollEdit.html';
        $output->pageTitle = 'PollMgr :: Add';
        $output->action    = 'insert';
        $output->wysiwyg   = true;    
    }

    function _cmd_insert(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        
        SGL_DB::setConnection();
        //  get new order number
        $poll = DB_DataObject::factory($this->conf['table']['poll']);
        $poll->selectAdd();
        $poll->selectAdd('MAX(order_id) AS new_order');
        $poll->groupBy('order_id');
        $maxItemOrder = $poll->find(true);
        unset($poll);
        
        $poll = DB_DataObject::factory($this->conf['table']['poll']);
        $poll->setFrom($input->poll);
        $poll->poll_id 			= $pollId = $this->dbh->nextId($this->conf['table']['poll']);
        $poll->title			= $input->poll->title;
        $poll->date_created 	= SGL_Date::getTime(true);
        $poll->order_id 		= $maxItemOrder;
        
        $success = $poll->insert();
        
        //  insert options in poll_question table
        foreach ($input->pollOpt as $key => $value)
        {
        	$pollQues = DB_DataObject::factory($this->conf['table']['poll_question']);
        	$pollQues->setFrom($input->pollOpt);
       		$pollQues->poll_question_id	= $this->dbh->nextId('poll_question');
       		$pollQues->poll_id 			= $pollId;
       		$pollQues->title 			= $value;
       		$pollQues->order_id 		= $key;
        		 
       		$pollQuesRes = $pollQues->insert();
        }

        if ($success !== false) {
            SGL::raiseMsg('poll insert successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError("poll insert NOT successfull",
                SGL_ERROR_NOAFFECTEDROWS);
        }
    }

    function _cmd_edit(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'pollEdit.html';
        $output->pageTitle = 'PollMgr :: Edit';
        $output->action    = 'update';
        $output->wysiwyg   = true;

        $poll = DB_DataObject::factory($this->conf['table']['poll']);
        $poll->get($input->pollId);
        $output->poll = $poll;
        
        $pollOpts = DB_DataObject::factory($this->conf['table']['poll_question']);
        $pollOpts->poll_id = $input->pollId;
        $pollOpts->find();
        
        $aPollOpts = array();
        while($pollOpts->fetch())
        {
        	$aPollOpts[$pollOpts->poll_question_id] = $pollOpts->title; 
        }
        $output->aPollOpts = $aPollOpts;
        //echo '<pre>';print_r($aPollOpts);echo '</pre>';die;
    }

    function _cmd_update(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $poll = DB_DataObject::factory($this->conf['table']['poll']);
        $poll->poll_id = $pollId = $input->pollId;
        $poll->find(true);
        $poll->setFrom($input->poll);
        $poll->date_updated 	= SGL_Date::getTime(true);
        $poll->updated_by 	= SGL_Session::getUid();
        $success = $poll->update();
        
        //insert new options to poll_question table
        if (!empty($input->pollOpt['0']))
        {
	        //  insert options in poll_question table
	        foreach ($input->pollOpt as $key => $value)
	        {
	        	//  get new order number
	        	$pollQues = DB_DataObject::factory($this->conf['table']['poll_question']);
	        	$pollQues->selectAdd();
	        	$pollQues->selectAdd('MAX(order_id) AS new_order');
	        	$pollQues->whereAdd('poll_id = ' . $pollId);
	        	$pollQues->groupBy('order_id');
	        	$maxItemOrder = $pollQues->find(true);
	        	unset($pollQues);
	        	
	        	$pollQues = DB_DataObject::factory($this->conf['table']['poll_question']);
	        	$pollQues->setFrom($input->pollOpt);
	       		$pollQues->poll_question_id	= $this->dbh->nextId('poll_question');
	       		$pollQues->poll_id 			= $pollId;
	       		$pollQues->title 			= $value;
	       		$pollQues->order_id 		= $maxItemOrder;
	        		 
	       		$pollQuesRes = $pollQues->insert();
	        }
        }
         
        //delete images sent from form FROM product images
        $aOptDel = explode(',', $input->pollDelOpt);
        unset($aOptDel['0']);
         
    	if (is_array($aOptDel)) {
            foreach ($aOptDel as $index => $pollQId) {
                $pollQues = DB_DataObject::factory($this->conf['table']['poll_question']);
                $pollQues->get($pollQId);
                $pollQues->delete();
                unset($pollQues);
            }
            SGL::raiseMsg('poll Options delete successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('poll option delete NOT successfull ' .
                __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }

        if ($success !== false) {
            SGL::raiseMsg('poll update successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('poll update NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }    }

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'pollList.html';
        $output->pageTitle = 'PollMgr :: List';

		$query = "  
            	SELECT *
                FROM {$this->conf['table']['poll']}
            	ORDER BY order_id 
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
    }

    function _cmd_delete(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        if (is_array($input->aDelete)) {
            foreach ($input->aDelete as $index => $pollId) {
                $poll = DB_DataObject::factory($this->conf['table']['poll']);
                $poll->get($pollId);
                $poll->delete();
                unset($poll);
            }
            SGL::raiseMsg('poll delete successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('poll delete NOT successfull ' .
                __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
    }
    
    function _cmd_reorder(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->pageTitle = $this->pageTitle . ' :: Reorder';
        $output->template  = 'pollReorder.html';
        $output->action    = 'reorderUpdate';
        $pollList = DB_DataObject::factory($this->conf['table']['poll']);
        $pollList->orderBy('order_id');
        $result = $pollList->find();
        if ($result > 0) {
            $aPolls = array();
            while ($pollList->fetch()) {
                $aPolls[$pollList->poll_id] = SGL_String::summarise($pollList->question, 40);
            }
            $output->aPolls = $aPolls;
        }
    }
    
	function _cmd_reorderUpdate(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        if (!empty($input->items)) {
            $aNewOrder = explode(',', $input->items);
            //  reorder elements
            $pos = 1;
            foreach ($aNewOrder as $pollId) {
                $poll = DB_DataObject::factory($this->conf['table']['poll']);
                $poll->get($pollId);
                $poll->order_id = $pos;
                $success = $poll->update();
                unset($poll);
                $pos++;
            }
            SGL::raiseMsg('polls reordered successfully', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('Incorrect parameter passed to ' . __CLASS__ . '::' .
                __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
    }

	function _cmd_results($input, $output)
	{
		SGL::logMessage(null, PEAR_LOG_DEBUG);
		
		$output->pageTitle = 'Poll Resuts';
		$output->template  = 'pollResults.html';
		$output->action    = 'reorderUpdate';
		
		$pollId = $input->pollId;
		
		$allowedSortFields = array('usr.username', 'usr.first_name', 'usr.last_name', 'paCount');
		if (      !empty($input->sortBy)
				&& !empty($input->sortOrder)
				&& in_array($input->sortBy, $allowedSortFields)) {
			$orderBy_query = ' ORDER BY ' . $input->sortBy . ' ' . $input->sortOrder ;
		} else {
			$orderBy_query = ' ORDER BY pq.order_id ASC ';
		}
		
		$query = "
				SELECT 
					p.title AS pTitle, p.poll_id AS pollId,
					pq.title AS pqTitle,
					pa.date_created AS paDateCr,
					COUNT(pa.poll_answer_id) AS paCount,
					usr.username AS usrName,
					usr.first_name AS usrFName,
					usr.last_name AS usrLName
				FROM {$this->conf['table']['poll']} AS p
				JOIN {$this->conf['table']['poll_question']} AS pq
				ON pq.poll_id = p.poll_id
				LEFT JOIN {$this->conf['table']['poll_answer']} AS pa
				ON pa.poll_question_id = pq.poll_question_id
				LEFT JOIN {$this->conf['table']['user']} AS usr
				ON usr.usr_id = pa.usr_id
				WHERE p.poll_id = '$pollId'
				GROUP BY pq.poll_question_id
				$orderBy_query
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
		
		$aPagedData['data'] = $this->percentage($aPagedData['data']);
		
		$output->aPagedData = $aPagedData;
		$output->totalItems = $aPagedData['totalItems'];
		
		if (is_array($aPagedData['data']) && count($aPagedData['data'])) {
			$output->pager = ($aPagedData['totalItems'] <= $limit) ? false : true;
		}
		
		//echo '<pre>';print_r($aPagedData['data']);echo '</pre>';die;
	}
	
	function percentage($aValue)
	{
		$total = 0;
		foreach ($aValue as $val)
		{
			$total += $val['paCount'];
		}
	
		foreach ($aValue as $key => $val)
		{
			$count1 = $val['paCount'] / $total;
			$count2 = $count1 * 100;
			$aValue[$key]['percent'] = number_format($count2, 0);
		}
		return $aValue;
	}
	
	/**
	 * Determines which column results should be sorted by.
	 *
	 * If no value passed from Request, returns last value
	 * from session
	 *
	 * @access  public
	 * @param   string  $frmSortBy      column name passed from Request
	 * @return  string  $sortBy         value to sort by
	 */
	function getSortBy($frmSortBy)
	{
		SGL::logMessage(null, PEAR_LOG_DEBUG);
		
		$sessSortBy = SGL_Session::get('sortByPollQuest');
	
		if ($frmSortBy == '' && $sessSortBy == '') {
			//  take default set in child class
			$sortBy = $this->sortBy;
		} elseif ($frmSortBy == '') {
			$sortBy = $sessSortBy;
		} else {
			$sortBy = $frmSortBy;
		}
		//  update session
		$sessVar = 'sortBy' . $sortByType;
		SGL_Session::set($sessVar, $sortBy);
		return $sortBy;
	}
}
?>