<?php
/**
 * Table Definition for village
 */
require_once 'DB/DataObject.php';

class DataObjects_Village extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'village';             // table name
    public $village_id;                      // int(11)  not_null primary_key
    public $region_id;                       // int(11)  not_null
    public $title;                           // string(128)  not_null
    public $status;                          // int(6)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Village',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
