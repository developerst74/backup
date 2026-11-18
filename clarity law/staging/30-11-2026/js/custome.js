
// window.addEventListener("load", function () {
//   function setup() {
//     document.querySelectorAll(".autoplay-slider").forEach(function (box) {
//       var el = box.querySelector(".swiper") || box.querySelector(".swiper-container");
//       if (!el || !el.swiper) return;

//       var sw = el.swiper;
//       var reverse = box.classList.contains("reverse");

//       sw.params.loop = true;
//       sw.params.speed = 1000;              // jetlu vadhu, etlu dhimu
//       sw.params.allowTouchMove = false;
//       sw.params.autoplay = {
//         delay: 0,
//         disableOnInteraction: false,
//         pauseOnMouseEnter: false,
//         reverseDirection: reverse
//       };

//       sw.autoplay.stop();
//       sw.autoplay.start();
//     });
//   }
//   setTimeout(setup, 1500);
//   setTimeout(setup, 3500); // backup, jo pehli vaar na chale to
// });


jQuery(document).ready(function ($) {
 
    // Hide the guide content by default

    $('.content-box-parts').hide();
 
    // Hide the Close button by default

    $('.close-the-guide').hide();
 
    // Read the full guide button

    $('.read-full-guide').on('click', function (e) {

        e.preventDefault();
 
        $('.content-box-parts').slideDown(400);
 
        $('.read-full-guide').hide();

        $('.close-the-guide').show();

    });
 
    // Close the guide button

    $('.close-the-guide').on('click', function (e) {

        e.preventDefault();
 
        $('.content-box-parts').slideUp(400);
 
        $('.close-the-guide').hide();

        $('.read-full-guide').show();

    });
 
});
 