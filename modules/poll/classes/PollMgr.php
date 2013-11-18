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
class PollMgr extends SGL_Manager
{
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle    = 'Poll';
        $this->template     = 'pollList.html';

        $this->_aActionsMapping =  array(
            'insert'    => array('insert', 'redirectToDefault'),
            'list'      => array('list'),
        );
    }

    function validate($req, $input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $this->validated    = true;
        $input->error       = array();
        $input->pageTitle   = $this->pageTitle;
        $input->masterTemplate = $this->masterTemplate;
        $input->template    = $this->template;
        $input->action      = ($req->get('action')) ? $req->get('action') : 'list';
        $input->submitted   = $req->get('submitted');
        $input->poll 		= (object)$req->get('poll');
        $input->pollId 		= $req->get('frmPollID');
        $input->pollAnsId 	= $req->get('frmPollAnswer');
    }

    function display($output)
    {
        if ($this->conf['PollMgr']['showUntranslated'] == false) {
            $c = SGL_Config::singleton();
            $c->set('debug', array('showUntranslated' => false));
        }
    }

    function _cmd_insert($input, $output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        
        
        //Get User ID
        $usrId = SGL_Session::getUid();
        
        //Check If user vote for this poll
        $pollAnsCheck = $this->searchExistRes($input->pollId, $input->pollAnsId, $usrId);
        
        if ($pollAnsCheck){
	        $pollAns = DB_DataObject::factory($this->conf['table']['poll_answer']);
	        $pollAns->setFrom($input->poll);
	        $pollAns->poll_answer_id = $this->dbh->nextId($this->conf['table']['poll_answer']);
	        $pollAns->poll_question_id = $input->pollAnsId;
	        $pollAns->usr_id = $usrId;
	        $pollAns->date_created = SGL_Date::getTime(true);;
	        $success = $pollAns->insert();
	
	        if ($success !== false) {
	            SGL::raiseMsg('Poll Answer insert successfull', false, SGL_MESSAGE_INFO);
	            $options = array(
	            		'moduleName' => 'poll',
	            		'managerName' => 'poll',
	            		'action' => 'list',
	            		'frmPollID' => $input->pollId
	            );
	            SGL_HTTP::redirect($options);
	        } else {
	            SGL::raiseError('Poll Answer insert NOT successfull',
	                SGL_ERROR_NOAFFECTEDROWS);
	        }
        } else {
        	SGL::raiseMsg('You Voted Later!', false, SGL_MESSAGE_WARNING);
        }
    }

    function _cmd_list($input, $output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        
        $pollId = $input->pollId;

        if (isset($pollId)){
	        $query = "  
	        		SELECT 
	        			p.title AS pTitle, 
	        			pq.poll_question_id AS pqId, pq.title AS pqTitle, 
	        			COUNT(pa.poll_answer_id) AS paCount
	                FROM {$this->conf['table']['poll']} AS p
	                JOIN {$this->conf['table']['poll_question']} AS pq
	                ON pq.poll_id = p.poll_id
	                LEFT JOIN {$this->conf['table']['poll_answer']} AS pa
	                ON pa.poll_question_id = pq.poll_question_id
	                WHERE p.poll_id = {$pollId}
	        		GROUP BY pq.poll_question_id
	        		ORDER By pq.order_id
				";
	        $pollResult = $this->dbh->getAll($query);
	        
	        $pollResult = $this->percentage($pollResult);
	        
	        $output->aPollRes = $pollResult;
	        $output->template  = 'pollList.html';
	        $output->pageTitle = 'Poll Result For ' . $pollResult['0']->pTitle;
        }
    }
    
    function searchExistRes($pollId, $pollQuesId, $usrId)
    {
    	$query = "
		    	SELECT
			    	*
		    	FROM {$this->conf['table']['poll_answer']} AS pa
		    	JOIN {$this->conf['table']['poll_question']} AS pq
                ON pq.poll_question_id = pa.poll_question_id
		    	WHERE pa.poll_question_id = {$pollQuesId}
    			AND pa.usr_id = {$usrId}
    			AND pq.poll_id = {$pollId}
    		";
    	
    	$pollAnsResult = $this->dbh->getOne($query);
    	
    	if ($pollAnsResult)
    		return false;
    	else 
    		return true;
    }
    
    function percentage($aValue)
    {
    	$total = 0;
    	foreach ($aValue as $val)
    	{
    		$total += $val->paCount;
    	}
		
    	foreach ($aValue as $key => $val)
    	{
			$count1 = $val->paCount / $total;
			$count2 = $count1 * 100;
			$aValue[$key]->percent = number_format($count2, 0);
    	}
    	return $aValue;
    }
}
?>