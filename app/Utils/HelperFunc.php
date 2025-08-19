<?php

namespace App\Utils;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class HelperFunc
{
    public static function getTimestampAsId(): int
    {
       // Get microtime float
       $microFloat = microtime(true);
       $microTime = Carbon::createFromTimestamp($microFloat);
       $formatted = $microTime->format('ymdHisu');
       usleep(100);
       return (int)$formatted;
    }

    public static function getListBankOptions(): array
    {
        $response = Http::get('https://api.vietqr.io/v2/banks');
        if ($response->ok()) {
            $data = new Collection($response->json()['data']);
            return $data->mapWithKeys(function ($item) {
                return [
                    $item['bin'] => $item['name'] . ' - ' . $item['shortName'],
                ];
            })->toArray();
        }
        return [];
    }

    public static  function removeVietnameseTones($str): array|string|null
    {
        $unicode = [
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
        ];
        foreach($unicode as $nonUnicode=>$uni){
            $str = preg_replace("/($uni)/i",$nonUnicode,$str);
        }
        return $str;
    }

    public static function generateURLFilePath(?string $filePath): ?string
    {
        if (!empty($filePath)){
            return route('loadfile', ['file_path' => $filePath]);
        }
        return null;
    }

    public static function generateQRCodeBanking($binBank, $bankNumber, $bankName, $amount, $addInfo = null): string
    {
        // Chuyển đổi thông tin thành URL
        $url = "https://img.vietqr.io/image/{$binBank}-{$bankNumber}-print.jpg?amount={$amount}&accountName={$bankName}";
        if ($addInfo) {
            $url .= "&addInfo=" . urlencode($addInfo);
        }
        return $url;
    }
}
