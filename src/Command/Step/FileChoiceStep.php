<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 11/10/2018
 * Time: 22:07
 */

namespace App\Command\Step;

use App\Component\EmailValidator;
use App\Component\CsvFinderInterface;
use App\Component\CsvReaderInterface;
use App\Component\StorageInterface;
use App\Component\StringValidatorInterface;
use EmailValidator\Validator;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class FileChoiceStep extends AbstractStep
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
     * @var StringValidatorInterface
     */
    private $emailValidator;

    public function __construct(CsvFinderInterface $csvFinder, CsvReaderInterface $csvReader, StorageInterface $storage)
    {
        $this->csvFinder = $csvFinder;
        $this->csvReader = $csvReader;
        $this->storage = $storage;
        $this->emailValidator = new EmailValidator(); // getter and setter can be used to change it
    }

    public function execute(): void
    {
        do {
            try {
                $this->askDir();
                $this->askFile();
                break;
            } catch (\InvalidArgumentException $e) {
                $this->getOutput()->writeln($e->getMessage());
            }
        } while (true);

        $this->save();
    }

    private function askDir(): void
    {
        while (true) {
            try {
                $defaultDir = realpath('../');
                $question = new Question("Please enter the the directory in which CSV files are stored (default: {$defaultDir}): ", $defaultDir);
                $dir = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);
                $this->getOutput()->writeln("Selected directory: {$dir}");

                $this->csvFinder->setCsvDir($dir);
                break;
            } catch (\InvalidArgumentException $e) {
                $this->getOutput()->writeln($e->getMessage());
            }
        }
    }

    private function askFile(): void
    {
        $files = $this->csvFinder->listFiles();

        if (!count($files)) {
            throw new \InvalidArgumentException('You have provided a directory with no CSV files.');
        }

        $question = new ChoiceQuestion(
            'Please select the CSV file containing the user list: ',
            $files,
            '0'
        );

        $file = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);
        $this->csvReader->setCsv($this->csvFinder->getFileFullPath($file));
        $this->getOutput()->writeln("Selected file: {$file}");
    }

    private function askRetypeEmail(int $position, array $user): array
    {
        $question = new Question("{$position}) Please retype {$user['first_name']}'s e-mail ({$user['email']}) it looks invalid': ");
        $user['email'] = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);

        return $user;
    }

    private function save(): void
    {
        $this->getOutput()->writeln('Saving users...');

        $counter = 0;
        foreach ($this->csvReader->getEntriesAsArray() as $k => $user) {
            while (!$this->getEmailValidator()->isValid($user['email'])) {
                $user = $this->askRetypeEmail($k, $user);
            }

            $this->storage->add($user);
            $counter++;
        }
        $this->getOutput()->writeln("{$counter} users processed");
        $this->storage->save();
        $this->getOutput()->writeln('#####################');
        $this->getOutput()->writeln('####### SAVED #######');
        $this->getOutput()->writeln('#####################');
        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('');
    }

    /**
     * @return Validator
     */
    public function getEmailValidator(): Validator
    {
        return $this->emailValidator;
    }

    /**
     * @param StringValidatorInterface $emailValidator
     */
    public function setEmailValidator(StringValidatorInterface $emailValidator)
    {
        $this->emailValidator = $emailValidator;
    }
}
