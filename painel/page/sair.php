<?php

session_start();
unset($_SESSION);
session_destroy();
echo "<script>location.href='https://probusca.com';</script>Redirecionando...";

?>
