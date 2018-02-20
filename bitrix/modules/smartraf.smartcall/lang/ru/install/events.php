<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

//EVENT_TYPE RU
$MESS['SMARTCALL_EVENTTYPE_NAME']     = 'FILL_CALLBACK_FORM_EVA';
$MESS['RU_SMARTCALL_TYPENAME']        = 'Отправка формы обратного звонка / заказа';
$MESS['EN_SMARTCALL_TYPENAME']        = 'Send form callback_eva';
$MESS['RU_SMARTCALL_TYPEDESCRIPTION'] = '
#AUTHOR# - автор сообщения (ФИО) 
#AUTHOR_PHONE# - телефон 
#TIME_TOCALL# - удобное время звонка 
#MESSAGE# - текст сообщения 
#EMAIL_TO# - email получателя письма (поле Кому)';


//EVENT_MESSAGE ADMIN
$MESS['EMESSAGE_EMAIL_FROM']       = '#DEFAULT_EMAIL_FROM#';
$MESS['EMESSAGE_EMAIL_TO']         = '#EMAIL_TO#';
$MESS['EMESSAGE_SUBJECT_ADMIN']    = '#SITE_NAME#: Сообщение из формы обратного звонка';
$MESS['EMESSAGE_TEXT']          = 'Информационное сообщение сайта #SITE_NAME#
------------------------------------------

ФИО: #AUTHOR#
Телефон: #AUTHOR_PHONE#
Удобное время звонка: #TIME_TOCALL#

#MESSAGE# 

------
Сообщение сгенерировано автоматически.';