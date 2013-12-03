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
// | categoryMgr.php                                                    |
// +---------------------------------------------------------------------------+
// | Author: Sina Saderi <sina.saderi@gmail.com>                                  |
// +---------------------------------------------------------------------------+
// $Id: ManagerTemplate.html,v 1.2 2005/04/17 02:15:02 demian Exp $

require_once 'DB/DataObject.php';
require_once SGL_MOD_DIR  . '/category/classes/CategoryDAO.php';
require_once SGL_CORE_DIR . '/Delegator.php';
/**
 * Type your class description here ...
 *
 * @package category
 * @author  Sina Saderi <sina.saderi@gmail.com>
 */
class AdminCategoryMgr extends SGL_Manager
{
	var $catTree = "";
	var $aLevelTitle = array(); 
	
	
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();
        
        $daCategory	= CategoryDAO::singleton();
    	$this->da	= new SGL_Delegator();
    	$this->da->add($daCategory);
        
        $this->aLevelTitle = array(
        				1 => "Category",
        				2 => "Product group",
        				3 => "Product property",
        				4 => "Brand"
        );

        $this->pageTitle    = 'CategoryMgr';
        $this->template     = 'categoryList.html';

        $this->_aActionsMapping =  array(
            'add'       	=> array('add'),
            'insert'    	=> array('insert'),
            'edit'      	=> array('edit'), 
            'update'    	=> array('update'),
            'list'      	=> array('list'),
            'delete'    	=> array('delete'),
        	'reorder'       => array('reorder'),
            'reorderUpdate' => array('reorderUpdate'),
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
        $input->category 	= (object)$req->get('category');
        $input->categoryId 	= $req->get('frmCategoryID');
        $input->items     	= $req->get('_items');
    }

    function display($output)
    {
        if ($this->conf['CategoryMgr']['showUntranslated'] == false) {
            $c = SGL_Config::singleton();
            $c->set('debug', array('showUntranslated' => false));
        }
        
        $category = DB_DataObject::factory($this->conf['table']['category']);
        $category->selectAdd();
        $category->selectAdd('title,category_id');
        $category->whereAdd('parent_id = 0');
        $category->find();
        while($category->fetch()){
        	$aCategory[$category->category_id] = $category->title;
        }
        
        $output->aCategory = $aCategory;
    }


    function _cmd_add(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'categoryEdit.html';
        
        $output->pageTitle = "Category :: Add ";
        
        $output->action    = 'insert';

    }

    function _cmd_insert(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        
        SGL_DB::setConnection();
        //  get new order number
        $category = DB_DataObject::factory($this->conf['table']['category']);
        $category->selectAdd();
        $category->selectAdd('MAX(order_id) AS new_order');
        $category->groupBy('order_id');
        $maxOrderId = $category->find(true);
        unset($category);
        
        $category = DB_DataObject::factory($this->conf['table']['category']);
        $category->setFrom($input->category);
        $category->category_id 		= $this->dbh->nextId($this->conf['table']['category']);
        $category->status 			= (isset($input->category->status)) ? 1 : 0;
        $category->created_date 	= SGL_Date::getTime(true);
        $category->updated_date 	= SGL_Date::getTime(true);
        $category->created_by 		= SGL_Session::getUid();
        $category->updated_by 		= SGL_Session::getUid();
        $category->order_id 		= $maxOrderId;
        
        if ($category->parent_id == 0)
        	$category->level_id = 1;
        else
        	$category->level_id = 2;
        
        $success = $category->insert();

        if ($success !== false) {
            SGL::raiseMsg('category insert successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError("category insert NOT successfull",
                SGL_ERROR_NOAFFECTEDROWS);
        }
        
		$options = array(
		    'moduleName' => 'category',
		    'managerName' => 'admincategory',
		    'action' => 'list',
		);
		SGL_HTTP::redirect($options);
		
    }

    function _cmd_edit(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'categoryEdit.html';
        
        $output->action    = 'update';
        $output->wysiwyg   = true;

        $category = DB_DataObject::factory($this->conf['table']['category']);
        $category->get($input->categoryId);
        $output->category = $category;
        
        $output->pageTitle = "Edit Category";
    }

