<?php

$aSections = array(
    array (
      'title'           => 'Poll Managment',
      'parent_id'       => SGL_NODE_ADMIN,
      'uriType'         => 'dynamic',
      'module'          => 'poll',
      'manager'         => 'AdminPollMgr.php',
      'actionMapping'   => '',              // eg: "edit"
      'add_params'      => '',              // eg: "frmArticleID/23"
      'is_enabled'      => 1,
      'perms'           => SGL_ADMIN,       // role id or constant, eg: 1 for admin
                                            //   multiple possible, as comma-separated string
                                            //   eg: "2,4,6,7"
        ),
    array (
      'title'           => 'Poll Manager',
      'parent_id'       => SGL_NODE_GROUP,
      'uriType'         => 'dynamic',
      'module'          => 'poll',
      'manager'         => 'AdminPollMgr.php',
      'actionMapping'   => '',
      'add_params'      => '',
      'is_enabled'      => 1,
      'perms'           => SGL_ADMIN,
        ),
    );
?>
