<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Контакты интернет-магазина ➦ Gem.style. У нас представлен большой выбор бижутерии, \$ лучшие цены, ✈ быстрая доставка, ☑ гарантия!");
$APPLICATION->SetPageProperty("title", "Контакты - магазин Gem.style | Киев, Украина");
$APPLICATION->SetTitle("Наши контакты");
?><div class="row">
	<div class="col-xs-12">
		<div class="row" style="margin-bottom: 15px">
			<div class="col-lg-6 cont_col" style="margin-bottom: 10px;">
				<div class="cont_head">
					<h3 style="margin-bottom: 15px;"><i class="fa fa-shopping-bag" style="margin-right: 8px;"></i>Шоу-рум </h3>
				</div>
				<p>
					 м. Шулявская<br>
					 ул. Вадима Гетьмана,2<br>
					 тел. 093-720-70-20<img src="/upload/medialibrary/7f8/7f8aa7b90e1650a2ba96f0c26efd2f71.png"><img alt="intro1.png" src="/upload/medialibrary/2a4/2a47432cf8f85bb1e35566b84a016a8c.png" title="intro1.png"><br>
					 тел. 097-720-70-20<br>
					 пн - вс с 10.00 до 20.00<br>
					 E-mail:&nbsp;<a href="mailto:zakaz@gem.style">zakaz@gem.style</a><br>
				</p>
			</div>
			<div class="col-lg-6 cont_col" style="margin-bottom: 10px;">
				<div class="cont_head">
					<h3 style="margin-bottom: 15px;"><i class="fa fa-star" style="margin-right: 8px;"></i>Офис</h3>
				</div>
				<p>
					 м. Берестейская, ул. Артиллерийский переулок,7б<br>
					 пн - пт с 9.00 до 17.00<br>
					 тел. 093-370-75-85<img src="/upload/medialibrary/7f8/7f8aa7b90e1650a2ba96f0c26efd2f71.png"><br>
					 E-mail:<b>&nbsp;</b><a href="mailto:info@gem.style">info@gem.style</a><br>
				</p>
			</div>
		</div>
		<div class="row" style="margin-bottom: 15px">
			<div class="col-xs-12">
				<div class="cont_head">
					<h3 style="margin-bottom: 15px; padding-left: 15px"><i class="fa fa-shopping-bag" style="margin-right: 8px;"></i>Мастерские украшений</h3>
				</div>
			</div>
			<div class="col-lg-4 cont_col" style="margin-bottom: 10px;">
				<p>
					 м. Шулявская, ул. Вадима Гетьмана,2<br>
					 тел. 093-280-70-50<img src="/upload/medialibrary/7f8/7f8aa7b90e1650a2ba96f0c26efd2f71.png"><br>
					 пн - вс с 10.00 до 20.00,<br>
					<br>
				</p>
			</div>
			<div class="col-lg-4 cont_col" style="margin-bottom: 10px;">
				<p>
					 м. Вокзальная, Западный<br>
					 подземный переход<br>
					 тел. 093-280-70-10<img src="/upload/medialibrary/7f8/7f8aa7b90e1650a2ba96f0c26efd2f71.png"><br>
					 пн - вс с 10.00 до 20.00,<br>
					<br>
				</p>
			</div>
			<div class="col-lg-4 cont_col" style="margin-bottom: 10px;">
				<p>
					 м. Академгородок, аллея магазинов к ТЦ Novus,<br>
					 тел. 093-280-70-20<img src="/upload/medialibrary/7f8/7f8aa7b90e1650a2ba96f0c26efd2f71.png"><br>
					 пн - вс с 10.00 до 20.00,<br>
					<br>
				</p>
			</div>
		</div>
		<div class="row" style="margin-bottom: 15px">
			<div class="col-lg-4 cont_col" style="margin-bottom: 10px;">
				<p>
					 м. Выставочный центр, аллея магазинов,<br>
					 тел. 093-280-70-30<img src="/upload/medialibrary/7f8/7f8aa7b90e1650a2ba96f0c26efd2f71.png"><br>
					 пн - вс с 10.00 до 20.00,<br>
					<br>
				</p>
			</div>
			<div class="col-lg-4 cont_col" style="margin-bottom: 10px;">
				<p>
					 м. Контрактовая площадь, переход к Валам,<br>
					 тел. 098-360-30-30<img src="/upload/medialibrary/7f8/7f8aa7b90e1650a2ba96f0c26efd2f71.png"><br>
					 пн - вс с 10.00 до 20.00,<br>
					<br>
				</p>
				<p>
 <br>
				</p>
			</div>
		</div>
 <b><span style="color: #ed008c;">&nbsp; &nbsp;Внимание! </span></b>В период с&nbsp;3.01.18г.&nbsp;до&nbsp;14.01.18г.&nbsp; все магазины работают с&nbsp;<b>10:00&nbsp;</b>до&nbsp;<b>20:00</b><br>
 <br>
		<div>
			<div id="map">
			</div>
		</div>
		<div class="cont_head">
			<h3>Задать вопрос</h3>
		</div>
		 <?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	"eshop",
	Array(
		"COMPONENT_TEMPLATE" => "eshop",
		"EMAIL_TO" => "zakaz@gem.style",
		"EVENT_MESSAGE_ID" => array(),
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"REQUIRED_FIELDS" => array(),
		"USE_CAPTCHA" => "Y"
	)
);?>
	</div>
