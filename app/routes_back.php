<?php
// Routes du back
$back_r = array(
	['GET', '/admin/', 'Default#dashboard', 'back_dashboard'],
	['GET|POST', '/admin/test', 'Default#test', 'back_test'],
	['GET|POST', '/admin/dump-sql', 'Default#dump', 'back_dump_sql'],

	/** Gestion des pages **/
	['GET|POST', '/admin/pages', 'Pages#listAll', 'back_pages_list'],
	['GET|POST', '/admin/pages/template', 'Pages#template', 'back_pages_choose_tpl'],
	['GET|POST', '/admin/pages/add/[:template]', 'Pages#addOrEdit', 'back_pages_add'],
	['GET|POST', '/admin/pages/edit/[:template]/[i:id]', 'Pages#addOrEdit', 'back_pages_edit'],
	['GET|POST', '/admin/pages/delete/[i:id]', 'Pages#delete', 'back_pages_delete'],

	/** Gestion newsletters */
	['GET|POST', '/admin/newsletters/list', 'Newsletters#listAll', 'back_newsletters_list'],
	['GET|POST', '/admin/newsletters/view/[i:id]?', 'Newsletters#view', 'back_newsletters_view'],
	['GET|POST', '/admin/newsletters/add', 'Newsletters#addEdit', 'back_newsletters_add'],
	['GET|POST', '/admin/newsletters/edit/[i:id]', 'Newsletters#addEdit', 'back_newsletters_edit'],
	['GET|POST', '/admin/newsletters/subscribers', 'Newsletters#subscribers', 'back_newsletters_subscribers'],

	/** Gestion utilisateurs */
	['GET|POST', '/admin/users', 'Users#listAll', 'back_users_list'],
	['GET|POST', '/admin/users/add', 'Users#addOrEdit', 'back_users_add'],
	['GET|POST', '/admin/users/edit/[i:id]', 'Users#addOrEdit', 'back_users_edit'],
	['GET|POST', '/admin/users/delete/[i:id]', 'Users#delete', 'back_users_delete'],
	['GET|POST', '/admin/users/warning/[i:id]', 'Users#warning', 'back_users_warning'],
	['GET|POST', '/admin/users/banish/[i:id]', 'Users#banish', 'back_users_banish'],
	['GET|POST', '/admin/users/note/[i:id]', 'Users#note', 'back_users_note'],
	['GET|POST', '/admin/users/note/delete/[i:id]/[:uid]', 'Users#noteDelete', 'back_delete_note'],

	/** Gestion des options & médias */
	['GET|POST', '/admin/options', 'Default#options', 'back_options'],
	['GET|POST', '/admin/medias/[:action]?', 'Medias#home', 'back_medias'],
	['GET|POST', '/admin/medias-popup', 'Medias#ckeditor', 'back_medias_ckeditor'],

	/** Ajax **/
	['GET|POST', '/admin/ajax/medias/upload', 'Medias#upload', 'ajax_media_upload'],
	['GET|POST', '/admin/ajax/medias/list', 'Medias#listAll', 'ajax_media_list'],

	['GET|POST', '/admin/ajax/get/search', 'Ajax#getSearch', 'ajax_get_search'],
	['GET|POST', '/admin/ajax/get/banish/[i:id]', 'Ajax#getBanish', 'ajax_get_banish'],

	['GET|POST', '/admin/ajax/notes/delete', 'Ajax#deleteNote', 'ajax_notes_delete'],
	['GET|POST', '/admin/ajax/notesF', 'Ajax#listNotes', 'ajax_notes_list'],
	['GET|POST', '/admin/ajax/notes/add', 'Ajax#addNote', 'ajax_note_add'],

	/** Gestion des bannis **/
	['GET|POST', '/admin/banish/list', 'Banish#listAll', 'back_banish_list'],
	['GET|POST', '/admin/banish/delete/[i:id]', 'Banish#delete', 'back_banish_delete'],

	/** Gestion des avertissements **/
	['GET|POST', '/admin/warning/list', 'Warning#listAll', 'back_warning_list'],
	['GET|POST', '/admin/warning/delete/[i:id]', 'Warning#delete', 'back_warning_delete'],

	/** Gestion des signalements **/
	['GET|POST', '/admin/report/list', 'Report#listAll', 'back_report_list'],
	['GET|POST', '/admin/report/message', 'Report#reportMessage', 'back_report_message'],
	['GET|POST', '/admin/report/delete', 'Report#delete', 'back_report_delete'],
	['GET|POST', '/admin/report/close', 'Report#close', 'back_report_close'],
	['GET|POST', '/admin/report/view/[i:id_post]', 'Report#view', 'back_report_view'],
	
	/** Gestion de la FAQ **/
	['GET|POST', '/admin/faq', 'Faq#listAll', 'back_faq_list'],
	['GET|POST', '/admin/faq/add/', 'Faq#add', 'back_faq_add'],
	['GET|POST', '/admin/faq/edit/[i:id]', 'Faq#edit', 'back_faq_edit'],
	['GET|POST', '/admin/faq/delete/[i:id]', 'Faq#delete', 'back_faq_delete'],

	/** Gestion du forum **/
	['GET|POST', '/forum/categories/list', 'ForumCategory#listAllCategory', 'forum_list_categories'],
	['GET|POST', '/forum/categories/add', 'ForumCategory#addCategory', 'forum_add_categories'],
	['GET|POST', '/forum/categories/edit/[i:id]', 'ForumCategory#editCategory', 'forum_edit_categories'],
	['GET|POST', '/forum/categories/delete/[i:id]', 'ForumCategory#deleteCategory', 'forum_delete_categories'],

);
