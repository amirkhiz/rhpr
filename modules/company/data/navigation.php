<?php
/*
Data setup in navigation files such as this one will allow you to add sections
to the 'Admin menu' that only the admin user can see.  There are 2 types of
nodes you can add:

 (1)    a SGL_NODE_ADMIN node
        These will appear at the top level in the admin menu
 (2)    a SGL_NODE_GROUP node
        These will appear as a subsection of the node inserted previously

Using this logic you can create node groups that correspond to modules, with a
SGL_NODE_ADMIN as the designated parent, and all children designated as
SGL_NODE_GROUP nodes.

*/

$aSections = array(
// Admin Menu
    array (
          'title'           => 'Companies and Members',
          'parent_id'       => SGL_NODE_ADMIN,
          'uriType'         => 'dynamic',
          'module'          => 'company',
          'manager'         => 'CompanyMgr.php',
          'actionMapping'   => '',
          'add_params'      => '',
          'is_enabled'      => 1,
          'perms'           => SGL_ADMIN,
            ),
    array (
          'title'           => 'Manage Company',
          'parent_id'       => SGL_NODE_GROUP,
          'uriType'         => 'dynamic',
          'module'          => 'company',
          'manager'         => 'CompanyMgr.php',
          'actionMapping'   => '',
          'add_params'      => '',
          'is_enabled'      => 1,
          'perms'           => SGL_ADMIN,
            ),
    array (
          'title'           => 'My Account',
          'parent_id'       => SGL_NODE_ADMIN,
          'uriType'         => 'dynamic',
          'module'          => 'company',
          'manager'         => 'AccountMgr.php',
          'actionMapping'   => '',
          'add_params'      => '',
          'is_enabled'      => 1,
          'perms'           => SGL_ADMIN,
            ),
    array (
          'title'           => 'View Profile',
          'parent_id'       => SGL_NODE_GROUP,
          'uriType'         => 'dynamic',
          'module'          => 'company',
          'manager'         => 'AccountMgr.php',
          'actionMapping'   => 'viewProfile',
          'add_params'      => '',
          'is_enabled'      => 1,
          'perms'           => SGL_ADMIN,
            ),
    // Company Menu
    array (
          'title'           => 'My Account',
          'parent_id'       => SGL_NODE_USER,
          'uriType'         => 'dynamic',
          'module'          => 'company',
          'manager'         => 'AccountMgr.php',
          'actionMapping'   => '',
          'add_params'      => '',
          'is_enabled'      => 1,
          'perms'           => SGL_MEMBER,
            ),
);
?>
