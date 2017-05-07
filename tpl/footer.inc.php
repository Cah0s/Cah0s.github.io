<?php
if (strcmp(basename($_SERVER['SCRIPT_NAME']), basename(__FILE__)) === 0){
  header("location: ../");
}
$latitude = $_SESSION['lat'];
$longitude = $_SESSION['lng'];
?>
</div><!-- WRAPPER -->

<footer>
	<font color="white">Copyright &copy 2016 - 0x{TheClone}</font>
</footer>

<script type="text/javascript" src="http://code.jquery.com/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBRYZkHMdA9XGNv_Xkc33YvTpJBPg6ry_s"></script>
<script>
function initMap() {
    var mapOptions = {
        zoom: 14,
        center: new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>), // New York
        styles: [{"elementType":"geometry","stylers":[{"hue":"#ff4400"},{"saturation":-68},{"lightness":-4},{"gamma":0.72}]},{"featureType":"road","elementType":"labels.icon"},{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"hue":"#0077ff"},{"gamma":3.1}]},{"featureType":"water","stylers":[{"hue":"#00ccff"},{"gamma":0.44},{"saturation":-33}]},{"featureType":"poi.park","stylers":[{"hue":"#44ff00"},{"saturation":-23}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"hue":"#007fff"},{"gamma":0.77},{"saturation":65},{"lightness":99}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"gamma":0.11},{"weight":5.6},{"saturation":99},{"hue":"#0091ff"},{"lightness":-86}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"lightness":-48},{"hue":"#ff5e00"},{"gamma":1.2},{"saturation":-23}]},{"featureType":"transit","elementType":"labels.text.stroke","stylers":[{"saturation":-64},{"hue":"#ff9100"},{"lightness":16},{"gamma":0.47},{"weight":2.7}]}],
        //mapTypeId: google.maps.MapTypeId.ROADMAP
        //mapTypeId: google.maps.MapTypeId.SATELLITE
    };

    var mapElement = document.getElementById('map');
    var map = new google.maps.Map(mapElement, mapOptions);

    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>),
        map: map,
        title: ''
    });
}
google.maps.event.addDomListener(window, 'load', initMap);
</script>
<!-- <script type="text/javascript" src="<?php echo HOME;?>assets/js/map.js"></script> -->

<script type="text/javascript" src="<?php echo HOME;?>assets/js/usercheck.js"></script>
<script type="text/javascript" src="<?php echo HOME;?>assets/js/jquery.nicescroll.min.js"></script>
<script type="text/javascript" src="<?php echo HOME;?>assets/js/scroll.js"></script>
<script src="<?php echo HOME;?>assets/js/inputTags.jquery.js"></script>
<script src="<?php echo HOME;?>assets/js/inputTags.func.js"></script>
<script type='text/javascript'>
$(function(){
	$(window).scroll(function(){
		$(this).scrollTop()>100?$("#BounceToTop").fadeIn():$("#BounceToTop").fadeOut();
	}),
	$("#BounceToTop").click(function(){
		$("body,html").animate({scrollTop:0},800).animate({scrollTop:25},200).animate({scrollTop:0},150).animate({scrollTop:10},100).animate({scrollTop:0},50);
	});
	var o=$("body");
	$("#GoToDown").click(function(){
		$("html, body").animate({scrollTop:o.height()},800),$("#GoToDown").fadeOut();
	}),
	$("#BounceToTop").click(function(){
		$("#GoToDown").fadeIn();
	});
});
</script>
<div id='BounceToTop'></div>

<script>
$(document).ready(function () {
  $('#close').click(function () {
  	$('#overlay').hide();
  	var HOME = "http://localhost/meineprojekte/teste/index.php";
  	var $acao	= "add";
    var $id 	= "style='display:none;'";
    $.ajax({
      type: "POST",
      url: HOME, //"session.php",
      data: { 'acao':$acao,'id':$id }
    });
    return false;
  });

	$(".search").keyup(function() {
		var searchbox = $(this).val();
		var searchtype = $("#type").val();
		var dataString = 'searchword='+ searchbox +'&type='+searchtype;
		var HOME = '<?php echo HOME; ?>';
		
		if(searchbox === '') {
			$("#display").hide();
		}
		else{
			$.ajax({
				type: "POST",
				url: HOME + "tpl/data/search.php",
				data: dataString,
				cache: false,
				success: function(html) {
					$("#display").html(html).show();
				}
			});
		} return false; 
	});

	$('html, body').click(function() {
		$("#display").hide();
	});
});
</script>
<!-- whatsapp -->
<script>
$(document).ready(function() {
var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};
 $(document).on("click", '.whatsapp', function() {
        if( isMobile.any() ) {
            var text = $(this).attr("data-text");
            var url = $(this).attr("data-link");
            var message = encodeURIComponent(text) + " - " + encodeURIComponent(url);
            var whatsapp_url = "whatsapp://send?text=" + message;
            window.location.href = whatsapp_url;
        } else {
            alert("This function only works on a mobile!!");
        }
    });
});
</script>
<!-- end whatsapp -->