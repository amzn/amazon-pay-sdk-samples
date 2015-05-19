$(document).ready(function () {
    $(window).scroll(function () {
        $('#floater').animate({top: $(window).scrollTop() + 50 + "px"}, {queue: false, duration: 600});
    });

    $('.float-icon').hover(function () {
        $(this).removeClass('float-normal').addClass('float-hover')
    }, function () {
        $(this).removeClass('float-hover').addClass('float-normal')
    });

    $('.float-icon').on('click', function () {
        $('.float-icon').each(function () {
            $(this).removeClass('foat-normal float-hover float-active');
        });
        $(this).removeClass('foat-normal float-hover').addClass('float-active')
        if ($(this).hasClass('float-lang-php')) {
            switchPhp();
        }
        if ($(this).hasClass('float-lang-python')) {
            switchPython();
        }
        if ($(this).hasClass('float-lang-ruby')) {
            switchRuby();
        }
    });

    $('#floater').animate({top: $(window).scrollTop() + 50 + "px"}, {queue: false, duration: 600});
    $('.float-lang-php').addClass('float-active')
    switchPhp();
});

function switchPhp() {
    $('.l-python,.l-ruby').hide();
    $('.l-php').fadeIn();
}
function switchPython() {
    $('.l-ruby,.l-php').hide();
    $('.l-python').fadeIn();
}
function switchRuby() {
    $('.l-python,.l-php').hide();
    $('.l-ruby').fadeIn();
}
function startOver() {
    amazon.Login.logout();
    document.cookie = "amazon_Login_accessToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
    window.location = 'https://amzn.github.io/login-and-pay-with-amazon-sdk-samples/';
}