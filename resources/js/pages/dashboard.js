import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

import Swiper from 'swiper';

import {Navigation, Pagination} from 'swiper/modules';

export default function initSwiperHome() {
    new Swiper("#slide-home", {
        modules: [Navigation, Pagination],
        loop: true,
        slidesPerView: 1,
        spaceBetween: 30,
        autoplay: {
            delay: 250,
            disableOnInteraction: false,
        },
        // pagination: {
        //     el: ".swiper-pagination",
        //     clickable: true,
        // },
        navigation: {
            // nextEl: ".swiper-button-next",
            // prevEl: ".swiper-button-prev",
        },
    });
}
