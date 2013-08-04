<?php

$roles = array(
    'ROLE_USER',
    'ROLE_ADMIN'
);

$access_control = array(
    '^/admin' => 'ROLE_ADMIN'
);