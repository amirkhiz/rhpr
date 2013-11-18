<?php
/**
 * Table Definition for ads
 */
require_once 'DB/DataObject.php';

class DataObjects_Ads extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'ads';                 // table name
    public $ads_id;                          // int(11)  not_null primary_key
    public $usr_id;                          // int(11)  not_null
    public $title;                           // string(100)  not_null
    public $description;                     // blob(65535)  not_null blob
    public $start_date;                      // date(10)  not_null binary
    public $end_date;                        // date(10)  not_null binary
    public $weight;                          // int(11)  not_null
    public $image;                           // blob(65535)  not_null blob
    public $url;                             // string(255)  not_null
    public $block_id;                        // int(11)  not_null
    public $hits;                            // int(11)  not_null
    public $clicks;                          // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Ads',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
