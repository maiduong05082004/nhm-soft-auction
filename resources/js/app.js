import $ from "jquery";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/thumbs";
import Swiper from "swiper";
import { Navigation, Thumbs, FreeMode } from "swiper/modules";

window.$ = window.jQuery = $;

document.addEventListener("DOMContentLoaded", () => {
    const thumbs = new Swiper(".thumbsSwiper", {
        modules: [FreeMode],
        spaceBetween: 8,
        freeMode: true,
        watchSlidesProgress: true,
        breakpoints: {
            0: { slidesPerView: 3 },
            480: { slidesPerView: 4 },
            640: { slidesPerView: 6 },
        },
    });

    const main = new Swiper(".mainSwiper", {
        modules: [Navigation, Thumbs],
        spaceBetween: 10,
        loop: document.querySelectorAll(".mainSwiper .swiper-slide").length > 1,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        thumbs: { swiper: thumbs },
    });
});
