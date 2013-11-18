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
// | SliderMgr.php                                                             |
// +---------------------------------------------------------------------------+
// | Author: Author: Ali Agha <alizowghi@gmail.com>                            |
// |         & Siavash AmirKhiz <amirkhiz@gmail.com>                           |
// +---------------------------------------------------------------------------+
// $Id: sliderMgr.php,v 1.26 2005/06/12 17:57:57 demian Exp $

require_once 'DB/DataObject.php';

/**
 * Allow users to see sliders.
 *
 * @package slider
 * @author  Author: Ali Agha <alizowghi@gmail.com> & Siavash AmirKhiz <amirkhiz@gmail.com>   
 * @version 1.0
 * @since   PHP 4.1
 */
class sliderMgr extends SGL_Manager
{
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle = 'sliders';
        $this->template  = 'slider1.html';

        $this->_aActionsMapping = array(
            'list' => array('list'),
        );
        
    }

    function validate($req, &$input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $this->validated       = true;
        $input->error          = array();
        $input->pageTitle      = $this->pageTitle;
        $input->masterTemplate = $this->masterTemplate;
        $input->template       = $this->template;
        $input->action         = ($req->get('action')) ? $req->get('action') : 'list';
    }

    function display(&$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

            $output->addJavascriptFile(array(
                'slider/js/jquery.easing.js',
                'slider/js/jquery.js',
                'slider/js/script.js',
                ), $optimize = false);
    }

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $sliderList = DB_DataObject::factory($this->conf['table']['slider']);
        $sliderList->orderBy('order_id');
        $result = $sliderList->find();
        $asliders  = array();
        if ($result > 0) {
            while ($sliderList->fetch()) {
                $sliderList->title = $sliderList->title;
                $sliderList->image1   = nl2br($sliderList->image1);
                $asliders[]           = clone($sliderList);
            }
        }
        $output->results = $asliders;
    
    }
}

?>
