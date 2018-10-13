<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 12/10/2018
 * Time: 05:25
 */

namespace App\Command\Step;

use App\Component\StorageInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class WinnerByCountryStep extends AbstractStep
{

    /**
     * @var StorageInterface
     */
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function execute(): void
    {
        $users = $this->storage->getAll();
        $total = count($users);
        if (!$total) {
            throw new \LengthException('Users database is empty, please load a CSV file first');
        }

        $usersByCountry = [];
        foreach ($users as $user) {
            $usersByCountry[$user['country']][] = $user;
        }

        $question = new ChoiceQuestion(
            'Select the country: ',
            array_keys($usersByCountry),
            '0'
        );

        $countryCode = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);

        $countryTotal = count($usersByCountry[$countryCode]);

        if (!$countryTotal) {
            $this->getOutput()->writeln('There\'s no users in this country');
            return;
        }

        $lastIndex = $countryTotal -1;
        $winner = rand(0, $lastIndex);
        $this->getOutput()->writeln("The winner for {$countryCode} is: {$users[$winner]['first_name']} (id: {$users[$winner]['id']})");
    }
}
