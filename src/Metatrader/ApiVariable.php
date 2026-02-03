<?php
namespace Allmedia\Shared\Metatrader;

class ApiVariable {

    public static array $timeframes = ['M1', 'M5', 'M15', 'M30', 'H1', 'H4'];
    public static string $operationBuy = 'buy';
    public static string $operationSell = 'sell';
    public static string $operationBuyLimit = 'buy_limit';
    public static string $operationSellLimit = 'sell_limit';
    public static string $operationBuyStop = 'buy_stop';
    public static string $operationSellStop = 'sell_stop';

    public static function operations(): array {
        return [self::$operationBuy, self::$operationSell, self::$operationBuyLimit, self::$operationSellLimit, self::$operationBuyStop, self::$operationSellStop];
    }

}