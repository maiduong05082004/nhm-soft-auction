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
    case EXTENDED_LISTING_DURATION = 'extended_listing_duration';
    case COMMISSION_REDUCTION = 'commission_reduction';

    public function label(): string
    {
        return match($this) {
            self::FREE_PRODUCT_LISTING => 'Đăng sản phẩm miễn phí',
            self::FREE_AUCTION_PARTICIPATION => 'Tham gia đấu giá miễn phí',
            self::DISCOUNT_PERCENTAGE => 'Phần trăm giảm giá',
            self::MAX_PRODUCTS_PER_MONTH => 'Số sản phẩm tối đa/tháng',
            self::PRIORITY_SUPPORT => 'Hỗ trợ ưu tiên',
            self::FEATURED_LISTING => 'Sản phẩm nổi bật',
            self::EXTENDED_LISTING_DURATION => 'Thời gian đăng tin mở rộng',
            self::COMMISSION_REDUCTION => 'Giảm hoa hồng',
        };
    }

    public function type(): string
    {
        return match($this) {
            self::FREE_PRODUCT_LISTING,
            self::FREE_AUCTION_PARTICIPATION,
            self::PRIORITY_SUPPORT,
            self::FEATURED_LISTING => 'boolean',
            
            self::DISCOUNT_PERCENTAGE,
            self::COMMISSION_REDUCTION => 'percentage',
            
            self::MAX_PRODUCTS_PER_MONTH,
            self::EXTENDED_LISTING_DURATION => 'number',
        };
    }
}