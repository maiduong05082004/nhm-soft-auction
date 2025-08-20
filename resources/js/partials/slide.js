import Swiper from "swiper";
import { Navigation, Scrollbar } from "swiper/modules";
import "swiper/css";
import "swiper/css/scrollbar";

document.addEventListener("DOMContentLoaded", () => {
    new Swiper(".slide-product", {
        modules: [Navigation],
        slidesPerView: 2,
        spaceBetween: 12,
        // scrollbar: {
        //     el: ".swiper-scrollbar",
        //     draggable: true,
        //     hide: false
        // },
        breakpoints: {
            768: {
                slidesPerView: 4,
                spaceBetween: 24,
            },
            1024: {
                slidesPerView: 5,
                spaceBetween: 30,
            },
        },
        navigation: {
            prevEl: ".prev-btn",
            nextEl: ".next-btn",
        },
    });

    new Swiper(".slide-banner", {
        modules: [Navigation],
        slidesPerView: 2,
        spaceBetween: 12,
        // scrollbar: {
        //     el: ".swiper-scrollbar-banner",
        //     draggable: true,
        //     hide: false,
        // },
        breakpoints: {
            768: {
                slidesPerView: 4,
                spaceBetween: 24,
            },
            1024: {
                slidesPerView: 5,
                spaceBetween: 30,
            },
        },
        navigation: {
            prevEl: ".prev-btn",
            nextEl: ".next-btn",
        },
    });
    new Swiper('.similar-articles-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 3,
            },
            1024: {
                slidesPerView: 4,
            }
        }
    });
});