</div>
 <script>
        var map;
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 11,
                center: new google.maps.LatLng(50.4416168, 30.4455562),
                mapTypeId: 'roadmap',
//                disableDefaultUI: true,
                styles: [{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#d3d3d3"}]},{"featureType":"transit","stylers":[{"color":"#808080"},{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#b3b3b3"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"weight":1.8}]},{"featureType":"road.local","elementType":"geometry.stroke","stylers":[{"color":"#d7d7d7"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#ebebeb"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"color":"#a7a7a7"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#efefef"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#696969"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"color":"#737373"}]},{"featureType":"poi","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"color":"#d6d6d6"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"color":"#dadada"}]}]

            });

            var iconBase = 'https://gem.style/about/contacts/';
            var icons = {
                info: {
                    icon: iconBase + 'Shop.svg'
                },
                main: {
                    icon: iconBase + 'Work.svg'
                },
                showroom: {
                    icon: iconBase + 'Shopping_Bag.svg'
                }
            };




            var features = [
                {
                    position: new google.maps.LatLng(50.4631981, 30.3551485),
                    type: 'info',
                    content: 'м. Академгородок, аллея магазинов к ТЦ Novus'
                }, {
                    position: new google.maps.LatLng(50.3812089, 30.4766761),
                    type: 'info',
                    content: 'м. Выставочный центр, аллея магазинов'
                }, {
                    position: new google.maps.LatLng(50.4660956, 30.5128101),
                    type: 'info',
                    content: 'м. Контрактовая площадь, переход к Валам'
                }, {
                    position: new google.maps.LatLng(50.4523797, 30.443445),
                    type: 'showroom',
                    content: 'м. Шулявка ул. Вадима Гетьмана,2'
                }, {
                    position: new google.maps.LatLng(50.4598594, 30.4220681),
                    type: 'main',
                    content: 'м. Берестейская, ул. Артиллерийский переулок,7б'
                }
            ];

            // Create markers.
            features.forEach(function (feature) {
                var infowindow = new google.maps.InfoWindow({
                    content: feature.content
                });

                var marker = new google.maps.Marker({
                    position: feature.position,
                    icon: icons[feature.type].icon,
                    map: map
                });
                marker.addListener('click', function() {
                    infowindow.open(map, marker);
                });
            });

        }
    </script> <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCV9W11CyHN63S1jTZfeYXA3QpHuDSolMs&callback=initMap">
    </script> <br>
 <br><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php") ?>