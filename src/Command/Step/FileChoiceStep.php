<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 11/10/2018
 * Time: 22:07
 */

namespace App\Command\Step;

use App\Component\Exception\AlertMessageException;
use App\Component\DataColumnInterface as ColumnFeature;
use App\Component\Validator\EmailValidator;
use App\Component\Csv\CsvFinderInterface;
use App\Component\Csv\CsvReaderInterface;
use App\Component\Storage\StorageInterface;
use App\Component\Validator\StringValidatorInterface;
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
     * @var string
     */
    private $defaultBaseDir;

    /**
     * @var array
     */
    private $dataColumns;

    /**
     * @var StringValidatorInterface
     */
    private $emailValidator;

    public function __construct(array $dataColumns, $defaultBaseDir, CsvFinderInterface $csvFinder, CsvReaderInterface $csvReader, StorageInterface $storage)
    {
        $this->dataColumns = $dataColumns;
        $this->defaultBaseDir = $defaultBaseDir;
        $this->csvFinder = $csvFinder;
        $this->csvReader = $csvReader;
        $this->storage = $storage;
        $this->emailValidator = new EmailValidator(); // getter and setter can be used to change it
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
        $defaultDir = realpath($this->defaultBaseDir);
        $question = new Question("Please enter the the directory in which CSV files are stored (default: {$defaultDir}): ", $defaultDir);
        $dir = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);
        $this->getOutput()->writeln("Selected directory: {$dir}");

        $this->csvFinder->setCsvDir($dir);
    }

    private function askFile(): void
    {
        $files = $this->csvFinder->listFiles();

        if (!count($files)) {
            throw new AlertMessageException('You have provided a directory with no CSV files.');
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

    private function askRetype(string $column, array $user): array
    {
        $question = new Question("Please retype {$user['first_name']}'s data, \"{$user[$column]}\" looks invalid': ");
        $user['email'] = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);

        return $user;
    }

    private function save(): void
    {
        $this->getOutput()->writeln('Saving users...');

        $counter = 0;

        foreach ($this->csvReader->getEntriesAsArray() as $k => $user) {
            $counter++;
            while (true) {
                foreach ($user as $column => $value) {
                    $validatorKey = array_search($column, array_column($this->dataColumns, 'name'));

                    if (!isset($this->dataColumns[$validatorKey])) {
                        throw new \RuntimeException("Unable to get field by column: [$validatorKey][$column]");
                    }

                    $validator = $this->dataColumns[$validatorKey][ColumnFeature::VALIDATOR];
                    if ($validator instanceof StringValidatorInterface) {
                        while (!$validator->isValid($user[$column])) {
                            $user = $this->askRetype($column, $user);
                        }
                    }
                }
                $this->storage->addOrUpdate($user);
                break;
            }
        }
        $this->getOutput()->writeln("{$counter} users processed");
        $this->storage->save();
        $this->getOutput()->writeln('#####################');
        $this->getOutput()->writeln('####### SAVED #######');
        $this->getOutput()->writeln('#####################');
        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('');
    }
}
