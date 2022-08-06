<?php

	$botscount = 5;
    $token = "token";
    R::setup( 'mysql:host=localhost;dbname=database', 'user', 'password' );


    // stickers for 6 bot
    $sticksforsixthbot = [100, 104, 106, 108, 110, 111, 141, 194, 401, 402, 403, 404, 405, 409, 642, 643,  43, 529, 523, 292, 191, 397, 211];

    $stickersstring = '(';

    foreach ($sticksforsixthbot as $value) {
    	$stickersstring .= $value . ', ';
    }

    $stickersstring = substr($stickersstring, 0, -2) . ')';

?>