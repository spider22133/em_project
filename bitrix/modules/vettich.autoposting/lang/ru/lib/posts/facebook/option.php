<?

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
$MESS['FB_HELP1_TEXT'] = 'Сначала создайте приложение (если у Вас его еще нет) на 
	<a href="https://developers.facebook.com/apps/">этой странице</a>.
	Далее, после создания, перейдите в настройки (Settings) и добавьте платформу "WebSite" и введите в появившееся
	поле адресс вашего сайта (вместе с http). Так же заполните поле Контактный Email (Contact Email).
	И сохраните изменения. Потом перейдите в Проверку приложения (App Review), и включите первый пункт "Make имя_приложения public?"
	Снова перейдите в настройки, и скопируйте оттуда поля App ID и App Secret в соответствующие поля на этой странице.
	Затем в поле "ID страницы" впишите идентификатор своей страницы, группы или профиля.
	И нажмите на кнопку "Получить Access Token" и в открывшемся окне подтвердите все права для приложения.
	После этого можно сохранять, Вы настроили приложение';
$MESS['FB_HELP1_TEXT_old'] = 'Для начала создайте приложение (если оно еще не создано) на 
	<a href="https://developers.facebook.com/apps/">этой странице</a>
	(после создания, обязательно укажите в настройках приложения домен, с которого будет публиковаться материалы, например http://example.ru, там же укажите свой email, и на вкладке App Review сделайте свое приложение публичным).
	Далее введите в соответствующие поля данные о приложении (App ID, App Secret).
	Потом нажмите на кнопку "Получить Access Token" и в открывшемся окне подтвердите все права для приложения 
	(если они ранее не были подтвержденны).
	Затем в поле "ID страницы" впишите идентификатор своей страницы, группы или профиля.
	И не забудьте заполнить поле "Название", так как без него аккаунт не сохранится.
	В конце настройки нажмите "Сохранить".';
$MESS['FB_access_token_button'] = '<div class="voptions-add-button" onClick="vettich_fb_access_token_get(this)">Получить Access Token</div>';
$MESS['POSTS_FB_PUBLISH_DATE_HELP'] = 'Выберите дату для отложенной публикации, обычно это "DATE_ACTIVE_FROM". Используются средства самой соц. сети. (необязательный параметр)';
$MESS['POSTS_FB_LINK_HELP'] = 'Выберите ссылку на элемент. С опубликованного поста в Facebook, она будет ввести на сайт, с детальным просмотром элемента - если выбрано поле DETAIL_PAGE_URL. (параметр необязательный)';
$MESS['POST_FB_PHOTO_HELP'] = 'Выберите картинку, которая будет размещена в блоке со ссылкой. <b>Внимание</b>, если Вы указали картинку, то необходимо указать и Ссылку на элемент, иначе Facebook выдаст ошибку. (необязательный параметр)';
$MESS['POSTS_FB_NAME_HELP'] = 'Выберите заголовок для блока со ссылкой. (необязательный)';
$MESS['POST_FB_DESCRIPTION_HELP'] = 'Выберите описание для блока со ссылкой. (необязательный)';
$MESS['FB_GROUP_ID_HELP'] = 'Укажите ID группы, или страницы, на которую необходимо будет публиковать. Для указания своего профиля, не нужно указывать его ID, нужно написать "me", это и будет тот профиль, с которого был получен Access Token.';
$MESS['YES'] = 'Да';
$MESS['NO'] = 'Нет';
$MESS['SAVE_BUTTON'] = 'Сохранить';
$MESS['APPLY_BUTTON'] = 'Применить';
$MESS['FB_TAB_VIDEO_NAME'] = 'Видео инструкция';
$MESS['FB_TAB_VIDEO_TITLE'] = 'Видео инструкция';
$MESS['FB_HELP_VIDEO_NAME'] = '';
$MESS['FB_HELP_VIDEO_TEXT'] = '<iframe width="640" height="360" src="#URL#" frameborder="0" allowfullscreen></iframe>';
$MESS['FB_EDIT_PAGE_TITLE'] = 'Редактирование аккаунта Facebook';
$MESS['POST_FB_MESSAGE_HELP'] = 'Заполните шаблон сообщения. Поле является <b>обязательным</b> - если не заполнены поля для вложений (картинка с ссылкой), и является <b>не обязательным</b> - если выше перечисленные поля заполнены';
$MESS['FB_ADD_PAGE_TITLE'] = 'Добавление аккаунта Facebook';
$MESS['FB_MESSAGE_DEFAULT'] = '#NAME#
#PREVIEW_TEXT#
Подробней: #DETAIL_PAGE_URL#';
$MESS['FB_PUBLICATION_MODE'] = 'Режим обновления поста при изменении элемента инфоблока';
$MESS['FB_PUBLICATION_MODE_UPDATE'] = 'Обновлять сам пост (По умолчанию)';
$MESS['FB_PUBLICATION_MODE_DEL_ADD'] = 'Удалять старый пост и добавлять новый';
$MESS['FB_PUBLICATION_MODE_NONE'] = 'Ничего не выполнять';
$MESS['FB_PUBLICATION_MODE_HELP'] = '';
$MESS['FB_IS_STANDARD'] = 'Использовать стандартное приложение';
