<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 11/10/2018
 * Time: 22:07
 */

namespace App\Command\Step;

use App\Command\Step\CommandStepInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractStep implements CommandStepInterface
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @param InputInterface $input
     * @return CommandStepInterface
     */
    public function setInput(InputInterface $input): CommandStepInterface
    {
        $this->input = $input;
        return $this;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     * @return CommandStepInterface
     */
    public function setOutput(OutputInterface $output): CommandStepInterface
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @return QuestionHelper
     */
    public function getQuestionHelper(): QuestionHelper
    {
        return $this->questionHelper;
    }

    /**
     * @param QuestionHelper $questionHelper
     * @return CommandStepInterface
     */
    public function setQuestionHelper(QuestionHelper $questionHelper): CommandStepInterface
    {
        $this->questionHelper = $questionHelper;
        return $this;
    }
}