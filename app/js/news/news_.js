let currentSlide = 0;
const $slides = $('.banner-carousel-item');
const $indicators = $('.banner-pagination');

function updateCarousel() {
    const $carouselContainer = $('.banner-container');
    $carouselContainer.css('transform', `translateX(-${currentSlide * 100}%)`);

    // Update active indicator
    $indicators.each(function (index) {
        if (index === currentSlide) {
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }
    });
}

function moveSlide(direction) {
    currentSlide += direction;
    if (currentSlide < 0) {
        currentSlide = $slides.length - 1;
    } else if (currentSlide >= $slides.length) {
        currentSlide = 0;
    }
    updateCarousel();
}

function goToSlide(index) {
    currentSlide = index;
    updateCarousel();
}

// Example for navigation using jQuery
$indicators.on('click', function () {
    const index = $(this).index();
    goToSlide(index);
});

// Change slide every 3 seconds
setInterval(function() {
    moveSlide(1); // Move to the next slide
}, 3000);



$(document).ready(function () {

    // var is_owlCarousel = $('.game-section').length;

    // if(is_owlCarousel > 0){

    //     $(".custom-carousel").owlCarousel({
    //         autoWidth: true,
    //         loop: false,
    //         nav: false, 
    //         dots: true
    //     });

    //     $(".custom-carousel .item").first().addClass("active");

    //     $(".custom-carousel .item").click(function () {
    //         $(".custom-carousel .item").not($(this)).removeClass("active");
    //         $(this).toggleClass("active");
    //     });

    // }



});
