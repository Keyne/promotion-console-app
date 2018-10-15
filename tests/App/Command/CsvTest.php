<?php

namespace Tests\Command;

use App\Component\AppConfigInterface;
use App\Component\Csv\CsvFinder;
use App\Component\Csv\CsvFinderInterface;
use App\Component\Csv\CsvReader;
use App\Component\Csv\CsvReaderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CsvTest extends KernelTestCase
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var CsvFinderInterface
     */
    private $csvFinder;

    /**
     * @var CsvReaderInterface
     */
    private $csvReader;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->config = include dirname(__FILE__) . '/../../config/config.php';
    }

    public function buildCsvFinder(): CsvFinderInterface
    {
        if ($this->csvFinder instanceof CsvFinderInterface) {
            return $this->csvFinder;
        }
        $this->csvFinder = new CsvFinder();
        $this->csvFinder->setCsvDir($this->config[AppConfigInterface::DEFAULT_BASE_DIR]);
        return $this->csvFinder;
    }

    public function buildCsvReader(): CsvReaderInterface
    {
        if ($this->csvFinder instanceof CsvReaderInterface) {
            return $this->csvReader;
        }
        $this->csvReader = new CsvReader();
        $this->csvReader->setCsv($this->buildCsvFinder()->getFileFullPath('data-sample.csv'));
        return $this->csvReader;
    }

    public function testCsvFinder(): void
    {
        $finder = $this->buildCsvFinder();
        $files = $finder->listFiles();

        $this->assertArrayHasKey(0, $files);
        $this->assertContains('data-sample.csv', $files);
    }

    public function testCsvReader(): void
    {
        $reader = $this->buildCsvReader();
        $users = $reader->getEntriesAsArray();

        $this->assertArrayHasKey(0, $users);
        $this->assertContains('59970-054', $users[0]['id']);
    }
}
