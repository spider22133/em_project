<?

$MESS['VK_TAB_NAME'] = 'ВКонтакте';
$MESS['VK_TAB_TITLE'] = 'Настройки ВКонтакте';
$MESS['VK_PAGE_TITLE'] = 'Аккаунты ВКонтакте';
$MESS['IS_VK_ENABLE'] = 'Включить модуль ВКонтакте';
$MESS['VK_SETTINGS'] = 'Настройки ВКонтакте';
$MESS['VK_ACCOUNTS'] = 'Аккаунты';
$MESS['VK_ACCOUNTS_NAME'] = 'Название';
$MESS['VK_IS_ENABLE'] = 'Аккаунт активен';
$MESS['IS_GROUP_PUBLISH'] = 'Публиковать от имени группы';
$MESS['GROUP_PUBLISH_ID'] = 'ID группы/пользователя';
$MESS['VK_ACCOUNTS_GROUP_ID'] = 'ID приложения';
$MESS['VK_ACCOUNTS_GROUP_ID_PLACEHOLDER'] = 'Введите ID Вашего приложения';
$MESS['VK_ACCOUNTS_GROUP_ID_STD'] = 'Использовать приложение "Автопубликации в ВКонтакте", вместо своего';
$MESS['VK_ACCOUNTS_ACCESS_TOKEN'] = 'Access Token';
$MESS['VK_ACCOUNTS_GET_ACCESS_TOKEN'] = 'Получить access token';
$MESS['GROUP_PUBLISH_GROUP'] = 'Группа';
$MESS['GROUP_PUBLISH_USER'] = 'Пользователь';
$MESS['POST_VK_PUBLISH_DATE'] = 'Дата публикации';
$MESS['POST_VK_PHOTO'] = 'Главная картинка';
$MESS['POST_VK_PHOTOS'] = 'Дополнительные картинки';
$MESS['POST_VK_LINK_ATTACHMENT'] = 'Ссылку во вложение';
$MESS['POST_VK_LINK'] = 'Ссылка на элемент (во вложении)';
$MESS['POST_VK_MESSAGE'] = 'Сообщение';
$MESS['vk_log_success'] = 'Журналировать успешную публикацию в ВКонтакте';
$MESS['vk_log_error'] = 'Журналировать ошибки при публикации';

$MESS['ALERT_NOT_ACCESS_TOKEN'] = 'Сначала впишите в поле ссылку с access_token';
$MESS['ALERT_NOT_GROUP_ID'] = 'Сначала заполните поле "ID приложения"';

$MESS['VK_ACCOUNTS_NAME_DESCRIPTION'] = 'Задайте название для данного аккаунта. Оно будет отображаться в списке доступных аккаунтов.';
$MESS['GROUP_PUBLISH_ID_DESCRIPTION'] = 'ID пользователя можно посмотреть в вк в Мои Настройки -&gt; Общее -&gt; Номер страницы.<br/>
	Для просмотра ID группы можно:<br/>
	<ul>
	1) перейти по ссылке "Статистика страницы" рассположенной справа на странице Вашей группы, https://vk.com/stats?gid=&lt;id_группы&gt; - в конце ссылки будет номер Вашей группы<br/>
	2) перейти в обсуждения, в адресной строке будет адрес https://vk.com/board&lt;id_группы&gt; - id_группы и будет ID группы<br/>
	3) посмотреть на странице Участники сообщества, для этого откройте ссылку Участники в новой вкладке, и в адресной строке будет https://vk.com/search?c[section]=people&c[group]=&lt;id_группы&gt; - id_группы и есть ID Вашей группы
	</ul>';
$MESS['VK_ACCOUNTS_GROUP_ID_DESCRIPTION'] = 'Вам необходимо зарегистрировать Standalone-приложение на страннице <a href="https://vk.com/editapp?act=create" target="_blank">https://vk.com/editapp?act=create</a>, и вписать ID приложения в поле "ID приложения"';
$MESS['VK_ACCOUNTS_ACCESS_TOKEN_DESCRIPTION'] = 'После того как Вы зарегистрировали Standalone-приложение и вписали его ID в поле выше,
	необходимо получить Access token, для этого перейдите по <a href="javascript:undefined" onClick="vettich_vk_get_access_token(this)">ссылке</a>, 
	в открывшемся окне авторизуйтесь, подтвердите права, и после того как на экране будет 
	сообщение "Пожалуйста, не копируйте данные из адресной строки для сторонних сайтов...", 
	скопируйте адрес из адресной строки в поле "Access Token",
	и нажмите кнопку "Преобразовать", чтобы получить Access token из адреса.';
$MESS['VK_access_token_button_HTML'] = '<div class="voptions-add-button" onClick="vettich_vk_access_token_mod(this)">Преобразовать</div>';

?> 
