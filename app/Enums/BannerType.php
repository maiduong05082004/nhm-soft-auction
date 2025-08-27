<?php

namespace App\Enums;

enum BannerType: string
{
    case PRIMARY_HOME = "Banner chính trang chủ";
    case SIDEBAR_HOME = "Banner sidebar bên trái nội dung trang chủ";
    case CONTENT_HOME = "Banner main trang chủ";
    case PRIMARY_NEWS = "Banner trang tin tức";
    case SIDEBAR_ARTICLE = "Banner bài viết";
}
