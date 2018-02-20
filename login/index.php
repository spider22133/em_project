<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if (isset($_REQUEST["backurl"]) && strlen($_REQUEST["backurl"])>0) 
	LocalRedirect($backurl);

$APPLICATION->SetTitle("Вход на сайт");
?>
    <div class="text_info text-center" style="background-color: white;">
        <img src="/include/smile_hello.png" alt="Корзина еще пуста" class="img-responsive" style="margin: 0 auto">
        <p style="font-weight: bold; font-size: 30px;">Приветствуем. Желаем хорошего шоппинга!</p>
        <a href="/catalog/" style="margin-top: 30px" class="btn bx_bt_button">ПЕРЕЙТИ В КАТАЛОГ</a>
    </div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>