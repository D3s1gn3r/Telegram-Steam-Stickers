<?php

    require $_SERVER['DOCUMENT_ROOT'] . '/rb.php';
	require 'config.php';

    $id = trim($_POST['id']);

    R::exec('DELETE FROM `stickers` WHERE `id` = ?', array(
    $id
    ));

?>


