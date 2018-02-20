<? $disableReferers = false;
if (!isset($_GET["referer1"]) || strlen($_GET["referer1"])<=0) $_GET["referer1"] = "yandext";
if (!isset($_GET["referer1"]) || strlen($_GET["referer1"])<=0) $_GET["referer1"] = "yandext";
$strReferer1 = htmlspecialchars($_GET["referer1"]);
if (!isset($_GET["referer2"]) || strlen($_GET["referer2"]) <= 0) $_GET["referer2"] = "";
$strReferer2 = htmlspecialchars($_GET["referer2"]);
header("Content-Type: text/xml; charset=windows-1251");
echo "<"."?xml version=\"1.0\" encoding=\"windows-1251\"?".">"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="2018-02-13 13:45">
<shop>
<name>ћастерска€ Gem</name>
<company>ћастерска€ Gem</company>
<url>https://gem.style</url>
<platform>1C-Bitrix</platform>
<currencies>
<currency id="UAH" rate="1" />
</currencies>
<categories>
<category id="309">‘урнитура</category>
<category id="310" parentId="309">Ўнуры</category>
<category id="311" parentId="310">вощеный</category>
</categories>
<offers>
<offer id="22221" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_chernyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/bac/bace8b77c7bc2581f085f21b37c2df64.jpg</picture>
<name>Ўнур вощеный черный</name>
<description>Ўнур вощеный </description>
</offer>
<offer id="22222" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_temno_rozovyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/2d1/2d12e12c08461bec3382029e3615845e.jpg</picture>
<name>Ўнур вощеный, темно-розовый</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="22225" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_zheltyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/245/245665838f9ccdbd5720e02e26d17d98.jpg</picture>
<name>Ўнур вощеный, желтый</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="22227" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_zelenyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/51d/51d0abcb91759df42359de4968b40505.jpg</picture>
<name>Ўнур вощеный, зеленый</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="22228" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_fioletovyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/1f0/1f02be71fbc0542851765f2c226185ad.jpg</picture>
<name>Ўнур вощеный, фиолетовый</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="22229" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_bezhevyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/f49/f494f5fafa19124ab33eea120ea5a55c.jpg</picture>
<name>Ўнур вощеный, бежевый</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="22230" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_oranzhevyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/6ec/6ec063f0ff4e0b2a6bc8637778c5b7c9.jpg</picture>
<name>Ўнур вощеный, оранжевый</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="22231" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_rozovyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/6af/6af587aa1dd737ed656ce592a83e57fa.jpg</picture>
<name>Ўнур вощеный, розовый</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="22232" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_temno_zelenyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/127/12793fdfa79c54444d34247fa38827c1.jpg</picture>
<name>Ўнур вощеный, темно-зеленый</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="22233" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_salatovyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/063/063729186b9097fc115425c127d45a63.jpg</picture>
<name>Ўнур вощеный, салатовый</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="22234" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_svetlo_salatovyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/ae5/ae59d056f909a7d7ed0bab9574ed45a5.jpg</picture>
<name>Ўнур вощеный, светло-салатовый</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="22476" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_krasnyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/54f/54f3b3468019c6e59b05d8eb9595a904.jpg</picture>
<name>Ўнур вощеный красный</name>
<description>Ўнур вощеный </description>
</offer>
<offer id="22477" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_korichnevyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/8ff/8ff76a7dd93f3b3895ec3ba7b27bed87.jpg</picture>
<name>Ўнур вощеный, коричневый</name>
<description>Ўнур вощеный </description>
</offer>
<offer id="22478" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_siniy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/ab0/ab0c22efb4585b4624f7b74487b186a7.jpg</picture>
<name>Ўнур вощеный, синий</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="22479" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_biryuzovyy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/d8d/d8d67de93c1f22e16649b791da9a9a84.jpg</picture>
<name>Ўнур вощеный,бирюзовый</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="22480" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_temno_siniy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/167/167ef02d82d53fdac2751ee8aa5adf3d.jpg</picture>
<name>Ўнур вощеный,  темно - синий</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="23959" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_nebesno_goluboy/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/74a/74a8ee23979c3d3d15fbf7332899af82.jpg</picture>
<name>Ўнур вощеный, небесно-голубой</name>
<description>Ўнур вощеный</description>
</offer>
<offer id="25045" available="true">
<url>https://gem.style/catalog/furnitura/shnury/voshchenyy/shnur_voshchenyy_svetlo_fioletovyy_1_5mm/?r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?></url>
<price>2</price>
<currencyId>UAH</currencyId>
<categoryId>311</categoryId>
<picture>https://gem.style/upload/iblock/7ef/7ef2f520d7a92ddf76c1c45ba1307116.jpg</picture>
<name>Ўнур вощеный, светло-фиолетовый 1,5мм</name>
<description>Ўнур вощеный</description>
</offer>
</offers>
</shop>
</yml_catalog>
