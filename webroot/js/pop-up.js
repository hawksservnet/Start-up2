$(document).ready(function(){
    var popup = '';
    $('.pg-top').click(function () {
		$("html, body").animate({
			scrollTop: 10
		}, 600);
		return false;
	});

	$(".btn-open-popup").click(function(even) {
		even.preventDefault();
		popup = $(this).attr('data-id');
		loadPopup(); // function show popup
	});

	$(".btn-close, .close-popup").click(function(){
		disablePopup();
	});

	$(this).keydown(function(event) {
		if (event.which == 27) { // 27 is 'Ecs' in the keyboard
			disablePopup();  // function close pop up
		}
	});

    $(".background-popup").click(function() {
		disablePopup();  // function close pop up
	});

    function loadPopup() {
        $('#'+popup).show();
        $(".popup-cont").animate({scrollTop: 10}, 500); //scroll popup to Top
        $(".to-popup").fadeIn(200); // fadein popup div
        $(".background-popup").css("opacity", "0.8"); // css opacity, supports IE7, IE8
        $(".background-popup").fadeIn(200);
    }

    function disablePopup() {
        $('#'+popup).hide();
        $(".to-popup").fadeOut(300);
        $(".background-popup").fadeOut(300);
        $('body,html').css("overflow","auto");//enable scroll bar
    }
});
