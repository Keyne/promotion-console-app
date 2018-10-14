<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 14/10/2018
 * Time: 13:40
 */

namespace App\Service;

interface FileManagementServiceInterface
{
    public function setDir(string $dir): FileManagementServiceInterface;

    public function setFile(string $file): FileManagementServiceInterface;

    /**
     * @param $invalidInputCallback
     * @return int
     */
    public function process($invalidInputCallback): int;

    public function add($user, callable $invalidInputCallback = null): void;

    public function listAll(): array;

    public function getRecords(): array;

    public function getDefaultDir(): string;

    public function getConfig(): array;
}