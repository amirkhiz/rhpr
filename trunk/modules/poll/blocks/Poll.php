<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Copyright (c) 2008, Demian Turner                                         |
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
// | Seagull 0.6                                                               |
// +---------------------------------------------------------------------------+
// | Poll.php                                           		               |
// +---------------------------------------------------------------------------+
// | Author: Siavash Habil Amirkhiz <amirkhiz@gmail.com>                       |
// +---------------------------------------------------------------------------+
include_once 'DB/DataObject.php';

/**
 * Creates static html blocks.
 *
 * @package block
 * @author  Siavash Habil Amirkhiz <amirkhiz@gmail.com>
 * @version 0.5
 */
class Poll_Block_Poll
{
 	var $template     = 'poll.html';
    var $templatePath = 'poll';
    
    function init(&$output, $blockId, &$aParams)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        return $this->getBlockContent($output, $aParams);
    }

    function getBlockContent(&$aParams, &$aParams)
    {
    	//  set default params
    	$aDefaultParams = array(
    			'pollTemplate' 	=> 'poll.html',
    			'pollId'      	=> 0,
    	);
    	
    	//  set custom params
    	foreach ($aParams as $key => $value) {
    		$aDefaultParams[$key] = $value;
    	}
    	$pollId = $aDefaultParams['pollId'];
    	
    	$blockOutput = new SGL_Output();
		SGL::logMessage(null, PEAR_LOG_DEBUG);
		
		static $conf;
		static $webRoot;
        
		$blockOutput->aPoll = $this->getPoll($pollId);

        return $this->process($blockOutput);
    }
    
    function getPoll($pollId) 
    {
    	$dbh = & SGL_DB::singleton();
        $c = &SGL_Config::singleton();
        $conf = $c->getAll();

        $query = "
                SELECT p.poll_id AS pId, p.title AS pTitle, pq.poll_question_id AS pqId, pq.title AS pqTitle
                FROM {$conf['table']['poll']} AS p
                JOIN {$conf['table']['poll_question']} AS pq
                ON pq.poll_id = p.poll_id
                WHERE p.poll_id = {$pollId}
                ";
        $pollRes = $dbh->getAll($query);
        
        $aPoll = array();
        foreach ($pollRes as $key => $value)
        {
        	$aPoll[$value->pId]['title'] = $value->pTitle;
        	$aPoll[$value->pId]['opts'][$value->pqId] = $value->pqTitle;
        }
        
        //echo '<pre>';print_r($aPoll);echo '</pre>';die;
        
        if (!DB::isError($aPoll)) {
            return $aPoll;
        } else {
            SGL::raiseError('perhaps no item tables exist', SGL_ERROR_NODATA);
        }
    }
    
	function process($output)
    {
 		// use moduleName for template path setting
        $output->moduleName     = $this->templatePath;
        $output->masterTemplate = $this->template;
        
        $view = new SGL_HtmlSimpleView($output);
        
        return $view->render();
        
    }
    
    
}
?>