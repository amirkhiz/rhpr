<?php
/**
 * Table Definition for city
 */
require_once 'DB/DataObject.php';

class DataObjects_City extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'city';                // table name
    public $city_id;                         // int(11)  not_null primary_key
    public $title;                           // string(128)  not_null
    public $status;                          // int(6)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_City',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
