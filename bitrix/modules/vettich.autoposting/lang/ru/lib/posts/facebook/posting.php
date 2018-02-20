<?

$MESS['FB_NAME'] = 'Facebook';
$MESS['FB_ERROR'] = 'Ошибка с кодом "#CODE#" в аккаунте "#ACC_NAME#" при публикации <a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=#IBLOCK_ID#&type=#IBLOCK_TYPE#&ID=#ID#">элемента</a>:<br/>
	#MESSAGE# <br/>
	<a href="http://fbdevwiki.com/wiki/Error_codes" target="_blank">Описание кодов ошибок Facebook (не офицальная документация)</a>';
$MESS['FB_SUCCESS'] = '<a href="#URL#" target="_blank">Запись</a> из "#ACC_NAME#" успешно опубликована';
$MESS['FB_WARNING_NOT_URL'] = '<a href="#URL#" target="_blank">Запись</a> из "#ACC_NAME#" успешно опубликована,
	но не была прикреплена картинка, так как не задано поле "Ссылка на элемент"';
$MESS['FB_PAGE_TITLE'] = 'Аккаунты Facebook';
$MESS['FB_TAB_NAME'] = 'Facebook';
$MESS['FB_TAB_TITLE'] = 'Настройки для Facebook';
$MESS['IS_FB_ENABLE'] = 'Включить модуль Facebook';
$MESS['FB_SETTINGS'] = 'Настройки Facebook';
$MESS['FB_ACCOUNTS'] = 'Аккаунты';
$MESS['FB_ACCOUNTS_NAME'] = 'Название';
$MESS['FB_IS_ENABLE'] = 'Аккаунт активен';
$MESS['FB_GROUP_ID'] = 'ID страницы';
$MESS['FB_GROUP'] = 'Токен для';
$MESS['FB_GROUP_PROFILE'] = 'Профиля';
$MESS['FB_GROUP_PAGE'] = 'Страницы';
$MESS['FB_APP_ID'] = 'ID приложения (App ID)';
$MESS['FB_APP_SECRET'] = 'Секрет приложения (App Secret)';
$MESS['FB_ACCOUNTS_ACCESS_TOKEN'] = 'Access Token';
$MESS['FB_ACCOUNTS_GET_ACCESS_TOKEN'] = 'Получить access token';
$MESS['GROUP_PUBLISH_GROUP'] = 'Группа';
$MESS['GROUP_PUBLISH_USER'] = 'Пользователь';
$MESS['POST_FB_PUBLISH_DATE'] = 'Дата публикации';
$MESS['POST_FB_PHOTO'] = 'Картинка';
$MESS['POST_FB_NAME'] = 'Заголовок блока';
$MESS['POST_FB_DESCRIPTION'] = 'Описание блока';
$MESS['POST_FB_LINK_ATTACHMENT'] = 'Ссылку во вложение';
$MESS['POST_FB_LINK'] = 'Ссылка на элемент';
$MESS['POST_FB_MESSAGE'] = 'Сообщение';
$MESS['fb_log_success'] = 'Журналировать успешную публикацию в Facebook';
$MESS['fb_log_error'] = 'Журналировать ошибки при публикации';
$MESS['ALERT_NOT_APP_ID'] = 'Сначала заполните поле \'App ID\' и \'App Secret\'';
$MESS['FB_ACCOUNTS_NAME_DESCRIPTION'] = 'Задайте название для данного аккаунта. Оно будет отображаться в списке доступных аккаунтов.';
$MESS['FB_GROUP_ID_DESCRIPTION'] = 'ID страницы, профиля или группы на которую нужно публиковать. Чтобы публиковать элементы на свой профиль, вместо его ID напишите "me", иначе будут ошибки при публикации';
$MESS['FB_HELP1_NAME'] = 'Инструкция для настройки аккаунта Facebook';
$MESS['FB_HELP1_TEXT'] = 'Для начала создайте приложение (если оно еще не создано) на 
	<a href="https://developers.facebook.com/apps/">этой странице</a>
	(после создания, обязательно укажите в настройках приложения домен, с которого будет публиковаться материалы, например http://example.ru).
	Далее введите в соответствующие поля данные о приложении (App ID, App Secret).
	Потом нажмите на кнопку "Получить Access Token" и в открывшемся окне подтвердите все права для приложения 
	(если они ранее не были подтвержденны).
	Затем в поле "ID страницы" впишите идентификатор своей страницы, группы или профиля.
	И не забудьте заполнить поле "Название", так как без него аккаунт не сохранится.
	В конце настройки нажмите "Сохранить".';
$MESS['FB_access_token_button'] = '<div class="voptions-add-button" onClick="vettich_fb_access_token_get(this)">Получить Access Token</div>';
$MESS['FB_WARNING_INCORRECT_URL'] = '<a href="#URL#" target="_blank">Запись</a> из "#ACC_NAME#" успешно опубликована. Но возникла ошибка с кодом "#CODE#" связанная с неправильной прикрепленной ссылкой "#ULINK#" при публикации <a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=#IBLOCK_ID#&type=#IBLOCK_TYPE#&ID=#ID#">элемента</a>:<br/>
#MESSAGE# <br/>';
$MESS['FB_SUCCESS_DELETE'] = '<a href="#URL#" target="_blank">Запись</a> из "#ACC_NAME#" успешно удалена';
$MESS['FB_ERROR_DELETE'] = 'Ошибка с кодом "#CODE#" в аккаунте "#ACC_NAME#" при удалении<a href="#URL#" target="_blank">записи</a>:<br/>
	#MESSAGE# <br/>';
