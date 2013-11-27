<?php
/**
 * Table Definition for company
 */
require_once 'DB/DataObject.php';

class DataObjects_Company extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'company';             // table name
    public $company_id;                      // int(11)  not_null primary_key
    public $usr_id;                          // int(11)  not_null
    public $category_id;                     // int(11)  not_null
    public $village_id;                      // int(11)  not_null
    public $region_id;                       // int(11)  not_null
    public $city_id;                         // int(11)  not_null
    public $name;                            // string(128)  not_null
    public $first_name;                      // string(128)  
    public $last_name;                       // string(128)  
    public $contact_person_1;                // string(255)  not_null
    public $contact_person_2;                // string(255)  not_null
    public $telephone_1;                     // string(16)  
    public $telephone_2;                     // string(16)  not_null
    public $fax;                             // string(16)  not_null
    public $mobile;                          // string(16)  
    public $site_name;                       // string(255)  not_null
    public $facebook;                        // string(128)  not_null
    public $twitter;                         // string(128)  not_null
    public $email;                           // string(128)  
    public $email_2;                         // string(128)  not_null
    public $keywords;                        // blob(65535)  not_null blob
    public $logo;                            // string(255)  not_null
    public $image;                           // string(255)  not_null
    public $addr;                            // string(128)  
    public $history;                         // blob(65535)  not_null blob
    public $sex;                             // int(4)  not_null
    public $blood;                           // string(8)  not_null
    public $has_branch;                      // int(6)  not_null
    public $date_created;                    // datetime(19)  binary
    public $last_updated;                    // datetime(19)  binary

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Company',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
