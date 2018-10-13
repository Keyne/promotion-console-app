<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 12/10/2018
 * Time: 00:55
 */

namespace App\Component;

use App\Component\CsvReaderInterface;
use League\Csv\Reader;

class CsvReader implements CsvReaderInterface
{
    /**
     * @var Reader
     */
    private $csv;

    public function setCsv(string $file): CsvReaderInterface
    {
        $this->csv = Reader::createFromPath($file, 'r');
        $this->csv->setHeaderOffset(0); //set the CSV header offset
        return $this;
    }

    public function getEntriesAsArray(): array
    {
        $records = $this->csv->getRecords();
        $recordsArr = [];
        foreach ($records as $record) {
            $recordsArr[] = $record;
        }

        return $recordsArr;
    }
}
