<?php
namespace App\Shared\Contracts;

interface DatabaseInterface {

    public static function connect();

    public static function close();

    public static function getTableInfo(string $table): array;

    public static function getBindTypes($value): string;

    public static function insert(string $table, array $data);
    
    public static function update(string $table, array $data, array $where): bool;
    
    public static function delete(string $table, array $where): bool;
    
}