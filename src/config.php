<?php

const CONFIG_PATH = __DIR__ . '/../config.ini';
if (file_exists(CONFIG_PATH) || (getenv('DATABASE_URL') === null)) {
    putenv("DATABASE_URL=" . parse_ini_file(CONFIG_PATH)['DATABASE_URL']);
}
