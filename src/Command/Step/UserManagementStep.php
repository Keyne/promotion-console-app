<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 11/10/2018
 * Time: 22:07
 */

namespace App\Command\Step;

use App\Component\Exception\AlertMessageException;
use App\Component\Storage\StorageInterface;
use App\Service\FileManagementServiceInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ChoiceQuestion;

class UserManagementStep extends AbstractStep
{
    const MENU_LIST = 'List users';
    const MENU_USER_INSERT = 'Add new user';
    const MENU_BACK = 'Back';
    //const MENU_USER_EDIT = 'Edit user by id';
    //const MENU_SEARCH_BY_EMAIL = 'Search user by email';
    //const MENU_SEARCH_BY_NAME = 'Search user by name';
    //const MENU_DELETE_BY_EMAIL = 'Delete user by email';
    //const MENU_DELETE_BY_ID = 'Delete user by id';

    private $menu = [
        self::MENU_LIST,
        self::MENU_USER_INSERT,
        self::MENU_BACK,
        //self::MENU_USER_EDIT,
        //self::MENU_SEARCH_BY_EMAIL,
        //self::MENU_SEARCH_BY_NAME,
        //self::MENU_DELETE_BY_EMAIL,
        //self::MENU_DELETE_BY_ID,
    ];

    /**
     * @var FileManagementServiceInterface
     */
    private $fileService;

    public function __construct(FileManagementServiceInterface $fileManagementService)
    {
        $this->fileService = $fileManagementService;
    }

    public function execute(): void
    {
        do {
            $question = new ChoiceQuestion('Please select an option: ', $this->menu, '0');
            $choice = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);
            $exit = false;
            try {
                switch ($choice) {
                    case self::MENU_LIST:
                        $this->runListStep();
                        break;
                    case self::MENU_USER_INSERT:
                        $this->runNewUserStep();
                        break;
                    case self::MENU_BACK:
                        $exit = true;
                        break;
                }
            } catch (AlertMessageException $e) {
                $this->getOutput()->writeln($e->getMessage());
            } catch (\Exception $e) {
                $this->getOutput()->writeln('Ops! Something when wrong: ' . $e->getMessage());
            }
        } while ($exit === false);
    }

    private function runListStep(): void
    {
        $table = new Table($this->getOutput());
        $table
            ->setHeaders(array('ID', 'FIRST NAME', 'EMAIL', 'COUNTRY', 'LATITUDE', 'LONGITUDE', 'JOINED AT'))
            ->setRows($this->fileService->getRecords())
        ;
        $table->render();
    }

    private function runNewUserStep(): void
    {
        $formStep = new FormStep($this->fileService->getConfig(), $this->fileService);
        $formStep
            ->setOutput($this->getOutput())
            ->setInput($this->getInput())
            ->setQuestionHelper($this->getQuestionHelper())
        ;
        $formStep->execute();
    }
}