    function _cmd_update(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $category = DB_DataObject::factory($this->conf['table']['category']);
        $category->category_id = $input->categoryId;
        $category->find(true);
        $category->setFrom($input->category);
        $category->status 			= (isset($input->category->status)) ? 1 : 0;
        $category->updated_date 	= SGL_Date::getTime(true);
        $category->updated_by 		= SGL_Session::getUid();
        
        if ($category->parent_id == 0)
        	$category->level_id = 1;
        else
        	$category->level_id = 2;
        
        $success = $category->update();

        if ($success !== false) {
            SGL::raiseMsg('category update successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('category update NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }
        
        $options = array(
		    'moduleName' => 'category',
		    'managerName' => 'admincategory',
		    'action' => 'list',
		);
		SGL_HTTP::redirect($options);
    }

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'categoryList.html';
        
        $output->pageTitle = "Category list";
        
    	$query = " 
    			SELECT 
    				c.*, m.title AS pTitle 
    			FROM {$this->conf['table']['category']} AS c 
    			LEFT JOIN {$this->conf['table']['category']} AS m 
    				ON c.parent_id = m.category_id
    			ORDER BY c.order_id
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
            foreach ($input->aDelete as $index => $categoryId) {
                $category = DB_DataObject::factory($this->conf['table']['category']);
                $category->get($categoryId);
                $category->delete();
                unset($category);
            }
            SGL::raiseMsg('category delete successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('category delete NOT successfull ' .
                __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
        
        $options = array(
		    'moduleName' => 'category',
		    'managerName' => 'admincategory',
		    'action' => 'list',
		);
		SGL_HTTP::redirect($options);
    }
    
    function _cmd_reorder(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->pageTitle = $this->pageTitle . ' :: Reorder';
        $output->template  = 'categoryReorder.html';
        $output->action    = 'reorderUpdate';
        $categoryList = DB_DataObject::factory($this->conf['table']['category']);
        $categoryList->orderBy('order_id');
        $result = $categoryList->find();
        if ($result > 0) {
            $aFaqs = array();
            while ($categoryList->fetch()) {
                $aCategorys[$categoryList->category_id] = $categoryList->title;
            }
            $output->aCategorys = $aCategorys;
        }
    }
    
	function _cmd_reorderUpdate(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        if (!empty($input->items)) {
            $aNewOrder = explode(',', $input->items);
            //  reorder elements
            $pos = 1;
            foreach ($aNewOrder as $categoryId) {
                $category = DB_DataObject::factory($this->conf['table']['category']);
                $category->get($categoryId);
                $category->order_id = $pos;
                $success = $category->update();
                unset($category);
                $pos++;
            }
            SGL::raiseMsg('categorys reordered successfully', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('Incorrect parameter passed to ' . __CLASS__ . '::' .
                __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
        $options = array(
		    'moduleName' => 'category',
		    'managerName' => 'admincategory',
		    'action' => 'list',
		    'frmLevelID' => $levelId,
        	'frmParentID' => $parentId
		);
		SGL_HTTP::redirect($options);
    }
    
    function multilevel($id, $n = ' ') 
	{ 
		$this->dbh->setFetchMode(DB_FETCHMODE_OBJECT);
		$query = "select * from category where parent_id='$id'";
	    $res =& $this->dbh->query($query);
	    
	    if($res->numRows() == 0) return; 
	    $n .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	    while($row =& $res->fetchRow()) 
	    { 
		    $this->catTree .= '
			    	<tr>
			    		<td align="center"><input type="checkbox" name="frmDelete[]" value="'.$row->category_id.'" /></td>
						<td nowrap>'.$row->category_id.'</td>
						<td class="left">'.$n.$row->title.'</td>
						<td nowrap>'.SGL_Output::formatDate($row->created_date).'</td>
						<td nowrap>';
			    			if($row->status){
			    				$this->catTree .= '<span class="btn btn-xs btn-primary">&nbsp;&nbsp;'  .SGL_Output::translate("Active") . '&nbsp;&nbsp;</span>';
			    			}else{
			    				$this->catTree .= '<span type="button" class="btn btn-xs btn-danger">' . SGL_Output::translate("inactive") . '</span>';
			    			}
		    		$this->catTree .= '
		    			<td width="10%" nowrap>
				            <a href="'.SGL_Output::makeUrl("edit","admincategory","category","","frmCategoryID|".$row->category_id).'" />
				                <span class="glyphicon glyphicon-edit"></span> '. SGL_Output::translate("edit") .'
				            </a> 
			            </td>';
					$this->catTree .= '
						</td>
			    	</tr>'; 
		    $this->multilevel($row->category_id, $n); 
	    } 
	}



}
?>