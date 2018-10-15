<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 14/10/2018
 * Time: 13:43
 */

namespace App\Service;

use App\Component\Csv\CsvFinderInterface;
use App\Component\Winner;

class PromotionService implements PromotionServiceInterface
{
    /**
     * @var array
     */
    private $users = [];

    /**
     * @var CsvFinderInterface
     */
    private $fileService;

    public function __construct(FileManagementServiceInterface $fileService)
    {
        $this->fileService = $fileService;
    }


    public function setUsers(array $users): PromotionServiceInterface
    {
        $this->users = $users;
        return $this;
    }

    public function getColumnsDistinctValues(string $column): array
    {
        if (!count($this->users)) {
            $this->users = $this->fileService->getRecords();
        }
        $total = count($this->users);
        if (!$total) {
            throw new \LengthException('Users database is empty, please load a CSV file first');
        }

        $usersByCountry = [];

        foreach ($this->users as $user) {
            $usersByCountry[$user[$column]][] = $user;
        }

        return array_keys($usersByCountry);
    }

    public function getWinnerByColumn(string $column, string $value): array
    {
        if (!count($this->users)) {
            $this->users = $this->fileService->getRecords();
        }
        $total = count($this->users);
        if (!$total) {
            throw new \LengthException('Users database is empty, please load a CSV file first');
        }

        $winner = new Winner();
        $winner->setDataTable($this->users);
        return $winner->getWinnerByColumn('country', $value);
    }

    public function getWinner(): array
    {
        if (!count($this->users)) {
            $this->users = $this->fileService->getRecords();
        }
        $total = count($this->users);
        if (!$total) {
            throw new \LengthException('Users database is empty, please load a CSV file first');
        }

        $winner = new Winner();
        $winner->setDataTable($this->users);
        return $winner->getWinner();
    }
}
