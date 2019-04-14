<?php

const CONFIG_PATH = __DIR__ . DIRECTORY_SEPARATOR . '../config.ini';
if (file_exists(CONFIG_PATH)) {
    putenv("DATABASE_URL=" . parse_ini_file(CONFIG_PATH)['DATABASE_URL']);
}