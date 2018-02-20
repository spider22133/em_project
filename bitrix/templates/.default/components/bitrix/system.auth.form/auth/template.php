
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<?
if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'])
	ShowMessage($arResult['ERROR_MESSAGE']);
?>

<?if($arResult["FORM_TYPE"] == "login"):?>

<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
		<div class="loginmodal-container">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3>Вход в аккаунт</h3><br><br>


<form class="gem" id="login-form" name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
<?if($arResult["BACKURL"] <> ''):?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?endif?>
<?foreach ($arResult["POST"] as $key => $value):?>
	<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
<?endforeach?>
	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="AUTH" />
	
	<input value="<?=$arResult["USER_LOGIN"]?>" type="text" name="USER_LOGIN" placeholder="<?=GetMessage("AUTH_LOGIN")?>">
	
	<div class="form-group">
			<input type="password" name="USER_PASSWORD"  placeholder="<?=GetMessage("AUTH_PASSWORD")?>">
			<a href="#0" class="hide-password">Показать</a>
	</div>
	
<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
	<div class="checkbox">
		<label for="USER_REMEMBER_frm" title="<?=GetMessage("AUTH_REMEMBER_ME")?>">
		<input type="checkbox" id="USER_REMEMBER_frm" name="USER_REMEMBER" value="Y"><?echo GetMessage("AUTH_REMEMBER_SHORT")?></label>
	</div>	
<?endif?>
			
			
<?if ($arResult["CAPTCHA_CODE"]):?>
<?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:<br />
	<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
	<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /><br /><br />
	<input type="text" name="captcha_word" maxlength="50" value="" />	
<?endif?>
    
	<input type="submit" name="login" class="login loginmodal-submit" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>">


	<?if($arResult["AUTH_SERVICES"]):?>

				<div class="bx-auth-lbl"><?=GetMessage("socserv_as_user_form")?></div>
				<?
				$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "icons",
					array(
						"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
						"SUFFIX"=>"form",
					),
					$component,
					array("HIDE_ICONS"=>"Y")
				);
				?>

	<?endif?>

</form>

<div class="login-help">
	<?if($arResult["NEW_USER_REGISTRATION"] == "Y"):?>
	<noindex><a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_REGISTER")?></a></noindex>
	<?endif?> - <noindex><a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a></noindex>
</div>
				</div>
			</div>
		  </div>

	<?if($arResult["AUTH_SERVICES"]):?>
		<?
		$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
			array(
				"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
				"AUTH_URL"=>$arResult["AUTH_URL"],
				"POST"=>$arResult["POST"],
				"POPUP"=>"Y",
				"SUFFIX"=>"form",
			),
			$component,
			array("HIDE_ICONS"=>"Y")
		);
		?>
	<?endif?>


<?endif?>
