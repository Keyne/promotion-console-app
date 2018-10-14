# Symfony Gift Promotion Console App (Beta)
This console application let you export your user's table in CSV format and choose a random winner of a given promotion.

Under `/config/data-columns.config.php` you will be able set the columns and validators of your table (a default is already configured).
This will be used to insert new users on the JSON object, which is by default saved in `/data/database.json`. It will also validate CSV data during the parsing.

After exporting a CSV file you can put it in the default directory for loading in the console app: `/data` 

## Features included in this beta version

- Configurable export table
- Interactive menu
- Select default folder to load CSV files from
- Interactve validation during CSV loading
- Load any number of CSV files
- Choose a random winner
- Choose a random winner by country code showing available codes (`TODO: Let choose column for sorting`)
- List users in JSON database
- Add new users (with input validation)
- Prevents duplicate IDs by updating entries with same ID

## Running the console
Before anything:

```
$ composer install
```

The console can be started with by running the PHP console file.

```
$ php console app:start
```

## PSR-2
The application is PSR-2 compliant and comes with an included Code sniffer
```
$ vendor/bin/phpcs ./src --ignore=./src/AppKernel.php
$ vendor/bin/phpcs ./tests --ignore=build
```

## Test coverage (PHPUnit)
There's a significant amount of tests which prevents application from breaking during changes. Altough, this project has not been developed under TDD and thus the tests does't cover 100% yet.


Open on your browser the following file to view the test coverage results: `testes/build/coverage/index.html`.
 
```
$ vendor/bin/phpunit -c tests/phpunit.xml
```

## Sample columns configuration

```
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
        ColumnFeature::NAME => 'Date Joined',
        ColumnFeature::VALIDATOR => new RegexValidator(RegexValidator::REGEX_COUNTRY_DATE)
    ],
];
```
