@extends('layouts.app')

@section('title', 'Danh sách đấu giá - AuctionsClone')
@section('meta_description', 'Xem danh sách các sản phẩm đấu giá mới nhất.')
@section('meta_keywords', 'đấu giá, mua bán, auctions, sản phẩm')
@section('og_title', 'Danh sách đấu giá')
@section('og_description', 'Xem danh sách các sản phẩm đấu giá mới nhất.')
@section('og_image', asset('images/auctions-og.jpg'))
@section('schema_type', 'CollectionPage')
@section('schema_name', 'Danh sách đấu giá')

@section('content')
    <div id="page-home" class="my-3">
        <section class="slider-banner overflow-hidden" id="slide-home">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="/images/banner_buyeeEnPc.png" class="w-5/6 mx-auto" alt="Slide 1">
                </div>
                <div class="swiper-slide">
                    <img src="/images/banner_buyeeEnPc.png" class="w-5/6  mx-auto" alt="Slide 2">
                </div>
                <div class="swiper-slide">
                    <img src="/images/banner_buyeeEnPc.png" class="w-5/6   mx-auto" alt="Slide 3">
                </div>
            </div>
{{-- 
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div> --}}
            {{-- <div class="swiper-pagination"></div> --}}
        </section>
    </div>
@endsection
