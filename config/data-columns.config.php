<?php

use \App\Component\Validator\RegexValidator;
use \App\Component\Validator\EmailValidator;
use \App\Component\DataColumnInterface as ColumnFeature;

return $fields = [
    [
        ColumnFeature::LABEL => 'ID (Ex: "1234-567")',
        ColumnFeature::NAME => 'id',
        ColumnFeature::VALIDATOR => new RegexValidator(RegexValidator::REGEX_ID)
    ],
    [
        ColumnFeature::LABEL => 'First name',
        ColumnFeature::NAME => 'first_name',
        ColumnFeature::VALIDATOR => new RegexValidator(RegexValidator::REGEX_FIRST_NAME)
    ],
    [
        ColumnFeature::LABEL => 'Email',
        ColumnFeature::NAME => 'email',
        ColumnFeature::VALIDATOR => new EmailValidator()
    ],
    [
        ColumnFeature::LABEL => 'Country code (Ex format: Uppercase "BR")',
        ColumnFeature::NAME => 'country',
        ColumnFeature::VALIDATOR => new RegexValidator(RegexValidator::REGEX_COUNTRY_CODE)
    ],
    [
        ColumnFeature::LABEL => 'Latitude (Ex format: "13.5936457")',
        ColumnFeature::NAME => 'latitude',
        ColumnFeature::VALIDATOR => new RegexValidator(RegexValidator::REGEX_COUNTRY_COORDINATES)
    ],
    [
        ColumnFeature::LABEL => 'Longitude (Ex format: "-50.9936457")',
        ColumnFeature::NAME => 'longitude',
        ColumnFeature::VALIDATOR => new RegexValidator(RegexValidator::REGEX_COUNTRY_COORDINATES)]
    ,
    [
        ColumnFeature::LABEL => 'Date (Ex format: "2018-03-10T12:45:57Z")',
        ColumnFeature::LABEL => 'Date Joined',
        ColumnFeature::NAME => new RegexValidator(RegexValidator::REGEX_COUNTRY_DATE)
    ],
];