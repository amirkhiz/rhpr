<?php
/**
 * Table Definition for region
 */
require_once 'DB/DataObject.php';

class DataObjects_Region extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'region';              // table name
    public $region_id;                       // int(11)  not_null primary_key
    public $city_id;                         // int(11)  not_null
    public $title;                           // string(128)  not_null
    public $status;                          // int(6)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Region',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
