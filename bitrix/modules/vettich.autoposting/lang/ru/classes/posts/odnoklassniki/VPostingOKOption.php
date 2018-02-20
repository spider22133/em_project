<?

$MESS['ODNOKLASSNIKI_TAB_NAME'] = 'Одноклассники';
$MESS['ODNOKLASSNIKI_TAB_TITLE'] = 'Настройки Одноклассники';
$MESS['ODNOKLASSNIKI_PAGE_TITLE'] = 'Аккаунты Одноклассники';
$MESS['IS_ODNOKLASSNIKI_ENABLE'] = 'Включить модуль Одноклассники';
$MESS['ODNOKLASSNIKI_SETTINGS'] = 'Настройки Одноклассники';
$MESS['ODNOKLASSNIKI_ACCOUNTS'] = 'Аккаунты';
$MESS['ODNOKLASSNIKI_ACCOUNTS_NAME'] = 'Название';
$MESS['ODNOKLASSNIKI_IS_ENABLE'] = 'Аккаунт активен';
$MESS['ODNOKLASSNIKI_IS_GROUP_PUBLISH'] = 'От имени группы';
$MESS['ODNOKLASSNIKI_GROUP_ID'] = 'ID группы';
$MESS['ODNOKLASSNIKI_API_ID'] = 'Application ID';
$MESS['ODNOKLASSNIKI_API_PUBLIC_KEY'] = 'Публичный ключ приложения';
$MESS['ODNOKLASSNIKI_API_SECRET_KEY'] = 'Секретный ключ приложения';
$MESS['ODNOKLASSNIKI_ACCESS_TOKEN'] = 'Access Token';
$MESS['ODNOKLASSNIKI_NOTE'] = 'Инструкция для заполнения полей';
$MESS['POST_ODNOKLASSNIKI_PUBLISH_DATE'] = 'Дата публикации';
$MESS['POST_ODNOKLASSNIKI_PHOTO'] = 'Главная картинка';
$MESS['POST_ODNOKLASSNIKI_PHOTO_OTHER'] = 'Дополнительные картинки';
$MESS['POST_ODNOKLASSNIKI_LINK'] = 'Ссылка';
$MESS['POST_ODNOKLASSNIKI_MESSAGE'] = 'Сообщение';
$MESS['odnoklassniki_log_success'] = 'Журналировать успешную публикацию в Одноклассники';
$MESS['odnoklassniki_log_error'] = 'Журналировать ошибки при публикации';
$MESS['ODNOKLASSNIKI_ACCOUNTS_NAME_DESCRIPTION'] = 'Задайте название для данного аккаунта. Оно будет отображаться в списке доступных аккаунтов.';
$MESS['ODNOKLASSNIKI_NOTE_TEXT'] = '<div align="justify">
	<a href="http://ok.ru/devaccess" target="_blank">Зарегистрируйтесь</a> как разработчик.
	<a href="http://apiok.ru/wiki/pages/viewpage.action?pageId=42476486" targer="_blank">Создайте приложение</a> по
	<a href="http://ok.ru/dk?st.cmd=appEdit&st._aid=Apps_Info_MyDev_AddApp" target="_blank">ссылке</a> 
	(если ссылка не работает, то перейдите в одноклассниках Игры -> Мои загруженные -> Добавить приложение).
	Заполните поля "Название", "Короткое имя", "Описание" соответствующей информацией.
	Далее выберите "Тип приложения" -> "External" (Внешнее приложение), поле "Статус" -> "Публичное",
	установите следующие права для приложения в положение "обязательно" (или "необязательно"):
	<ul>
		<li>Установка статуса (SET_STATUS)</li>
		<li>Изменение фотографий и фотоальбомов (PHOTO_CONTENT)</li>
		<li>Доступ к основной информации (VALUABLE_ACCESS)</li>
		<li>Доступ к группам (GROUP_CONTENT)</li>
	</ul>
	Если какое то право отсутствует в списке, необходимо его запросить у администрации Одноклассников по почте api-support@ok.ru.
	Правила оформления письма:<br>
	<ul>
		Тема: Права приложения<br>
		Сообщение: 
		<ul>
			ID приложения: &lt;App id вашего приложения><br>
			Права: <права, например VALUABLE_ACCESS, GROUP_CONTENT><br>
			Цель: <напишите цель получения прав>
		</ul>
	</ul>
	После того как Вы создали приложение, Вам на почту придет письмо с данными созданного приложения App ID, App public key и App secret key.
	Эти данные необходимо вписать в соответствующие поля выше.
	Для получения Access Token необходимо пройти в настройки вашего приложения, и внизу страницы возле поля "Вечный access_token" будет кнопка "Получить access_token",
	при нажатии на которую отобразится токен, который нужно скопировать в соответствующее поле выше.
	</div>';
$MESS['POST_ODNOKLASSNIKI_PUBLISH_DATE_HELP'] = 'Выберите дату для отложенной публикации, обычно это "DATE_ACTIVE_FROM". Механизм отложенной публикации реализуется в самой соц. сети. (необязательный параметр)';
$MESS['POST_ODNOKLASSNIKI_PHOTO_HELP'] = 'Выберите главную картинку для прикрепления ее к публикации. (необязательный)';
$MESS['POST_ODNOKLASSNIKI_PHOTO_OTHER_HELP'] = 'Выберите дополнительные картинки, которые необходимо прикрепить к публикации. (необязательный)';
$MESS['POST_ODNOKLASSNIKI_LINK_HELP'] = 'Выберите поле, из которого будет сформирована ссылка, прикрепляемая к публикации. (необязательный)';
$MESS['YES'] = 'Да';
$MESS['NO'] = 'Нет';
$MESS['POST_ODNOKLASSNIKI_MESSAGE_DESCRIPTION'] = '';
$MESS['SAVE_BUTTON'] = 'Сохранить';
$MESS['APPLY_BUTTON'] = 'Применить';
$MESS['ODNOKLASSNIKI_TAB_VIDEO_NAME'] = 'Видео инструкция';
$MESS['ODNOKLASSNIKI_TAB_VIDEO_TITLE'] = 'Видео инструкция';
$MESS['ODNOKLASSNIKI_EDIT_PAGE_TITLE'] = 'Редактирование аккаунта Одноклассники';
$MESS['POST_ODNOKLASSNIKI_MESSAGE_HELP'] = 'Заполните шаблон сообщения. Данное поле является <b>обязательным</b> - если не заполнены поля для вложений (картинки, ссылка)';
$MESS['ODNOKLASSNIKI_ADD_PAGE_TITLE'] = 'Добавление аккаунта Одноклассники';
