<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 12/10/2018
 * Time: 05:25
 */

namespace App\Command\Step;


use App\Component\StorageInterface;

class WinnerStep extends AbstractStep
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

        $lastIndex = $total -1;
        $winner = rand(0, $lastIndex);
        $this->getOutput()->writeln("The winner is: {$users[$winner]['first_name']} (id: {$users[$winner]['id']})");
    }
}