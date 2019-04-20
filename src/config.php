<?php

if (file_exists(__DIR__ . '/../config.ini') 
|| (getenv('DATABASE_URL') === null)) {
    putenv("DATABASE_URL=" . parse_ini_file(CONFIG_PATH)['DATABASE_URL']);
}
