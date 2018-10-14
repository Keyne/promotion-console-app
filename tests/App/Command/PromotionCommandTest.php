<?php

namespace Tests\Command;

use App\Command\PromotionCommand;
use App\Command\Step\UserManagementStep;
use App\Component\AppConfigInterface;
use App\Component\Storage\Storage;
use App\Component\Storage\StorageInterface;
use App\Factory\CommandFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Tester\CommandTester;

class PromotionCommandTest extends KernelTestCase
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var StorageInterface
     */
    private $storage;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->config = include dirname(__FILE__) . '/../../config/config.php';
    }

    public function buildStorage(): StorageInterface
    {
        if ($this->storage instanceof StorageInterface) {
            return $this->storage;
        }
        $this->storage = $this->storage = new Storage($this->config[AppConfigInterface::DEFAULT_BASE_DIR]);
        return $this->storage;
    }

    public function buildApplication(): Application
    {
        if ($this->application instanceof Application) {
            return $this->application;
        }

        $storage = $this->buildStorage();
        $config = include dirname(__FILE__) . '/../../config/config.php';
        $commandFactory = new CommandFactory($storage, $config);
        $application = new Application(static::createKernel());

        /**
         * @var CommandFactory $commandFactory
         */
        $application->add($commandFactory->create());

        return $this->application = $application;
    }

    public function testStartExecute(): void
    {
        $application = $this->buildApplication();
        $command = $application->find('app:start');
        $commandTester = new CommandTester($command);

        $questionHelper = $this->getMockBuilder(QuestionHelper::class)->getMock();
        $questionHelper
            ->method('ask')
            ->will($this->returnCallback(function () {
                return PromotionCommand::MENU_EXIT; // exit
            }));

        /**
         * @var QuestionHelper $questionHelper
         */
        $command->getHelperSet()->set($questionHelper, 'question');

        $commandTester->execute([
            'command' => $command->getName()
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertContains('CONSOLE CSV SORTABLE', $output);
    }

    public function testWinnerEmptyExecute(): void
    {
        $this->buildStorage()->clear();
        $output = $this->getWinner();
        $this->assertContains($env = 'Ops! Something when wrong: Users database is empty, please load a CSV file first', $output);
    }

    public function getWinner($a = null)
    {
        $application = $this->buildApplication();
        $command = $application->find('app:start');
        $commandTester = new CommandTester($command);

        $callsCount = 0;
        $questionHelper = $this->getMockBuilder(QuestionHelper::class)->getMock();
        $questionHelper
            ->method('ask')
            ->will($this->returnCallback(function ($i, $o, $q) use (&$callsCount, $a) {
                $callsCount++;
                //if ($a && $callsCount === 2) die($q->getQuestion());
                switch ($callsCount) {
                    case 1:
                        return PromotionCommand::MENU_ITEM_RANDOM_WINNER;
                    case 2:
                        return true;
                    default:
                        return PromotionCommand::MENU_EXIT;
                }
            }));

        /**
         * @var QuestionHelper $questionHelper
         */
        $command->getHelperSet()->set($questionHelper, 'question');

        $commandTester->execute([
            'command' => $command->getName()
        ]);

        // the output of the command in the console
        return $commandTester->getDisplay();
    }

    public function testWinnerByCountryEmptyExecute(): void
    {
        $output = $this->getWinnerByCountry();

        $this->assertContains('Users database is empty, please load a CSV file first', $output);
    }

    public function getWinnerByCountry(string $code = null)
    {
        $application = $this->buildApplication();

        $command = $application->find('app:start');
        $commandTester = new CommandTester($command);

        $callsCount = 0;
        $questionHelper = $this->getMockBuilder(QuestionHelper::class)->getMock();
        $questionHelper
            ->method('ask')
            ->will($this->returnCallback(function ($input, $output, Question $question) use (&$callsCount, $code) {
                $callsCount++;
                $strQuestion = $question->getQuestion();
                switch ($callsCount) {
                    case 1:
                        return PromotionCommand::MENU_ITEM_RANDOM_WINNER_BY_COUNTRY;
                    case preg_match('/Select the country/', $strQuestion) === 1:
                        return $code;
                    default:
                        return PromotionCommand::MENU_EXIT;
                }
            }));

        /**
         * @var QuestionHelper $questionHelper
         */
        $command->getHelperSet()->set($questionHelper, 'question');

        $commandTester->execute([
            'command' => $command->getName()
        ]);

        // the output of the command in the console
        return $commandTester->getDisplay();
    }

    public function testUserManagementExecute(): void
    {
        $application = $this->buildApplication();

        $command = $application->find('app:start');
        $commandTester = new CommandTester($command);

        $callsCount = 0;
        $questionHelper = $this->getMockBuilder(QuestionHelper::class)->getMock();
        $questionHelper
            ->method('ask')
            ->will($this->returnCallback(function () use (&$callsCount) {
                $callsCount++;
                switch ($callsCount) {
                    case 1:
                        return PromotionCommand::MENU_USER_MANAGEMENT;
                    case 2:
                        return UserManagementStep::MENU_USER_INSERT;
                    case 3:
                        return '000-111';
                    case 4:
                        return 'Keyne Viana Silva';
                    case 5:
                        return 'incorrect@email';
                    case 6:
                        return 'keyneviana@gmail.com';
                    case 7:
                        return 'BR';
                    case 8:
                        return '20';
                    case 9:
                        return '30';
                    case 10:
                        return '2030-10-13T23:34:00Z';
                    case 11:
                        return UserManagementStep::MENU_LIST;
                    case 12:
                        return UserManagementStep::MENU_BACK;
                    case 13:
                        return false;
                    default:
                        return PromotionCommand::MENU_EXIT;
                }
            }));

        /**
         * @var QuestionHelper $questionHelper
         */
        $command->getHelperSet()->set($questionHelper, 'question');

        $commandTester->execute([
            'command' => $command->getName()
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertContains('It looks like you\'ve typed a invalid value', $output);
        $this->assertContains('SAVED', $output);
    }

    public function testListUsersExecute(): void
    {
        $application = $this->buildApplication();

        $command = $application->find('app:start');
        $commandTester = new CommandTester($command);

        $callsCount = 0;
        $questionHelper = $this->getMockBuilder(QuestionHelper::class)->getMock();
        $questionHelper
            ->method('ask')
            ->will($this->returnCallback(function ($i, $o, $q) use (&$callsCount) {
                $callsCount++;

                switch ($callsCount) {
                    case 1:
                        return PromotionCommand::MENU_USER_MANAGEMENT;
                    case 2:
                        return UserManagementStep::MENU_LIST;
                    case 3:
                        return UserManagementStep::MENU_BACK;
                    case 4:
                        return false;
                }
            }));

        /**
         * @var QuestionHelper $questionHelper
         */
        $command->getHelperSet()->set($questionHelper, 'question');

        $commandTester->execute([
            'command' => $command->getName()
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertContains('Keyne Viana Silva', $output);
        $this->assertContains('keyneviana@gmail.com', $output);
        $this->assertContains('BR', $output);
        $this->assertContains('20', $output);
        $this->assertContains('30', $output);
        $this->assertContains('2030-10-13T23:34:00Z', $output);
    }

    public function testWinnerExecute(): void
    {
        $output = $this->getWinner(true);
        $this->assertContains('The winner is', $output);
    }

    public function testWinnerByCountryExecute(): void
    {
        $output = $this->getWinnerByCountry('BR');
        $this->assertContains('The winner for BR', $output);
    }

    public function xtestWrongMenuValue()
    {
        $application = $this->buildApplication();
        $command = $application->find('app:start');
        $commandTester = new CommandTester($command);

        $callsCount = 0;
        $questionHelper = $this->getMockBuilder(QuestionHelper::class)->getMock();
        $questionHelper
            ->method('ask')
            ->will($this->returnCallback(function () use (&$callsCount) {
                return 'wrong';
            }));

        /**
         * @var QuestionHelper $questionHelper
         */
        $command->getHelperSet()->set($questionHelper, 'question');

        $commandTester->execute([
            'command' => $command->getName()
        ]);

        $output = $commandTester->getDisplay();

        $this->assertContains('Ops! There was a internal error.', $output);
    }


    public function testLoadCsvExecute(string $dir = null): void
    {
        $output = $this->loadCsvExecute();
        $this->assertContains('users processed', $output);
        $this->assertContains('SAVED', $output);
    }

    public function testLoadCsvEmptyDirExecute(): void
    {
        $output = $this->loadCsvExecute('./');
        $this->assertContains('You have provided a directory with', $output);
    }

    public function xtestLoadCsvWrongDirExecute(): void
    {
        $output = $this->loadCsvExecute('./asdf');
        $this->assertContains('You have provided a directory with', $output);
    }

    public function loadCsvExecute(string $dir = null): string
    {
        $application = $this->buildApplication();

        $command = $application->find('app:start');
        $commandTester = new CommandTester($command);

        $callsCount = 0;
        $questionHelper = $this->getMockBuilder(QuestionHelper::class)->getMock();
        $questionHelper
            ->method('ask')
            ->will($this->returnCallback(function ($input, $output, Question $question) use (&$callsCount, $dir) {
                $callsCount++;
                switch ($callsCount) {
                    case 1:
                        return PromotionCommand::MENU_ITEM_LOAD_CSV;
                    case 2:
                        return $dir ?: $question->getDefault();
                    case 3:
                        return 'data-sample.csv';
                    case preg_match('/.*?Please retype.*?/', $question->getQuestion()) === 1:
                        return 'keyneviana@gmail.com';
                    default:
                        return PromotionCommand::MENU_EXIT;
                }
            }));

        /**
         * @var QuestionHelper $questionHelper
         */
        $command->getHelperSet()->set($questionHelper, 'question');

        $commandTester->execute([
            'command' => $command->getName()
        ]);

        // the output of the command in the console
        return $commandTester->getDisplay();
    }


    function testFinish()
    {
        $storage = $this->buildStorage()->clear();
        $this->assertInstanceOf(StorageInterface::class, $storage);
    }
}
