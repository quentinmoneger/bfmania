<?php
// Routes du front
$front_r = array(
	['GET|POST', '/', 'Default#home', 'default_home'],
	['GET|POST', '/contact', 'Default#contact', 'default_contact'],
	['GET|POST', '/page/[:url]', 'Default#page', 'default_page'],
	['GET|POST', '/jouez/[belote|tarot|coinche:action]', 'Default#playGame', 'default_play_game'],
	

	/* Connexion utilisateur */
	['GET|POST', '/login', 'Users#login', 'users_login'],
	['GET|POST', '/logout', 'Users#logout', 'users_logout'],
	['GET|POST', '/signup', 'Users#signup', 'users_signup'],
	['GET|POST', '/forgot-password', 'Users#forgotPassword', 'users_forgot_password'],
	['GET|POST', '/reset-password', 'Users#resetPassword', 'users_reset_password'],


	/* Compte utilisateur */
	['GET|POST', '/account', 'Users#account', 'users_account'],
	['GET|POST', '/account/modified', 'Users#editAccount', 'users_account_modified'],
	['GET|POST', '/account/password', 'Users#editPassword', 'users_account_password'],
	['GET|POST', '/account/img', 'Users#editImg', 'users_account_image'],

	/* FAQ */
	['GET|POST', '/support/faq/', 'Default#faqHome', 'default_faq_home'],
	['GET|POST', '/support/faq/[:category]', 'Default#faqCategory', 'default_faq_category'],

	/* Messages */
	['GET|POST', '/messages/home', 'Messages#messagesHome', 'messages_home'],
	['GET|POST', '/messages/mp/readunread', 'Messages#readUnread', 'messages_read_message'],
	['GET|POST', '/messages/mp/write', 'Messages#writeMessage', 'messages_write_message'],
	['GET|POST', '/messages/mp/replyall', 'Messages#replyMessageAll', 'messages_reply_all_message'],
	['GET|POST', '/messages/mp/reply', 'Messages#replyMessage', 'messages_reply_message'],
	['GET|POST', '/messages/mp/delete', 'Messages#deleteMessage', 'messages_delete_message'],
	['GET|POST', '/messages/mp/autocomplete', 'Messages#autocompleteMessage', 'messages_autocomplete_message'],

	/* Staff */
	['GET|POST', '/support/staff/', 'Default#staffList', 'user_staff_show'],

	/* Profil utilisateurs */
	['GET|POST', '/membres', 'Profile#searchViewProfile', 'profile_search_view'],
	['GET|POST', '/membre/[:username]', 'Profile#showProfile', 'profile_show'],
	['GET|POST', '/membres/result', 'Profile#searchProfile', 'profile_search'],

	/* Webservice */
	['GET|POST', '/ws/_auth', 'WebService#auth', 'webservice_auth'],
	['GET|POST|OPTIONS', '/ws/_update', 'WebService#update', 'webservice_update'],

	/* Forum */
	['GET|POST', '/forum/', 'Forum#listForum', 'list_forum'],
	['GET|POST', '/forum/topics/[:url]/[i:page]?', 'Topic#listTopics', 'list_topics'],
	['GET|POST', '/forum/topics/view/[i:id]/[i:page]?', 'Topic#viewTopic', 'view_topic'],
	['GET|POST', '/forum/topics/delete/[i:id]/[i:author_role]', 'Topic#deleteTopic', 'delete_topic'],
	['GET|POST', '/forum/topics/add/[i:category]/[:url]', 'Topic#addTopic', 'add_topic'],
	['GET|POST', '/forum/topic/pin/[:url]', 'Topic#pinTopic', 'pin_topic'],
	['GET|POST', '/forum/post/add/[i:id_topic]', 'Posts#addPost', 'add_post'],
	['GET|POST', '/forum/post/update/[i:id_post]/[i:id_topic]/[i:author_role]', 'Posts#updatePost', 'update_post'],
	['GET|POST', '/forum/post/delete/[i:id_post]/[i:id_topic]/[i:author_role]', 'Posts#deletePost', 'delete_post'],

	/* Chat */
	['GET|POST', '/chat', 'Chat#home', 'chat_home'],

	/* Ajax */
	['GET|POST', '/ajax/load-emoji', 'EmojiController#ajaxResult', 'emoji_load_json'],
	['GET|POST', '/ajax/getscore', 'Ajax#getScore', 'ajax_get_score'],

	/** Inscription newsletter */
	['GET|POST', '/subscribe-newsletters', 'Newsletters#subscribe', 'newsletters_subscribe'],
	['GET|POST', '/unsubscribe-newsletters', 'Newsletters#unsubscribe', 'newsletters_unsubscribe'],
	['GET|POST', '/unsubscribe-submit-newsletters', 'Newsletters#unsubscribesubmit', 'newsletters_unsubscribe_submit'],
	['GET|POST', '/[:unsubscribe]', 'Default#homeUnsubscribe', 'default_unsubscribe_home'],

	/* Game */
	['GET|POST', '/game/[:game_name]', 'Game#view', 'game_view'],
	['GET|POST', '/game/score/', 'Game#score', 'game_score'],
	['GET|POST', '/game/load/', 'Game#loadScore', 'game_load_score']
);
