<!-- SnapWidget -->
<!--<script src="https://snapwidget.com/js/snapwidget.js"></script>-->
<div style="padding: 15px;background-color: #f9f9f9;">
	<div class="h4" style="color: #000; padding: 5px 10px 25px; font-weight: 500; margin: 0px; text-align: left;">
		Посетите наш профиль в Instagram
	</div>

<!-- <iframe src="https://snapwidget.com/embed/391170" class="snapwidget-widget" allowTransparency="true" frameborder="0" scrolling="no" style="border:none; overflow:hidden; width:100%; "></iframe>-->
    <div id="instafeed">
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/instafeed.js/1.4.1/instafeed.min.js"></script>
    <script type="text/javascript">
        var userFeed = new Instafeed({
            get: 'user',
            userId: '4512671295',
            clientId: '04d098ab01274a57b086967750fb4fa9',
            accessToken: '4512671295.04d098a.8e08b136624c4c72bfb456c76e3395cf',
            resolution: 'standard_resolution',
            template:
            '<a href="{{link}}" target="_blank" id="{{id}}">' +
            '<div class="img-featured-container">' +
            '<div class="img-backdrop"></div>' +
            '<div class="description-container">' +
            '<span class="likes"><i class="icon ion-heart"></i> {{likes}}</span>' +
            '<span class="comments"><i class="icon ion-chatbubble"></i> {{comments}}</span>' +
            '</div>' +
            '<img src="{{image}}" class="img-responsive">' +
            '</div>' +
            '</a>',
            sortBy: 'most-recent',
            limit: 6,
            links: false
        });
        userFeed.run();
    </script>
</div>
 <br>