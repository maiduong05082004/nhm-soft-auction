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
    <h1 class="text-3xl font-bold mb-4">Danh sách đấu giá</h1>
    {{-- Nội dung trang --}}
@endsection
