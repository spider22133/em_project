<?
    // подключим пролог
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
  
    global $USER;
 
    if ($USER -> IsAuthorized())
    {
        die('Y'); // если авторизация прошла успешно, возвращаем Y
    }
 
    // в противном случае нам нужно вернуть html с описанием ошибок
    if (isset($arResult['ERROR_MESSAGE']['MESSAGE']) && strlen($arResult['ERROR_MESSAGE']['MESSAGE']) > 0)
    {
        die($arResult['ERROR_MESSAGE']['MESSAGE']);
    }
    else
    {
        // ну а если описание ошибок отсутствует, вернем простое служебное сообщение, 
        // чтобы не держать пользователя в неведении
        die('Ошибка авторизации');
    }
?>