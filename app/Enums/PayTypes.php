<?php
namespace App\Enums;

enum PayTypes: string {
    case QRCODE = 'qrcode';
    case POINTS = 'points';
}