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
// | Ads.php                                                                   |
// +---------------------------------------------------------------------------+
// | Author: Siavash AmirKhiz <amirkhiz@gmail.com>                             |
// +---------------------------------------------------------------------------+
include_once 'DB/DataObject.php';
/**
 * Creates static html blocks.
 *
 * @package block
 * @author  Author: Siavash AmirKhiz <amirkhiz@gmail.com>
 * @version 1.0
 */
class User_Block_History extends SGL_Manager
{
	var $template = 'historyBlock.html';
    var $templatePath 	= 'user';
    
    function init($output, $blockId, $aParams)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
            
        return $this->getBlockContent($aParams);
    }

    function getBlockContent($aParams)
    {
        $blockOutput = new SGL_Output();
        $blockOutput->history = $this->makedata($aParams);
        $blockOutput->webRoot = SGL_BASE_URL;
        return $this->process($blockOutput);
    }
    
    function makedata($aParams)
    {
    	$users = DB_DataObject::factory($this->conf['table']['user']);
    	$users->whereAdd('usr_id IN (' . $aParams['companyId'] . ')');
    	$users->find();
    	$aUsers = array();
    	while ($users->fetch())
    	{
    		$aUsers[] = clone $users;
    	}
    	//echo '<pre>';print_r($aUsers);echo '</pre>';die;
    	return $aUsers;
    }
    
	function process($output)
    {
 		SGL::logMessage(null, PEAR_LOG_DEBUG);

 		// use moduleName for template path setting
 		$output->moduleName     = $this->templatePath;
 		$output->masterTemplate = $this->template;
 		$output->conf=$conf;
 		 
 		$view = new SGL_HtmlSimpleView($output);
 		return $view->render();
    }
    
}
?>
