<?php
namespace App\Command;

use App\Command\Exception\AlertMessageException;
use App\Command\Step\CommandStepInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class DefaultCommand
 * A simple command that displays hello world to the console.
 *
 * @package App\Command
 */
class CsvParserCommand extends Command
{
    const MENU_ITEM_LOAD_CSV = 'Load CSV file from directory';
    const MENU_ITEM_RANDOM_WINNER = 'Get promotion winner';
    const MENU_ITEM_RANDOM_WINNER_BY_COUNTRY = 'Get promotion winner by country';
    const MENU_USER_MANAGEMENT = 'User management';

    const STEP_INPUT_FILE = 'fileSelectionStep';
    const STEP_WINNER = 'winnerStep';
    const STEP_WINNER_BY_COUNTRY = 'winnerByCountryStep';
    const STEP_USER_MANAGEMENT = 'userManagementStep';

    private $menu = [
        self::MENU_ITEM_LOAD_CSV,
        self::MENU_ITEM_RANDOM_WINNER,
        self::MENU_ITEM_RANDOM_WINNER_BY_COUNTRY,
        self::MENU_USER_MANAGEMENT,
    ];

    /**
     * @var CommandStepInterface
     */
    private $fileSelectionStep;

    /**
     * @var CommandStepInterface
     */
    private $winnerStep;

    /**
     * @var CommandStepInterface
     */
    private $winnerByCountryStep;

    /**
     * @var CommandStepInterface
     */
    private $userManagementStep;

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('app:start');
    }

    public function setFileSelectionStep(CommandStepInterface $fileSelectionStep): CsvParserCommand
    {
        $this->fileSelectionStep = $fileSelectionStep;
        return $this;
    }

    public function setWinnerStep(CommandStepInterface $winnerStep): CsvParserCommand
    {
        $this->winnerStep = $winnerStep;
        return $this;
    }

    public function setWinnerByCountryStep(CommandStepInterface $winnerByCountryStep): CsvParserCommand
    {
        $this->winnerByCountryStep = $winnerByCountryStep;
        return $this;
    }

    public function setUserManagementStep(CommandStepInterface $userManagementStep): CsvParserCommand
    {
        $this->userManagementStep = $userManagementStep;
        return $this;
    }

    /**
     * Execute the command from the console.
     *
     * @param InputInterface $input the input interface
     * @param OutputInterface $output the output interface
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        do {
            $shouldRunAgain = $this->start($input, $output);
        } while($shouldRunAgain);
    }

    private function start(InputInterface $input, OutputInterface $output): bool
    {
        $question = new ChoiceQuestion('Please select an option: ', $this->menu, '0');

        $choice = $this->getHelper('question')->ask($input, $output, $question);

        try {
            switch ($choice) {
                case self::MENU_ITEM_LOAD_CSV:
                    $this->runStep(self::STEP_INPUT_FILE, $input, $output);
                    break;
                case self::MENU_ITEM_RANDOM_WINNER:
                    $this->runStep(self::STEP_WINNER, $input, $output);
                    break;
                case self::MENU_ITEM_RANDOM_WINNER_BY_COUNTRY:
                    $this->runStep(self::STEP_WINNER_BY_COUNTRY, $input, $output);
                    break;
                case self::MENU_USER_MANAGEMENT:
                    $this->runStep(self::STEP_USER_MANAGEMENT, $input, $output);
                    break;
            }
        } catch (AlertMessageException $e) {
            $output->writeln($e->getMessage());
        } catch (\Exception $e) {
            $output->writeln('Ops! Something when wrong: ' . $e->getMessage());
        }


        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Press enter if you want to go back to the menu or type "N" to exit.', true);

        return $helper->ask($input, $output, $question);
    }

    private function runStep($step, InputInterface $input, OutputInterface $output)
    {
        $this
            ->$step
            ->setInput($input)
            ->setOutput($output)
            ->setQuestionHelper($this->getHelper('question'))
            ->execute();
    }
}
