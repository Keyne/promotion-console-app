<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 11/10/2018
 * Time: 22:07
 */

namespace App\Command\Step;

use App\Command\Exception\AlertMessageException;
use App\Component\Storage\StorageInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class UserManagementStep extends AbstractStep
{
    const MENU_LIST = 'List users';
    const MENU_USER_INSERT = 'Add new user';
    //const MENU_USER_EDIT = 'Edit user by id';
    //const MENU_SEARCH_BY_EMAIL = 'Search user by email';
    //const MENU_SEARCH_BY_NAME = 'Search user by name';
    //const MENU_DELETE_BY_EMAIL = 'Delete user by email';
    //const MENU_DELETE_BY_ID = 'Delete user by id';

    private $menu = [
        self::MENU_LIST,
        self::MENU_USER_INSERT,
        //self::MENU_USER_EDIT,
        //self::MENU_SEARCH_BY_EMAIL,
        //self::MENU_SEARCH_BY_NAME,
        //self::MENU_DELETE_BY_EMAIL,
        //self::MENU_DELETE_BY_ID,
    ];

    /**
     * @var array
     */
    private $formFields;

    /**
     * @var StorageInterface
     */
    private $storage;

    public function __construct(StorageInterface $storage, array $formFields)
    {
        $this->storage = $storage;
        $this->formFields = $formFields;
    }

    public function execute(): void
    {
        do {
            $question = new ChoiceQuestion('Please select an option: ', $this->menu, '0');
            $choice = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);

            try {
                switch ($choice) {
                    case self::MENU_LIST:
                        $this->runListStep();
                        break;
                    case self::MENU_USER_INSERT:
                        $this->runNewUserStep();
                        break;
                }
            } catch (AlertMessageException $e) {
                $this->getOutput()->writeln($e->getMessage());
            } catch (\Exception $e) {
                $this->getOutput()->writeln('Ops! Something when wrong: ' . $e->getMessage());
            }

            $question = new ConfirmationQuestion('Go back to user\'s management? Type N for no: ', true);
            $exit = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);
            if (!$exit) {
                break;
            }
        } while (true);
    }

    private function runListStep(): void
    {
        $table = new Table($this->getOutput());
        $table
            ->setHeaders(array('ID', 'FIRST NAME', 'EMAIL', 'COUNTRY', 'LATITUDE', 'LONGITUDE', 'JOINED AT'))
            ->setRows($this->storage->getAll())
        ;
        $table->render();
    }

    private function runNewUserStep(): void
    {
        $formStep = new FormStep($this->formFields, $this->storage);
        $formStep
            ->setOutput($this->getOutput())
            ->setInput($this->getInput())
            ->setQuestionHelper($this->getQuestionHelper())
        ;
        $formStep->execute();
    }
}
