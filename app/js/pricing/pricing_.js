$(document).ready(function() {
    $('.nav-link').on('click', function() {
        $('.nav-link').removeClass('active');
        $('.tab-pane').removeClass('show').hide();

        $(this).addClass('active');
        const target = $(this).data('target');
        $(target).addClass('show').fadeIn();
    });
});
