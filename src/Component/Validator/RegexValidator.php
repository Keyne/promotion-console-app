<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 12/10/2018
 * Time: 22:40
 */

namespace App\Component\Validator;

class RegexValidator implements StringValidatorInterface
{
    const REGEX_ID = '/[0-9]+-[0-9]+/';
    const REGEX_FIRST_NAME = '/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.\'-]+$/u';
    const REGEX_COUNTRY_CODE = '/[A-Z]{2}/';
    const REGEX_COUNTRY_COORDINATES = '/-?[0-9]+(\.[0-9]+)?/';
    const REGEX_COUNTRY_DATE = '/[0-9]{4}-[0-9]{2}-[0-9]{2} ?T?([0-9]{2})?:([0-9]{2})?([0-9]{2})?Z?/';

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
