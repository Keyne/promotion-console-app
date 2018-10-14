<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 12/10/2018
 * Time: 05:25
 */

namespace App\Command\Step;

use App\Component\Exception\AlertMessageException;
use App\Component\Storage\StorageInterface;
use App\Component\Winner;
use App\Service\FileManagementServiceInterface;

class WinnerStep extends AbstractStep
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
        $users = $this->fileService->getRecords();
        $total = count($users);
        if (!$total) {
            throw new AlertMessageException('Users database is empty, please load a CSV file first');
        }

        $this->getOutput()->writeln('
-----------------------------------------------------------------------
        ');
        $this->getOutput()->writeln("<question>
            )                  (        )     )       (      (    (      
  *   )  ( /(        (  (      )\\ )  ( /(  ( /(       )\\ )   )\\ ) )\\ )   
` )  /(  )\\()) (     )\\))(   '(()/(  )\\()) )\\()) (   (()/(  (()/((()/(   
 ( )(_))((_)\\  )\\   ((_)()\\ )  /(_))((_)\\ ((_)\\  )\\   /(_))  /(_))/(_))  
(_(_())  _((_)((_)  _(())\\_)()(_))   _((_) _((_)((_) (_))   (_)) (_))    
|_   _| | || || __| \\ \\((_)/ /|_ _| | \\| || \\| || __|| _ \\  |_ _|/ __|   
  | |   | __ || _|   \\ \\/\\/ /  | |  | .` || .` || _| |   /   | | \\__ \\   
  |_|   |_||_||___|   \\_/\\_/  |___| |_|\\_||_|\\_||___||_|_\\  |___||___/   
</question>                                                                                
        ");

        $winner = new Winner();
        $winner->setDataTable($users);
        $winnerUser = $winner->getWinner();
        $this->getOutput()->writeln("The winner is: {$winnerUser['first_name']} (id: {$winnerUser['id']})");

        $this->getOutput()->writeln('
-----------------------------------------------------------------------
        ');
    }
}
