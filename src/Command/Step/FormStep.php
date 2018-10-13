<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 12/10/2018
 * Time: 22:33
 */

namespace App\Command\Step;


use App\Component\StorageInterface;
use App\Component\StringValidatorInterface;
use Symfony\Component\Console\Question\Question;

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
            while(true) {
                try {
                    if (empty($fieldName['label'])) {
                        throw new \RuntimeException('Field label not set');
                    }
                    $fieldLabel = isset($field['label']) ? $field['label'] : 'undefined';
                    if (empty($fieldName['name'])) {
                        throw new \RuntimeException('Field name not set');
                    }
                    $fieldName = $field['name'];

                    $validator = isset($field['validator']) && $field['validator'] instanceof StringValidatorInterface ? $field['validator'] : false;
                    $question = new Question($fieldLabel . ': ');
                    $value = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);

                    /**
                     * @var StringValidatorInterface|bool $validator
                     */
                    if (($validator && $validator->isValid($value)) || !empty($value)) {
                        $inputs[$fieldName] = $value;
                        break;
                    } else {
                        $this->getOutput()->writeln('It looks like you\'ve typed a wrong value');
                    }
                } catch (\InvalidArgumentException $e) {
                    $this->getOutput()->writeln($e->getMessage());
                }
            }
        }
        $this->storage->add($inputs);
        $this->storage->save();
    }
}