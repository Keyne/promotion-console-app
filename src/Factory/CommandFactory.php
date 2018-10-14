<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 11/10/2018
 * Time: 21:13
 */

namespace App\Factory;

use App\Command\PromotionCommand;
use App\Command\Step\FileChoiceStep;
use App\Command\Step\CommandStepInterface;
use App\Command\Step\UserManagementStep;
use App\Command\Step\WinnerByCountryStep;
use App\Command\Step\WinnerStep;
use App\Component\AppConfigInterface as Config;
use App\Component\Csv\CsvFinder;
use App\Component\Csv\CsvReader;
use App\Component\Csv\CsvFinderInterface;
use App\Component\Csv\CsvReaderInterface;
use App\Component\Storage\StorageInterface;
use App\Component\Storage\Storage;
use App\Factory\Interfaces\CommandFactoryInterface;
use Symfony\Component\Console\Command\Command;

class CommandFactory implements CommandFactoryInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var array
     */
    protected $config;

    public function __construct(StorageInterface $storage = null, array $config = null)
    {
        $this->storage = $storage;
        $this->config = $config ?: include dirname(__FILE__) . '/../../config/config.php';
    }

    public function create(): Command
    {
        $command = new PromotionCommand();
        $command
            ->setFileSelectionStep($this->createFileChoiceStep())
            ->setWinnerStep($this->createWinnerStep())
            ->setWinnerByCountryStep($this->createWinnerByCountryStep())
            ->setUserManagementStep($this->createUserManagementStep())
        ;
        return $command;
    }

    protected function createFileChoiceStep(): CommandStepInterface
    {
        $dataColumns = include(dirname(__FILE__) . "/../../config/data-columns.config.php");
        ;
        $step = new FileChoiceStep($dataColumns, $this->config[Config::DEFAULT_BASE_DIR], $this->createCsvFinder(), $this->createCsvReader(), $this->createStorage());
        return $step;
    }

    protected function createWinnerStep(): CommandStepInterface
    {
        $step = new WinnerStep($this->createStorage());
        return $step;
    }

    protected function createWinnerByCountryStep(): CommandStepInterface
    {
        $step = new WinnerByCountryStep($this->createStorage());
        return $step;
    }

    protected function createUserManagementStep(): CommandStepInterface
    {
        $dataColumns = include(dirname(__FILE__) . "/../../config/data-columns.config.php");
        ;
        $step = new UserManagementStep($this->createStorage(), $dataColumns);
        return $step;
    }

    protected function createCsvFinder(): CsvFinderInterface
    {
        $finder = new CsvFinder();
        return $finder;
    }

    protected function createCsvReader(): CsvReaderInterface
    {
        $reader = new CsvReader();
        return $reader;
    }

    protected function createStorage(): StorageInterface
    {
        if ($this->storage instanceof StorageInterface) {
            return $this->storage;
        }
        $this->storage = new Storage();
        return $this->storage;
    }
}
