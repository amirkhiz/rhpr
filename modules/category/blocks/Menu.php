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
// | Menu.php                                                                   |
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
class Category_Block_Menu extends SGL_Manager
{
	var $leftMenuTemplate   = 'menuBlock.html';
	var $footerMenuTemplate = 'footerMenuBlock.html';
	var $template = '';
    var $templatePath 	= 'category';
    
    function init($output, $blockId, $aParams)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
            
        return $this->getBlockContent($aParams);
    }

    function getBlockContent($aParams)
    {
        $blockOutput = new SGL_Output();
        $blockOutput->items = $this->makedata($aParams, $blockOutput);
        $blockOutput->webRoot = SGL_BASE_URL;
        return $this->process($blockOutput);
    }
    
    function makedata($aParams, $output)
    {
    	$oCategory = DB_DataObject::factory($this->conf['table']['category']);
    	$oCategory->whereAdd('level_id = 1');
    	if ($aParams['position'] == 0)
    		$oCategory->limit($aParams['limit']);
    	$oCategory->find();
    	
    	$oSubCategory = DB_DataObject::factory($this->conf['table']['category']);
    	$oSubCategory->whereAdd('level_id = 2');
    	if ($aParams['position'] == 1)
    		$oSubCategory->limit($aParams['limit']);
    	$oSubCategory->find();
    	
    	$aCategory = array();
    	while ($oCategory->fetch())
    	{
    		$aCategory[$oCategory->category_id] = $this->objectToArray($oCategory);
    	}
    	
    	while ($oSubCategory->fetch())
    	{
    		$aCategory[$oSubCategory->parent_id]['SubMenu'][$oSubCategory->category_id] = $this->objectToArray($oSubCategory);
    		$aCategory['AllCats']['title'] = SGL_String::translate('All Categories');
    		$aCategory['AllCats']['SubMenu'][$oSubCategory->category_id] = $this->objectToArray($oSubCategory);
    	}
    	
    	//echo '<pre>';print_r($aCategory);echo '</pre>';die;
    	
    	//Move "All Cats" Array from last index to first Index
    	end($aCategory);
    	$last_key = key($aCategory);
    	$aCategory = $this->move_to_top($aCategory, $last_key);
    	
    	switch ($aParams['position'])
    	{
    		case 0:
    			$this->template = $this->leftMenuTemplate;
    			break;
    		case 1:
    			$this->template = $this->footerMenuTemplate;
    			break;
    	}
    	
    	$output->subCatsCount = $subCatsCount = count($aCategory['AllCats']['SubMenu']);
    	$cols = $subCatsCount / 10;
    	if ($cols <= 1)
    		$output->cols = 'one';
    	elseif (1 < $cols && $cols <= 2)
    		$output->cols = 'double';
    	elseif (2 < $cols && $cols <= 3)
    		$output->cols = 'triple';
    	elseif (3 < $cols && $cols <= 4)
    		$output->cols = 'quad';
		else
    		$output->cols = 'six';
		
    	//echo '<pre>';print_r($cols);echo '</pre>';die;
    	return $aCategory;
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
    
	function move_to_top($array, $key) {
	    $temp = array($key => $array[$key]);
	    unset($array[$key]);
	    $array = $temp + $array;
	    return $array;
	}
    
    function objectToArray( $data )
    {
    	if (is_array($data) || is_object($data))
    		//if (count($data))
    	{
    		$result = array();
    		foreach ($data as $key => $value)
    		{
    			$result[$key] = $this->objectToArray($value);
    		}
    		return $result;
    	}
    	return $data;
    }
}
?>
