<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
IncludeModuleLangFile(__FILE__);

//Тип почтового события
$eventType = new CEventType;
$arEventTypeFields = array(
	0 => array(
		'LID'         => 'ru',
		'EVENT_NAME'  => GetMessage('SMARTCALL_EVENTTYPE_NAME'),
		'NAME'        => GetMessage('RU_SMARTCALL_TYPENAME'),
		'DESCRIPTION' => GetMessage('RU_SMARTCALL_TYPEDESCRIPTION'),
	),
	1 => array(
		'LID'         => 'en',
		'EVENT_NAME'  => GetMessage('SMARTCALL_EVENTTYPE_NAME'),
		'NAME'        => GetMessage('EN_SMARTCALL_TYPENAME'),
		'DESCRIPTION' => GetMessage('RU_SMARTCALL_TYPEDESCRIPTION'),
	),
);
foreach($arEventTypeFields as $arField)
{
	$rsET = $eventType->GetByID($arField['EVENT_NAME'], $arField['LID']);
	$arET = $rsET->Fetch();

	//v2.0.0
	if(!$arET)
		$eventType->Add($arField);
	else
		$eventType->Update(array('ID'=>$arET['ID']),$arField);
}

unset($arField);

//Почтовые шаблоны
if(!empty($this->SITE_ID))
{
	$eventM = new CEventMessage;
		$arEventMessFields = array(
			0 => array(
				'ACTIVE'     => 'Y',
				'EVENT_NAME' => GetMessage('SMARTCALL_EVENTTYPE_NAME'),
				'LID'        => $this->SITE_ID,
				'EMAIL_FROM' => GetMessage('EMESSAGE_EMAIL_FROM'),
				'EMAIL_TO'   => GetMessage('EMESSAGE_EMAIL_TO'),
				'SUBJECT'    => GetMessage('EMESSAGE_SUBJECT_ADMIN'),
				'BODY_TYPE'  => 'text',
				'MESSAGE'    => GetMessage('EMESSAGE_TEXT'),
			),
		);

		foreach($arEventMessFields as $arField)
		{
			$rsMess = $eventM->GetList($by = 'id', $order = 'asc', array(
				'SUBJECT' => $arField['SUBJECT'],
				'LID'     => $arField['LID']
			));
			if(!$arMess = $rsMess->Fetch())
				$eventM->Add($arField);
		}
	unset($arField);
	
} else {

	$eventM = new CEventMessage;
		$arEventMessFields = array(
			0 => array(
				'ACTIVE'     => 'Y',
				'EVENT_NAME' => GetMessage('SMARTCALL_EVENTTYPE_NAME'),
				'LID'        => 's1',
				'EMAIL_FROM' => GetMessage('EMESSAGE_EMAIL_FROM'),
				'EMAIL_TO'   => GetMessage('EMESSAGE_EMAIL_TO'),
				'SUBJECT'    => GetMessage('EMESSAGE_SUBJECT_ADMIN'),
				'BODY_TYPE'  => 'text',
				'MESSAGE'    => GetMessage('EMESSAGE_TEXT'),
			),
		);

		foreach($arEventMessFields as $arField)
		{
			$rsMess = $eventM->GetList($by = 'id', $order = 'asc', array(
				'SUBJECT' => $arField['SUBJECT'],
				'LID'     => $arField['LID']
			));
			if(!$arMess = $rsMess->Fetch())
				$eventM->Add($arField);
		}
	unset($arField);

}
?>