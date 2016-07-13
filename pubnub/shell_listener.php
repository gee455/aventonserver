<?php

while (1) {
    $str1 = shell_exec('php listener.php 2>&1 > listener_errors.log');
}
?>
