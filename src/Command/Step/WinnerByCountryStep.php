<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 12/10/2018
 * Time: 05:25
 */

namespace App\Command\Step;

use App\Component\Storage\StorageInterface;
use App\Component\Winner;
use App\Service\FileManagementServiceInterface;
use App\Service\PromotionServiceInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class WinnerByCountryStep extends AbstractStep
{

    /**
     * @var FileManagementServiceInterface
     */
    private $fileService;

    /**
     * @var PromotionServiceInterface
     */
    private $promotionService;

    public function __construct(FileManagementServiceInterface $fileService, PromotionServiceInterface $promotionService)
    {
        $this->fileService = $fileService;
        $this->promotionService = $promotionService;
    }

    public function execute(): void
    {
        $countries = $this->promotionService->getColumnsDistinctValues('country');

        $question = new ChoiceQuestion(
            'Select the country: ',
            $countries,
            '0'
        );

        $countryCode = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question);
        $countryTotal = count($countries);

        if (!$countryTotal) {
            $this->getOutput()->writeln('There\'s no users in this country');
            return;
        }

        // TODO: Move drawings to a view layer
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

        $winnerUser = $this->promotionService->getWinnerByColumn('country', $countryCode);

        $this->getOutput()->writeln("The winner for {$countryCode} is: {$winnerUser['first_name']} (id: {$winnerUser['id']})");

        $this->getOutput()->writeln('
-----------------------------------------------------------------------
        ');
    }
}
