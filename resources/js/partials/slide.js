import Swiper from "swiper";
import { Scrollbar } from "swiper/modules";
import "swiper/css";
import "swiper/css/scrollbar";

document.addEventListener("DOMContentLoaded", () => {
    new Swiper(".slide-product", {
        modules: [Scrollbar],
        slidesPerView: 2,
        spaceBetween: 12,
        scrollbar: {
            el: ".swiper-scrollbar",
            draggable: true,
            hide: false
        },
        breakpoints: {
            768: {
                slidesPerView:4,
                spaceBetween: 24
            },
            1024: {
                slidesPerView: 5,
                spaceBetween: 30
            }
        }
    });

    new Swiper(".slide-banner", {
        modules: [Scrollbar],
        slidesPerView: 2,
        spaceBetween: 12,
        scrollbar: {
            el: ".swiper-scrollbar-banner",
            draggable: true,
            hide: false
        },
        breakpoints: {
            768: {
                slidesPerView:4,
                spaceBetween: 24
            },
            1024: {
                slidesPerView: 5,
                spaceBetween: 30
            }
        }
    });
});
