<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 12/10/2018
 * Time: 01:41
 */

namespace App\Component;

interface StorageInterface
{
    public function add(array $entry): StorageInterface;

    public function get(int $index): array;

    public function getAll(): array;

    public function delete(int $index): StorageInterface;

    public function save(): StorageInterface;

    public function count(): int;
}
