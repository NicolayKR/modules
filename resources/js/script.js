window.addEventListener('DOMContentLoaded', () => {
    $('.nav-link').click(function () {
        $('.nav-link').not(this).removeClass('active');
        $(this).addClass('active');
    });
    $(window).click(function(event){
        if($(event.target).hasClass('navbar-toggler-icon') == false ){
            if($(event.target).hasClass('navbar-mobile') == false && $('body').hasClass('navbar-open')){	
                $('body').removeClass('navbar-open');
             }
        }
	});
    $(window).resize(function(){
        let header = $('#main-header').height();
        if($(window).width()< 899){
            $('#sidebarMenu').css({
                'padding-top': header
            });
        }
    });
    $("div.pswp").remove();
    // $(window).click(function(event){
    //     $('#example-datepicker-end__dialog_').attr('x-placement', 'bottom-start');
    // });
});