<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 11/10/2018
 * Time: 22:17
 */

namespace App\Command\Step;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CommandStepInterface
{
    public function execute(): void;

    public function setInput(InputInterface $input): CommandStepInterface;

    public function setOutput(OutputInterface $output): CommandStepInterface;

    public function setQuestionHelper(QuestionHelper $helper): CommandStepInterface;
}