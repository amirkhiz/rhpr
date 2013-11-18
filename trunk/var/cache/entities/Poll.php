<?php
/**
 * Table Definition for poll
 */
require_once 'DB/DataObject.php';

class DataObjects_Poll extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'poll';                // table name
    public $poll_id;                         // int(11)  not_null primary_key
    public $title;                           // string(128)  not_null
    public $poll_type;                       // string(64)  not_null
    public $date_created;                    // datetime(19)  not_null binary
    public $order_id;                        // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Poll',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
