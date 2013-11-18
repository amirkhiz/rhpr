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
// | ProductMgr.php                                                             |
// +---------------------------------------------------------------------------+
// | Author:   Siavash AmirKhiz <amirkhiz@gmail.com>                           |
// +---------------------------------------------------------------------------+
// $Id: productMgr.php,v 1.26 2005/06/12 17:57:57 demian Exp $

require_once 'DB/DataObject.php';

/**
 * Allow users to see products.
 *
 * @package Product
 * @author  Siavash AmirKhiz <amirkhiz@gmail.com>
 * @version 1.0
 * @since   PHP 4.1
 */
class productMgr extends SGL_Manager
{
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle = 'products';
        $this->template  = 'productList.html';

        $this->_aActionsMapping =  array(
            'add'           => array('add'),
            'insert'        => array('insert', 'redirectToDefault'),
            'edit'          => array('edit'),
            'update'        => array('update', 'redirectToDefault'),
            'reorder'       => array('reorder'),
            'reorderUpdate' => array('reorderUpdate', 'redirectToDefault'),
            'delete'        => array('delete', 'redirectToDefault'),
            'list'          => array('list'),
        );
        
    }

    function validate($req, &$input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::validate($req, $input);
        
        $this->validated       = true;
        $input->pageTitle      = $this->pageTitle;
        $input->masterTemplate = $this->masterTemplate;
        $input->template       = $this->template;
        
        $input->productId   = $req->get('frmProductID');
        $input->items     	= $req->get('_items');
        $input->product     = (object)$req->get('product');
        
        // Misc.
        $input->submitted   = $req->get('submitted');
        $input->action      = ($req->get('action')) ? $req->get('action') : 'list';
        $input->aDelete     = $req->get('frmDelete');
        
        if ($input->submitted && in_array($input->action, array('insert'))) {
        	if (empty($input->product->title)) {
        		$aErrors['title'] = 'Please fill in a title';
        	}
        }
        if ($input->submitted && in_array($input->action, array('update'))) {
        	if (empty($input->product->title)) {
        		$aErrors['title'] = 'Please fill in a title';
        	}
        }
        
        //  if errors have occured
        if (isset($aErrors) && count($aErrors)) {
        	SGL::raiseMsg('Please fill in the indicated fields');
        	$input->error    = $aErrors;
        	$input->template = 'productEdit.html';
        	$this->validated = false;
        
        	// save currect title
        	if ($input->action == 'insert') {
        		$input->pageTitle .= ' :: Add';
        	} elseif ($input->action == 'update') {
        		$input->pageTitle .= ' :: Edit';
        	}
        }
    }

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $productList = DB_DataObject::factory($this->conf['table']['product']);
        $user = DB_DataObject::factory($this->conf['table']['user']);
        $productList->joinAdd($user, 'LEFT', 'AS u', 'usr_id');
        $productList->orderBy('item_order');
        $result = $productList->find();
        $aproducts  = array();
        if ($result > 0) {
            while ($productList->fetch()) {
                $productList->title = $productList->title;
                $aproducts[]        = clone($productList);
            }
        }
        $output->results = $aproducts = $this->objectToArray($aproducts);
        //echo '<pre>';print_r($aproducts);echo '</pre>';die;
    
    }
    
    function _cmd_add(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	$output->template   = 'productEdit.html';
    	$output->action     = 'insert';
    	$output->pageTitle  = $this->pageTitle . ' :: Add';
    	$output->productLang = SGL_Translation::getFallbackLangID();
    	
    	$this->edit_display($output);
    }
    
    function _cmd_insert(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	$usrId = SGL_Session::getUid();
    
    	SGL_DB::setConnection();
    	//  get new order number
    	$product = DB_DataObject::factory($this->conf['table']['product']);
    	$product->selectAdd();
    	$product->selectAdd('MAX(item_order) AS new_order');
    	$product->groupBy('item_order');
    	$maxItemOrder = $product->find(true);
    	unset($product);
    
    	//  insert record
    	$product = DB_DataObject::factory($this->conf['table']['product']);
    	$product->setFrom($input->product);
    	$product->product_id 	= $this->dbh->nextId('product');
    	$product->usr_id 		= $usrId;
    	$product->last_updated 	= $product->date_created = SGL_Date::getTime(true);
    	$product->item_order 	= $maxItemOrder + 1;
    
    	$success = $product->insert();
    
    	if ($success) {
    		SGL::raiseMsg('product saved successfully', true, SGL_MESSAGE_INFO);
    	} else {
    		SGL::raiseError('There was a problem inserting the record',
    				SGL_ERROR_NOAFFECTEDROWS);
    	}
    }
    
    function _cmd_edit(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	$output->template  = 'productEdit.html';
    	$output->action    = 'update';
    	$output->pageTitle = $this->pageTitle . ' :: Edit';
    	$product = DB_DataObject::factory($this->conf['table']['product']);
    	//  get product data
    	$product->get($input->productId);
    	$output->product = $product;
    
    }
    
    function _cmd_update(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	$product = DB_DataObject::factory($this->conf['table']['product']);
    	$product->get($input->product->product_id);
    	$product->setFrom($input->product);
    	$product->last_updated = SGL_Date::getTime(true);
    	$product->usr_id = SGL_Session::getUid();
    
    	$success = $product->update();
    	if ($success) {
    		SGL::raiseMsg('product updated successfully', true, SGL_MESSAGE_INFO);
    	} else {
    		SGL::raiseError('There was a problem updating the record',
    				SGL_ERROR_NOAFFECTEDROWS);
    	}
    }
    
    function edit_display(&$output) 
    {
    	$currency = DB_DataObject::factory($this->conf['table']['currency']);
        $result = $currency->find();
        
        $aCurrency  = array();
        if ($result > 0) {
        	while ($currency->fetch()) {
        		$curTitle = $currency->title;
        		$aCurrency[$currency->currency_id] = $curTitle;
        	}
        }
        
        //echo '<pre>';print_r($aCurrency);echo '</pre>';die;
        $output->aCurrency = $aCurrency;
        
        $output->aTax = array(
        		1 => 'Taxable Goods',
        		2 => 'Downloadable Products'
        		);
        
        $output->aDim = array(
        		1 => 'Centimeter',
        		2 => 'Millimeter',
        		3 => 'Inch',
        );
        
        $output->aWeight = array(
        		1 => 'Kilogram',
        		2 => 'Gram',
        		3 => 'Pound',
        		4 => 'Ounce',
        );
        
    }
    
    function _cmd_delete(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	if (is_array($input->aDelete)) {
    		foreach ($input->aDelete as $index => $productId) {
    			$product = DB_DataObject::factory($this->conf['table']['product']);
    			$product->get($productId);
    			unlink(SGL_WEB_ROOT.$product->image1);
    			$product->delete();
    			unset($product);
    		}
    		SGL::raiseMsg('product deleted successfully', true, SGL_MESSAGE_INFO);
    	} else {
    		SGL::raiseError('Incorrect parameter passed to ' . __CLASS__ . '::' .
    				__FUNCTION__, SGL_ERROR_INVALIDARGS);
    	}
    }
    
    function _cmd_reorder(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	$output->pageTitle = $this->pageTitle . ' :: Reorder';
    	$output->template  = 'productReorder.html';
    	$output->action    = 'reorderUpdate';
    	$productList = DB_DataObject::factory($this->conf['table']['product']);
    	$productList->orderBy('item_order');
    	$result = $productList->find();
    	if ($result > 0) {
    		$aproducts = array();
    		while ($productList->fetch()) {
    			$aproducts[$productList->product_id] = SGL_String::summarise($productList->title, 40);
    		}
    		$output->aproducts = $aproducts;
    	}
    }
    
    function _cmd_reorderUpdate(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	if (!empty($input->items)) {
    		$aNewOrder = explode(',', $input->items);
    		//  reorder elements
    		$pos = 1;
    		foreach ($aNewOrder as $productId) {
    			$product = DB_DataObject::factory($this->conf['table']['product']);
    			$product->get($productId);
    			$product->item_order = $pos;
    			$success = $product->update();
    			unset($product);
    			$pos++;
    		}
    		SGL::raiseMsg('products reordered successfully', true, SGL_MESSAGE_INFO);
    	} else {
    		SGL::raiseError('Incorrect parameter passed to ' . __CLASS__ . '::' .
    				__FUNCTION__, SGL_ERROR_INVALIDARGS);
    	}
    }
    
    function objectToArray( $data )
    {
    	if (is_array($data) || is_object($data))
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
