<?php
/**
 * Table Definition for poll_answer
 */
require_once 'DB/DataObject.php';

class DataObjects_Poll_answer extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'poll_answer';         // table name
    public $poll_answer_id;                  // int(11)  not_null primary_key
    public $poll_question_id;                // int(11)  not_null
    public $usr_id;                          // int(11)  not_null
    public $date_created;                    // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Poll_answer',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
