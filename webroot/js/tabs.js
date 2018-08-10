(jQuery)(document).ready(function() {
    (jQuery)(".tab_content").hide();
    (jQuery)(".tab_content:first").show(); 

    (jQuery)("ul.tabs li").click(function() {
        (jQuery)("ul.tabs li").removeClass("active");
        (jQuery)(this).addClass("active");
        (jQuery)(".tab_content").hide();
        var activeTab = (jQuery)(this).attr("rel"); 
        (jQuery)("#"+activeTab).fadeIn(); 
    });
});
