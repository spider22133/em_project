
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<?
if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'])
	ShowMessage($arResult['ERROR_MESSAGE']);
?>

<?if($arResult["FORM_TYPE"] == "login"):?>



		<li><a class="signin" href="#0" data-toggle="modal" data-target="#login-modal">Вход</a> <span>/</span> <a class="signin_r" href="/login/?register=yes">Регистрация</a></li>



<?
else:
?>

<form action="<?=$arResult["AUTH_URL"]?>">
		<li><p>Здравствуйте,</p><a class="usermenu"  href="<?=$arResult["PROFILE_URL"]?>"><?=$arResult["USER_NAME"]?></a></li>
		<li><a class="logout" href="<?=$APPLICATION->GetCurPageParam("logout=yes", array(
     "login",
     "logout",
     "register",
     "forgot_password",
     "change_password"))?>" class=""><?=GetMessage("AUTH_LOGOUT_BUTTON")?></a></li>
 </form>
<?endif?>
