<?php
/**
 * Table Definition for branch
 */
require_once 'DB/DataObject.php';

class DataObjects_Branch extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'branch';              // table name
    public $branch_id;                       // int(11)  not_null primary_key
    public $usr_id;                          // int(11)  not_null
    public $company_id;                      // int(11)  not_null
    public $village_id;                      // int(11)  not_null
    public $city_id;                         // int(11)  not_null
    public $country_id;                      // int(11)  not_null
    public $name;                            // string(128)  not_null
    public $contact_person_1;                // string(255)  not_null
    public $contact_person_2;                // string(255)  not_null
    public $addr;                            // string(128)  not_null
    public $telephone_1;                     // string(16)  not_null
    public $telephone_2;                     // string(16)  not_null
    public $fax;                             // string(16)  not_null
    public $mobile;                          // string(16)  not_null
    public $history;                         // blob(65535)  not_null blob
    public $image;                           // string(255)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Branch',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
