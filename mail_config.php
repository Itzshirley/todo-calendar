<?php
return [
    "host" => getenv("MAIL_HOST"),
    "username" => getenv("MAIL_USER"),
    "password" => getenv("MAIL_PASS"),
    "port" => getenv("MAIL_PORT")
];

