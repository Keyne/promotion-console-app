# Symfony Gift Promotion Console App (Beta)
This console application let you export your user's table in CSV format and choose a random winner of a given promotion.

Under `/config/data-columns.config.php` you will be able set the columns and validators of your table (a default is already configured).
This will be used to insert new users on the JSON object, which is by default saved in `/data/database.json`. It will also validate CSV data during the parsing.

After exporting a CSV file you can put it in the default directory for loading in the console app: `/data` 

## Features included in this beta version

- Configurable export table
- Interactive menu
- Select default folder to load CSV files from
- List files in folder and select file input
- Interactve validation during CSV loading
- Load any number of CSV files
- Choose a random winner
- Choose a random winner by country code showing available codes (`TODO: Let choose column for sorting`)
- List users in JSON database
- Add new users (with input validation)
- Prevents duplicate IDs by updating entries with same ID

## Running the console
Before anything:

```
$ composer install
```

The console can be started with by running the PHP console file.

```
$ php console app:start
```

## Requirements

- php ^7.1
- composer

## Dependencies

```
    "require": {
        "symfony/console": "^4.1",
        "league/flysystem": "^1.0",
        "league/csv": "^9.1",
        "nojacko/email-validator": "~1.0"
    }
```

```
    "require-dev": {
        "php": "^7.1.13",
        "squizlabs/php_codesniffer": "^3.3",
        "phpunit/phpunit": "^6.5",
        "symfony/phpunit-bridge": "*",
        "blast-project/tests-bundle": "^0.6.4"
    }
```    

## PSR-2
The application is PSR-2 compliant and comes with an included Code sniffer
```
$ vendor/bin/phpcs ./src --ignore=./src/AppKernel.php
$ vendor/bin/phpcs ./tests --ignore=build
```

## Test coverage (PHPUnit)
There's a significant amount of tests which prevents application from breaking during changes. Altough, this project has not been developed under TDD and thus the tests does't cover 100% yet.


Open on your browser the following file to view the test coverage results: `tests/build/coverage/index.html`.
 
```
$ vendor/bin/phpunit -c tests/phpunit.xml
```

## SOLID Principles

This application has been built using SOLID principles with a dedicated domain layer which let it grows as necessary (i.e. adding a brownser/mobile front-end app or a REST API). The Data Access Layer can also be changed easily to, for example, mongodb. A dedicated component for sorting winners can be extended for better sorting algorithms. Finally, each menu item has simple inteface `CommandStepInterface` and `FormStep` for the command line input/output.


Symfony IoC has not been used altough a factory inteface is available:

```
<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 11/10/2018
 * Time: 21:14
 */

namespace App\Factory\Interfaces;

use Symfony\Component\Console\Command\Command;

interface CommandFactoryInterface
{
    public function create(): Command;
}

```

This is the basic application wiring:

```
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

```

## Sample columns configuration

```
<?php
  
use \App\Component\Validator\RegexValidator;
use \App\Component\Validator\EmailValidator;
use \App\Component\DataColumnInterface as ColumnFeature;
  
  
return $fields = [
    [
        ColumnFeature::LABEL => 'ID (Ex: "1234-567")',
        ColumnFeature::NAME => 'id',
        ColumnFeature::VALIDATOR => new RegexValidator(RegexValidator::REGEX_ID)
    ],
    [
        ColumnFeature::LABEL => 'First name',
        ColumnFeature::NAME => 'first_name',
        ColumnFeature::VALIDATOR => new RegexValidator(RegexValidator::REGEX_FIRST_NAME)
    ],
    [
        ColumnFeature::LABEL => 'Email',
        ColumnFeature::NAME => 'email',
        ColumnFeature::VALIDATOR => new EmailValidator()
    ],
    [
        ColumnFeature::LABEL => 'Country code (Ex format: Uppercase "BR")',
        ColumnFeature::NAME => 'country',
        ColumnFeature::VALIDATOR => new RegexValidator(RegexValidator::REGEX_COUNTRY_CODE)
    ],
    [
        ColumnFeature::LABEL => 'Latitude (Ex format: "13.5936457")',
        ColumnFeature::NAME => 'latitude',
        ColumnFeature::VALIDATOR => new RegexValidator(RegexValidator::REGEX_COUNTRY_COORDINATES)
    ],
    [
        ColumnFeature::LABEL => 'Longitude (Ex format: "-50.9936457")',
        ColumnFeature::NAME => 'longitude',
        ColumnFeature::VALIDATOR => new RegexValidator(RegexValidator::REGEX_COUNTRY_COORDINATES)]
    ,
    [
        ColumnFeature::LABEL => 'Date (Ex format: "2018-03-10T12:45:57Z")',
        ColumnFeature::NAME => 'Date Joined',
        ColumnFeature::VALIDATOR => new RegexValidator(RegexValidator::REGEX_COUNTRY_DATE)
    ],
];
```
