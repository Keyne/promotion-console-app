<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 11/10/2018
 * Time: 21:14
 */

namespace App\Factory\Interfaces;

use Symfony\Component\Console\Command\Command;

interface CommandFactoryInterface
{
    public function create(): Command;
}
