<?php
namespace App\Enums;

enum BannerType:int
{
    case PRIMARY_HOME = 1;
    case SIDEBAR_HOME = 2;
    case CONTENT_HOME = 3;
    case SIDEBAR_ARTICLE = 4;

    public function label () : string 
    {
        return match($this) {
            self::PRIMARY_HOME => "Banner chính ở trang chủ",
            self::SIDEBAR_HOME => "Banner sidebar bên trái nội dung trang chủ",
            self::CONTENT_HOME => "Banner nội dung chính trang chủ",
            self::SIDEBAR_ARTICLE => "Banner siderbar bên phải nội dung trang chủ",
        };
    }
}