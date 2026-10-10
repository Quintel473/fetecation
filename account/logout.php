<?php
require_once __DIR__ . '/../includes/customer-auth.php';

customer_logout();

header('Location: /fetecation/login.php?loggedout=1');
exit;