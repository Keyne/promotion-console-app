<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 12/10/2018
 * Time: 22:37
 */

namespace App\Component;

interface StringValidatorInterface
{
    /**
     * @param mixed $str
     * @return mixed
     */
    public function isValid($str);
}
