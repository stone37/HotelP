$(document).ready(function() {

    // Material select
    $('.mdb-select').materialSelect();
    $('.select-wrapper.md-form.md-outline input.select-dropdown').bind('focus blur', function () {
        $(this).closest('.select-outline').find('label').toggleClass('active');
        $(this).closest('.select-outline').find('.caret').toggleClass('active');
    });

    silverCarousel();
    smoothScroll();
    flashes($('.notify'));
    password($('.input-prefix.fa-eye'));
    newsletter($('#newsletter-form'));
    cookie($('#cookieConsent'));
    mobileNavbar();
});

const cookie = function (element) {
    element.cookieConsent({testing: true, consentStyle: 'font-weight:bold'});
}
