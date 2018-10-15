<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 14/10/2018
 * Time: 13:43
 */

namespace App\Service;

use App\Component\Csv\CsvFinderInterface;
use App\Component\Csv\CsvReaderInterface;
use App\Component\DataColumnInterface;
use App\Component\Storage\StorageInterface;
use App\Component\Validator\StringValidatorInterface;

class CsvManagerService implements FileManagementServiceInterface
{
    /**
     * @var CsvFinderInterface
     */
    private $csvFinder;

    /**
     * @var CsvReaderInterface
     */
    private $csvReader;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var string
     */
    private $defaultBaseDir;

    /**
     * @var array
     */
    private $dataColumns;

    public function __construct(array $dataColumns, $defaultBaseDir, CsvFinderInterface $csvFinder, CsvReaderInterface $csvReader, StorageInterface $storage)
    {
        $this->dataColumns = $dataColumns;
        $this->defaultBaseDir = $defaultBaseDir;
        $this->csvFinder = $csvFinder;
        $this->csvReader = $csvReader;
        $this->storage = $storage;
    }

    public function setDir(string $dir): FileManagementServiceInterface
    {
        $this->csvFinder->setCsvDir($dir);
        return $this;
    }

    public function setFile(string $file): FileManagementServiceInterface
    {
        $this->csvReader->setCsv($this->csvFinder->getFileFullPath($file));
        return $this;
    }

    /**
     *
     * @param $invalidInputCallback
     * @return int
     */
    public function process($invalidInputCallback): int
    {
        $counter = 0;
        foreach ($this->csvReader->getEntriesAsArray() as $k => $user) {
            $counter++;
            while (true) {
                $this->add($user, $invalidInputCallback);
                break;
            }
        }

        return $counter;
    }

    public function add($user, callable $invalidInputCallback = null): void
    {
        foreach ($user as $column => $value) {
            $validatorKey = array_search($column, array_column($this->dataColumns, 'name'));

            if (!isset($this->dataColumns[$validatorKey])) {
                throw new \RuntimeException("Unable to get field by column: [$validatorKey][$column]");
            }

            $validator = $this->dataColumns[$validatorKey][DataColumnInterface::VALIDATOR];
            if ($validator instanceof StringValidatorInterface) {
                while ($invalidInputCallback && !$validator->isValid($user[$column])) {
                    $user = $invalidInputCallback($column, $user);
                }
            }
        }
        $this->storage->addOrUpdate($user);
        $this->storage->save();
    }

    public function listAll(): array
    {
        return $this->csvFinder->listFiles();
    }

    public function getDefaultDir(): string
    {
        return realpath($this->defaultBaseDir);
    }

    public function getConfig(): array
    {
        return $this->dataColumns;
    }

    public function getRecords(): array
    {
        return $this->storage->getAll();
    }
}
