<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 11/10/2018
 * Time: 22:07
 */

namespace App\Command\Step;

use App\Component\Exception\AlertMessageException;
use App\Service\FileManagementServiceInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class FileChoiceStep extends AbstractStep
{
    /**
     * @var FileManagementServiceInterface
     */
    private $fileService;

    public function __construct(FileManagementServiceInterface $fileService)
    {
        $this->fileService = $fileService;
    }

    public function execute(): void
    {
        do {
            $this->askDir();
            $this->askFile();
            break;
        } while (true);

        $this->save();
    }

    private function askDir(): void
    {
        $defaultDir = $this->fileService->getDefaultDir();
        $question = new Question("Please enter the the directory in which CSV files are stored (default: {$defaultDir}): ", $defaultDir);
        $dir = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);
        $this->getOutput()->writeln("Selected directory: {$dir}");

        $this->fileService->setDir($dir);
    }

    private function askFile(): void
    {
        $files = $this->fileService->listAll();

        if (!count($files)) {
            throw new AlertMessageException('You have provided a directory with no CSV files.');
        }

        $question = new ChoiceQuestion(
            'Please select the CSV file containing the user list: ',
            $files,
            '0'
        );

        $file = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);
        $this->fileService->setFile($file);
        $this->getOutput()->writeln("Selected file: {$file}");
    }



    private function save(): void
    {
        $this->getOutput()->writeln('Saving users...');

        $counter = $this->fileService->process(function (string $column, array $user): array {
            $question = new Question("Please retype {$user['first_name']}'s data, \"{$user[$column]}\" looks invalid': ");
            $user['email'] = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);

            return $user;
        });

        $this->getOutput()->writeln("{$counter} users processed");
        $this->getOutput()->writeln('#####################');
        $this->getOutput()->writeln('####### SAVED #######');
        $this->getOutput()->writeln('#####################');
        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('');
    }
}
