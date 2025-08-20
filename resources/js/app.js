import $ from "jquery";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/thumbs";
import "swiper/css/pagination";
import Swiper from "swiper";
import { Navigation, Thumbs, FreeMode, Pagination } from "swiper/modules";

window.$ = window.jQuery = $;
const APP_URL = 'http://127.0.0.1:8000';
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

    const popularProducts = new Swiper(".popularProductsSwiper", {
        modules: [Navigation, Pagination],
        spaceBetween: 16,
        slidesPerView: 1,
        navigation: {
            nextEl: ".popularProductsSwiper .swiper-button-next",
            prevEl: ".popularProductsSwiper .swiper-button-prev",
        },
        pagination: {
            el: ".popularProductsSwiper .swiper-pagination",
            clickable: true,
            dynamicBullets: true,
        },
        breakpoints: {
            480: {
                slidesPerView: 2,
                spaceBetween: 16,
            },
            640: {
                slidesPerView: 3,
                spaceBetween: 16,
            },
            768: {
                slidesPerView: 4,
                spaceBetween: 16,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 16,
            },
        },
    });
});
