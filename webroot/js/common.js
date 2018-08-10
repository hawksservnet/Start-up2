$(document).ready(() => {
    $(window).resize(() => {
        layout();
    }).resize();
    $('.menu-btn').on('click', event => {
        event.preventDefault();
        $('.navbox').slideToggle();
    });
    $('.navbox .sub > a').on('click', event => {
        event.preventDefault();
        $('.sub-nav').slideToggle();
    });
    $('.navbar-nav .sub > a').on('click', event => {
        event.preventDefault();
        $('.sub-nav').slideToggle();
    });
});
function layout() {

}

