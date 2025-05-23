<?php
return [
    'host' => getenv('MYSQL_HOST') ?: 'localhost',
    'dbname' => getenv('MYSQL_DATABASE') ?: 'judge_db',
    'username' => getenv('MYSQL_USER') ?: 'judge_user',
    'password' => getenv('MYSQL_PASSWORD') ?: 'judge_pass',
    'charset' => 'utf8mb4'
];
