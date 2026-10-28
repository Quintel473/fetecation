<?php
require_once __DIR__ . '/../includes/customer-auth.php';

customer_logout();

header('Location: /fetecation/auth/login.php?loggedout=1');
exit;