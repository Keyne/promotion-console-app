<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 11/10/2018
 * Time: 21:35
 */

namespace App\Component;


interface CsvReaderInterface
{
    public function setCsv(string $file): CsvReaderInterface;

    public function getEntriesAsArray(): array;
}