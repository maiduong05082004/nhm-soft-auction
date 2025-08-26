<?php

namespace App\Enums;

enum BannerType: string
{
    case PRIMARY_HOME = "Banner chính trang chủ";
    case SIDEBAR_HOME = "Banner sidebar bên trái nội dung trang chủ";
    case CONTENT_HOME = "Banner main trang chủ";
    case SIDEBAR_ARTICLE = "Banner trang tin tức";
    case PRIMARY_NEWS = "Banner bài viết";
}
