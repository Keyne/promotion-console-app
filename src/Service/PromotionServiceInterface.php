<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 14/10/2018
 * Time: 13:40
 */

namespace App\Service;

interface PromotionServiceInterface
{
    public function setUsers(array $users): PromotionServiceInterface;

    public function getWinnerByColumn(string $column, string $value): array;

    public function getWinner(): array;

    public function getColumnsDistinctValues(string $column): array;
}
