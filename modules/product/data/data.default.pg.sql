-- Last edited: Antonio J. Garcia 2007-04-24
-- Data dump for /faq
-- leave subqueries on a single line in order that table prefixes works
BEGIN;


INSERT INTO module VALUES ({SGL_NEXT_ID}, 1, 'faq', 'سوال‌های متداول', 'با استفاده از ماژول سوال‌های متداول امکان ایجاد یک لیست از سوال‌هایی که به کررات پرسیده می‌شوند به همراه جواب‌هایشان برای قرارگرفتن در سایت وجود دارد.', 'faq/faq', 'faqs.png', '', NULL, NULL, NULL);

INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr', '', (
    SELECT max(module_id) FROM module
    ));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_add', '', (
    SELECT max(module_id) FROM module
    ));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_insert', '', (
    SELECT max(module_id) FROM module
    ));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_edit', '', (
    SELECT max(module_id) FROM module
    ));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_update', '', (
    SELECT max(module_id) FROM module
    ));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_delete', '', (
    SELECT max(module_id) FROM module
    ));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_list', '', (
    SELECT max(module_id) FROM module
    ));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_reorder', '', (
    SELECT max(module_id) FROM module
    ));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_reorderUpdate', '', (
    SELECT max(module_id) FROM module
    ));

-- member role perms
INSERT INTO role_permission VALUES ({SGL_NEXT_ID}, 2, (
    SELECT permission_id FROM permission WHERE name = 'faqmgr_cmd_list'
    ));


COMMIT;

