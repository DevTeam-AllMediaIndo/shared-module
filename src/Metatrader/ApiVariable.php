<?php
namespace Allmedia\Shared\Metatrader;

class ApiVariable {

    public static array $timeframes = ['M1', 'M5', 'M15', 'M30', 'H1', 'H4'];
    public static string $operationBuy = 'buy';
    public static string $operationSell = 'sell';
    public static string $operationBuyLimit = 'buylimit';
    public static string $operationSellLimit = 'selllimit';
    public static string $operationBuyStop = 'buystop';
    public static string $operationSellStop = 'sellstop';

    public static function operations(): array {
        return [self::$operationBuy, self::$operationSell, self::$operationBuyLimit, self::$operationSellLimit, self::$operationBuyStop, self::$operationSellStop];
    }

}