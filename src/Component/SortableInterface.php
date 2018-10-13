<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 13/10/2018
 * Time: 18:48
 */

namespace App\Component;


interface SortableInterface
{
    public function setDataTable(array $table): SortableInterface;

    public function getWinner(): array;

    public function getWinnerByColumn(string $column, string $value): array;
}