<?php

use Symfony\Component\Console\Application;
use App\Command\DefaultCommand;

/*
 * Create a console application
 */
$application = new Application();

$commandFactory = new \App\Factory\CommandFactory();
$command = $commandFactory->create();

/*
 * Add the commands.
 */
$application->add($command);

/*
 * Run the application.
 */
$application->run();
