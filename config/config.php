<?php

use App\Component\AppConfigInterface as Config;

return $config = [
    Config::DEFAULT_BASE_DIR => realpath(dirname(__FILE__) . '/../data')
];