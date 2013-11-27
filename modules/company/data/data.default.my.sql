INSERT INTO module VALUES ({SGL_NEXT_ID}, 1, 'company', 'Company', 'Company Management Module', 'company/company', 'company.png', 'Siavash Habil Amirkhiz', NULL, NULL, NULL);

SELECT @moduleId := MAX(module_id) FROM module;

INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'companymgr', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'companymgr_cmd_add', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'companymgr_cmd_insert', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'companymgr_cmd_edit', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'companymgr_cmd_update', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'companymgr_cmd_delete', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'companymgr_cmd_list', '', @moduleId);

#member role perms
SELECT @permissionId := permission_id FROM permission WHERE name = 'companymgr_cmd_list';
INSERT INTO role_permission VALUES ({SGL_NEXT_ID}, 2, @permissionId);
