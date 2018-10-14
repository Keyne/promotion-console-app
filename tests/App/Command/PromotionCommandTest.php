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

    public function testWinnerExecute(): void
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
                        return PromotionCommand::MENU_ITEM_RANDOM_WINNER;
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

        $this->assertContains($env = 'Ops! Something when wrong: Users database is empty, please load a CSV file first', $output);
    }

    public function testWinnerByCountryExecute(): void
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
                        return PromotionCommand::MENU_ITEM_RANDOM_WINNER_BY_COUNTRY;
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

        $this->assertContains('Users database is empty, please load a CSV file first', $output);
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
            ->will($this->returnCallback(function () use (&$callsCount) {
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

        $this->assertContains('Keyne Viana Silva', $output);
        $this->assertContains('keyneviana@gmail.com', $output);
        $this->assertContains('BR', $output);
        $this->assertContains('20', $output);
        $this->assertContains('30', $output);
        $this->assertContains('2030-10-13T23:34:00Z', $output);
    }

    /*
    public function testLoadCsvExecute(): void
    {
        $application = $this->buildApplication();

        $command = $application->find('app:start');
        $commandTester = new CommandTester($command);

        $callsCount = 0;
        $questionHelper = $this->getMockBuilder(QuestionHelper::class)->getMock();
        $questionHelper
            ->method('ask')
            ->will($this->returnCallback(function ($input, $output, Question $question) use (&$callsCount) {
                $callsCount++;
                //if ($callsCount == 5) die($callsCount . ' - ' . $question->getQuestion() . ': ' . $question->getDefault());
                switch ($callsCount) {
                    case 1:
                        return PromotionCommand::MENU_ITEM_LOAD_CSV;
                    case 2:
                    case 3:
                    case 4:
                        return $question->getDefault();
                    //case preg_match('/Please retype .+\'s data, ".+" looks invalid:/', $question->getQuestion()):
                        //return 'keyneviana@gmail.com';
                    default:
                        return PromotionCommand::MENU_EXIT;
                }
            }));

        $command->getHelperSet()->set($questionHelper, 'question');

        $commandTester->execute([
            'command' => $command->getName()
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertContains('960 users processed', $output);
        $this->assertContains('SAVED', $output);
    }
    */

    function testFinish()
    {
        $storage = $this->buildStorage()->clear();
        $this->assertInstanceOf(StorageInterface::class, $storage);
    }
}
