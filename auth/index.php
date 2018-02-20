<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$userName = CUser::GetFullName();
if (!$userName)
	$userName = CUser::GetLogin();
?>
<script>
	<?if ($userName):?>
	BX.localStorage.set("eshop_user_name", "<?=CUtil::JSEscape($userName)?>", 604800);
	<?else:?>
	BX.localStorage.remove("eshop_user_name");
	<?endif?>

	<?if (isset($_REQUEST["backurl"]) && strlen($_REQUEST["backurl"])>0 && preg_match('#^/\w#', $_REQUEST["backurl"])):?>
	document.location.href = "<?=CUtil::JSEscape($_REQUEST["backurl"])?>";
	<?endif?>
</script>

<?
$APPLICATION->SetTitle("Авторизация");
?>
    <div class="text_info text-center" style="background-color: white;">
        <img src="/include/smile_hello.png" alt="Корзина еще пуста" class="img-responsive" style="margin: 0 auto">
        <p style="font-weight: bold; font-size: 30px;">Вы уже авторизированы!</p>
        <a href="/catalog/" style="margin-top: 30px" class="btn bx_bt_button">ПЕРЕЙТИ В КАТАЛОГ</a>
    </div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>