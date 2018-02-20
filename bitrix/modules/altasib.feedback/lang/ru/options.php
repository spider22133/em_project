<?
/**
 * Company developer: ALTASIB
 * Site: http://www.altasib.ru
 * E-mail: dev@altasib.ru
 * @copyright (c) 2006-2017 ALTASIB
 */


$MESS['ALX_FEEDBACK_SERVER'] = "Сервер CRM:";
$MESS['ALX_FEEDBACK_PATH'] = "Путь:";
$MESS['ALX_FEEDBACK_LOGIN'] = "Логин:";
$MESS['ALX_FEEDBACK_PASS'] = "Пароль:";
$MESS['ALX_COMMON_CRM'] = "Единые настройки для всех сайтов:";
$MESS['ALX_FEEDBACK_FOR_SITE'] = "Настройки для сайта ";
$MESS['ALX_FEEDBACK_HASH'] = "Хэш авторизации (заполнится автоматически):";
$MESS['ALX_FEEDBACK_LOGIN_PASS_ERROR'] = "Для настройки CRM введите логин и пароль";
$MESS['ALX_FEEDBACK_ERROR_CONNECTION'] = "Соединение не установлено";
$MESS['ALX_FEEDBACK_SAVE_SETTINGS'] = "Настройки CRM сохранены";
$MESS['BUTTON_SAVE'] = "Сохранить";
$MESS['BUTTON_RESET'] = "Сбросить";
$MESS['BUTTON_DEF'] = "По умолчанию";

$MESS['ALX_FEEDBACK_SITE_KEY'] = "Ключ reCAPTCHA для данного сайта:<br/><i>Для работы с reCAPTCHA <a href='https://www.google.com/recaptcha/admin#list' target='_blank'>зарегистрируйте</a> сайт в Google</i>";
$MESS['ALX_FEEDBACK_SECRET_KEY'] = "Секретный ключ reCAPTCHA для сайта:";
$MESS['ALX_FEEDBACK_LOGIN_PASS_ERROR_FOR'] = "Для настройки CRM введите логин и пароль для сайта ";
$MESS['ON_CHANGE_COMMON_SETTS_WARNING'] = "Внимание! Все несохраненные изменения, внесенные в форму, не будут сохранены. Продолжить?";
$MESS['ALX_COMMON_CRM_AFTER_CHECK'] = "<br/><i>После того как вы отметите этот чекбокс, выведутся настройки для всех сайтов.</i>";
$MESS['ALX_COMMON_CRM_AFTER_UNCHECK'] = "<br/><i>После того как вы снимите этот чекбокс, выведутся настройки для каждого сайта.</i>";
$MESS['ALX_RECAPTCHA_SUB'] = "Настройки виджета reCAPTCHA";
$MESS['ALX_FEEDBACK_CRM_ALL'] = "Настройки CRM для всех сайтов";

$MESS['ALTASIB_IS'] = "Магазин готовых решений для 1С-Битрикс";
$MESS['ALTASIB_FEEDBACK_DESCR'] = '<b>Информация для разработчиков</b><br/><br/>
<b>События модуля</b><br/><br/>
<table class="internal altasib_events" width="100%">
	<thead>
		<tr>
			<th>Событие</th>
			<th>Вызывается</th>
			<th>Метод</th>
			<th>С версии</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>OnBeforeSendLead</td>
			<td>Перед отсылкой данных в лиды Bitrtix24</td>
			<td>AltasibFeedbackCRM::AddLead</td>
			<td>1.8.0</td>
		</tr>
	</tbody>
</table>
<br/>
<div id="altasib_description_open_btn">
	<span class="altasib_description_open_text">Читать дальше</span>
</div>
<div id="altasib_description_full">
<pre>
// Пример обработчика события OnBeforeSendLead:
AddEventHandler("altasib.feedback", "OnBeforeSendLead", "OnBeforeSendLeadHandler");
function OnBeforeSendLeadHandler($arFields) {
	// Установка источника лида в зависимости от заполнения полей формы
	if($_REQUEST["type_question_FID1"] == 19) // если выбран раздел ИБ (категория) с ид = 19
		$arFields["SOURCE_ID"] = "TRADE_SHOW"; // источник лида - Выставка
	else
		$arFields["SOURCE_ID"] = "WEB"; // иначе - Веб-сайт
}
</pre>
<div id="altasib_description_close_btn">
	<span class="altasib_description_open_text">Свернуть</span>
</div>
</div>
';
?>