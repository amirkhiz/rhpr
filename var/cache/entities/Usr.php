<?php
/**
 * Table Definition for usr
 */
require_once 'DB/DataObject.php';

class DataObjects_Usr extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'usr';                 // table name
    public $usr_id;                          // int(11)  not_null primary_key
    public $organisation_id;                 // int(11)  
    public $role_id;                         // int(11)  not_null
    public $category_id;                     // int(11)  not_null
    public $village_id;                      // int(11)  not_null
    public $city_id;                         // int(11)  not_null
    public $country_id;                      // int(11)  not_null
    public $name;                            // string(128)  not_null
    public $username;                        // string(64)  
    public $passwd;                          // string(32)  
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
    public $post_code;                       // string(16)  
    public $history;                         // blob(65535)  not_null blob
    public $sex;                             // int(4)  not_null
    public $blood;                           // string(8)  not_null
    public $is_email_public;                 // int(6)  
    public $is_acct_active;                  // int(6)  
    public $security_question;               // int(6)  
    public $security_answer;                 // string(128)  
    public $date_created;                    // datetime(19)  binary
    public $created_by;                      // int(11)  
    public $last_updated;                    // datetime(19)  binary
    public $updated_by;                      // int(11)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Usr',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
