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
// | SearchBox.php                                                             |
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
class Company_Block_SearchBox extends SGL_Manager
{
	var $template = 'searchBoxBlock.html';
    var $templatePath 	= 'company';
    
    function init($output, $blockId, $aParams)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
            
        return $this->getBlockContent();
    }

    function getBlockContent()
    {
        $blockOutput = new SGL_Output();
        $blockOutput->aCats = $this->getCats();
        $blockOutput->aCity = $this->getCity();
        $blockOutput->webRoot = SGL_BASE_URL;
        return $this->process($blockOutput);
    }
    
    function getCats()
    {
    	$query = "
    			SELECT cat.category_id AS catId, cat.title AS catTitle
    			FROM {$this->conf['table']['company']} AS cmp
    			JOIN {$this->conf['table']['category']} AS cat
    			ON cat.category_id = cmp.category_id
    		";
    	$category = $this->dbh->getAll($query);
    	
    	$aCategory = array();
    	foreach ($category as $key => $value)
    	{
    		$aCategory[$value->catId] = $value->catTitle;
    	}
    	
    	//echo '<pre>';print_r($aCategory);echo '</pre>';die;
    	return $aCategory;
    }
    
    function getCity()
    {
    $query = "
    			SELECT city.city_id AS cityId, city.title AS cityTitle
    			FROM {$this->conf['table']['company']} AS cmp
    			JOIN {$this->conf['table']['city']} AS city
    			ON city.city_id = cmp.city_id
    		";
    	$city = $this->dbh->getAll($query);
    	
    	$aCity = array();
    	foreach ($city as $key => $value)
    	{
    		$aCity[$value->cityId] = $value->cityTitle;
    	}
    	 
    	//echo '<pre>';print_r($aCategory);echo '</pre>';die;
    	return $aCity;
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
