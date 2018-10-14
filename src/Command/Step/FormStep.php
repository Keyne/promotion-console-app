<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 12/10/2018
 * Time: 22:33
 */

namespace App\Command\Step;

use App\Component\Exception\AlertMessageException;
use App\Component\Storage\StorageInterface;
use App\Component\Validator\StringValidatorInterface;
use Symfony\Component\Console\Question\Question;
use App\Component\DataColumnInterface as ColumnFeature;

class FormStep extends AbstractStep
{
    /**
     *
     * @var array ['label' => '', 'name' => '', 'validator' => StringValidatorInterface $validator]
     */
    private $fields;

    /**
     * @var StorageInterface
     */
    private $storage;

    public function __construct(array $fields, StorageInterface $storage)
    {
        $this->fields = $fields;
        $this->storage = $storage;
    }

    public function execute(): void
    {
        $this->getOutput()->writeln('Please fill with user\'s data:');
        $this->askInputs();
    }

    private function askInputs(): void
    {
        $inputs = [];
        foreach ($this->fields as $field) {
            while (true) {
                try {
                    if (empty($field[ColumnFeature::LABEL])) {
                        throw new \RuntimeException('Field label not set');
                    }
                    $fieldLabel = isset($field[ColumnFeature::LABEL]) ? $field[ColumnFeature::LABEL] : 'undefined';
                    if (empty($field['name'])) {
                        throw new \RuntimeException('Field name not set');
                    }
                    $fieldName = $field['name'];

                    $validator = isset($field[ColumnFeature::VALIDATOR]) && $field[ColumnFeature::VALIDATOR] instanceof StringValidatorInterface ? $field[ColumnFeature::VALIDATOR] : false;
                    $question = new Question($fieldLabel . ': ');
                    $value = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);

                    /**
                     * @var StringValidatorInterface|bool $validator
                     */
                    if (!empty($value)) {
                        if (($validator && !$validator->isValid($value))) {
                            throw new AlertMessageException('It looks like you\'ve typed a invalid value');
                        }
                        $inputs[$fieldName] = $value;
                        break;
                    }
                    $this->getOutput()->writeln('Please fill the field correctly');
                } catch (AlertMessageException $e) {
                    $this->getOutput()->writeln($e->getMessage());
                }
            }
        }
        $this->storage->addOrUpdate($inputs);
        $this->storage->save();
        $this->getOutput()->writeln('#####################');
        $this->getOutput()->writeln('####### SAVED #######');
        $this->getOutput()->writeln('#####################');
        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('');
    }
}
