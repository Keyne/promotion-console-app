<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 13/10/2018
 * Time: 18:50
 */

namespace App\Component;

use App\Component\Exception\AlertMessageException;

class Winner implements SortableInterface
{
    /**
     * @var array
     */
    private $dataTable;

    public function setDataTable(array $table): SortableInterface
    {
        var_dump($table); exit;
        $this->dataTable = $table;
        return $this;
    }

    public function getWinner(): array
    {
        return $this->getRandom($this->dataTable);
    }

    public function getWinnerByColumn(string $column, string $value): array
    {
        $table = $this->getFilteredTable($this->dataTable, $column, $value);
        return $this->getRandom($table);
    }

    private function getRandom(array $table): array
    {
        $lastIndex = count($table) -1;
        if (count($table) === 0) {
            throw new AlertMessageException('No users found');
        }

        $winner = rand(0, $lastIndex);
        return $table ? $table[$winner] : $this->dataTable[$winner];
    }

    private function getFilteredTable(array $table, string $column, string $value): array
    {
        $filteredTable = [];
        foreach ($table as $row) {
            if ($row[$column] === $value) {
                $filteredTable[] = $row;
            }
        }
        return $filteredTable;
    }
}
