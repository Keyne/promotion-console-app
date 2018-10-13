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

class RegexValidator implements StringValidatorInterface
{
    /**
     * @var string
     */
    private $regex;

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    /**
     * @param mixed $str
     * @return mixed
     */
    public function isValid($str)
    {
        return preg_match($this->regex, $str) === 1;
    }
}