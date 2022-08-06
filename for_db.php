<?php
    require $_SERVER['DOCUMENT_ROOT'] . '/rb.php';
    require 'config.php';

    $name = trim($_POST['name']);
    $count = trim($_POST['count']);

    $name = str_replace(" ", "%20", $name);

    $places = R::dispense( 'stickers' );
    $places->name = $name;
    $places->count = $count;
    R::store( $places );

?>


