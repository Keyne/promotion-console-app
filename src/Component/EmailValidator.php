<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 12/10/2018
 * Time: 22:40
 */

namespace App\Component;


use App\Component\StringValidatorInterface;
use EmailValidator\Validator;

class EmailValidator extends Validator implements StringValidatorInterface
{

}