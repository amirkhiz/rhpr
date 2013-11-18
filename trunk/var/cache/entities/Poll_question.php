<?php
/**
 * Table Definition for poll_question
 */
require_once 'DB/DataObject.php';

class DataObjects_Poll_question extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'poll_question';       // table name
    public $poll_question_id;                // int(11)  not_null primary_key
    public $poll_id;                         // int(11)  not_null
    public $title;                           // string(128)  not_null
    public $order_id;                        // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Poll_question',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
