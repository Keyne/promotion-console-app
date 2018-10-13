<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 11/10/2018
 * Time: 21:35
 */

namespace App\Component;

interface CsvFinderInterface
{
    public function setCsvDir(string $dir): CsvFinderInterface;

    public function listFiles(): array;

    public function getFileFullPath(string $file): string;
}
