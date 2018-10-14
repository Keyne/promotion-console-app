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
use App\Service\CsvManagerService;
use App\Service\FileManagementServiceInterface;
use App\Service\PromotionService;
use App\Service\PromotionServiceInterface;
use Symfony\Component\Console\Command\Command;

class CommandFactory implements CommandFactoryInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var FileManagementServiceInterface
     */
    protected $fileService;

    /**
     * @var PromotionServiceInterface
     */
    protected $promotionService;

    /**
     * @var array
     */
    protected $config;

    public function __construct(StorageInterface $storage = null, array $config = null)
    {
        $this->storage = $storage;
        $this->config = $config ?: include dirname(__FILE__) . '/../../config/config.php';
        $this->createStorage();
        $this->createFileManagerService();
        $this->createPromotionService();
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

    public function createFileManagerService(): FileManagementServiceInterface
    {
        $dataColumns = include(dirname(__FILE__) . "/../../config/data-columns.config.php");

        return $this->fileService = new CsvManagerService($dataColumns, $this->config[Config::DEFAULT_BASE_DIR], $this->createCsvFinder(), $this->createCsvReader(), $this->storage);
    }

    public function createPromotionService(): PromotionServiceInterface
    {
        return $this->promotionService = new PromotionService($this->fileService);
    }

    protected function createFileChoiceStep(): CommandStepInterface
    {
        $step = new FileChoiceStep($this->fileService);
        return $step;
    }

    protected function createWinnerStep(): CommandStepInterface
    {
        $step = new WinnerStep($this->fileService);
        return $step;
    }

    protected function createWinnerByCountryStep(): CommandStepInterface
    {
        $step = new WinnerByCountryStep($this->fileService, $this->promotionService);
        return $step;
    }

    protected function createUserManagementStep(): CommandStepInterface
    {
        $step = new UserManagementStep($this->fileService);
        return $step;
    }

    protected function createCsvFinder(): CsvFinderInterface
    {
        $config = include dirname(__FILE__) . '/../../config/config.php';
        $finder = new CsvFinder($config);
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
