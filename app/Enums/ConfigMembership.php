<?php

namespace App\Enums;

enum ConfigMembership: string
{
    case FREE_PRODUCT_LISTING = 'free_product_listing';
    case FREE_AUCTION_PARTICIPATION = 'free_auction_participation';
    case DISCOUNT_PERCENTAGE = 'discount_percentage';
    case MAX_PRODUCTS_PER_MONTH = 'max_products_per_month';
    case PRIORITY_SUPPORT = 'priority_support';
    case FEATURED_LISTING = 'featured_listing';

    public function label(): string
    {
        return match($this) {
            self::FREE_PRODUCT_LISTING => 'Đăng sản phẩm miễn phí',
            self::FREE_AUCTION_PARTICIPATION => 'Tham gia trả giá miễn phí',
            self::DISCOUNT_PERCENTAGE => 'Phần trăm giảm giá',
            self::MAX_PRODUCTS_PER_MONTH => 'Số sản phẩm tối đa/tháng',
            self::PRIORITY_SUPPORT => 'Hỗ trợ ưu tiên',
            self::FEATURED_LISTING => 'Sản phẩm nổi bật',
        };
    }

    public function type(): string
    {
        return match($this) {
            self::FREE_PRODUCT_LISTING => 'boolean',
            self::FREE_AUCTION_PARTICIPATION => 'boolean',
            self::PRIORITY_SUPPORT => 'boolean',
            self::FEATURED_LISTING => 'boolean',

            self::DISCOUNT_PERCENTAGE => 'percentage',

            self::MAX_PRODUCTS_PER_MONTH => 'number',
        };
    }
}
