<?php
    require 'rb.php';
    require_once "vendor/autoload.php";
    require "config.php";

    $botid = 1;

    function curl_load($url, $proxy, $proxyauth){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $curl_scraped_page = curl_exec($ch);
        curl_close($ch);
        return $curl_scraped_page;
    }

    $time = time();
    $time_to_stop = $time + 58;
    $sleeptime = 0;

    $timereconecttostop = (int)R::getRow("SELECT count(*) as cnt FROM proxys$botid")['cnt'] + 3;

    $timereconect = 0;

    $bot = new \TelegramBot\Api\Client($token);

    for(;;){
        $time = time();
        if($time > $time_to_stop){
            exit();
        }

        $stickernum = R::getRow('SELECT `stickernum` FROM `stickernums` WHERE `id` = "' . $botid .'"')['stickernum'];
        settype($stickernum, "int");
        $telmes = R::getAll( 'SELECT * FROM usersid' );
        $query = R::getAll( 'SELECT * FROM stickers ORDER BY stickers.position' );
        $partnum = round((count($query)/$botscount))+1;

        if($stickernum == $partnum || $stickernum > $partnum){
            $stickernum = 0;
            $cat = R::load('stickernums', $botid);
            $cat->stickernum = $stickernum;
            R::store($cat);
        }

        if(in_array($query[$stickernum]['id'], $sticksforsixthbot)){
            $stickernum++;
            $cat = R::load('stickernums', $botid);
            $cat->stickernum = $stickernum;
            R::store($cat);
            continue;
        }

        $querycount = ( int ) $query[$stickernum]['count'];
        if ($querycount == 1){
            $to_query = $query[$stickernum]['name'];

        }
        elseif ($querycount == 2){
            $to_query = $query[$stickernum]['name'] . ',' . $query[$stickernum]['name'];

        }
        elseif ($querycount == 3){
            $to_query = $query[$stickernum]['name'] . ',' . $query[$stickernum]['name'] . ',' . $query[$stickernum]['name'];
        }
        elseif ($querycount == 4){
            $to_query = $query[$stickernum]['name'] . ',' . $query[$stickernum]['name'] . ',' . $query[$stickernum]['name'] . ',' . $query[$stickernum]['name'];
        }
        else{
            continue;
        }

        $url = 'https://steamcommunity.com/market/search/render/?query=%22' . $to_query . '%22&start=0&count=100&search_descriptions=1&norender=1&sort_column=price&sort_dir=asc&appid=730&category_730_ItemSet%5B%5D=any&category_730_Weapon%5B%5D=any&category_730_Quality%5B%5D';

        $proxys = R::getAll(' SELECT * FROM `proxys' . $botid . '`');
        $proxynum = R::getRow(' SELECT `proxynum` FROM `proxynums` WHERE `id` = "' . $botid . '"')['proxynum'];
        settype($proxynum, "int");
        do {
            if( $proxynum > count($proxys)){
                $proxynum = 1;
                $cat = R::load('proxynums', $botid);
                $cat->proxynum = $proxynum;
                R::store($cat);
            }

            $proxy = $proxys[$proxynum - 1];

            $content = curl_load($url, trim($proxy['ip']), trim($proxy['logpass']));

            $decoded = json_decode($content);

            if(!is_object($decoded)){
                $proxynum++;
                $cat = R::load('proxynums', $botid);
                $cat->proxynum = $proxynum;
                R::store($cat);
                $timereconect++;
            }

        } while (!is_object($decoded));

        foreach ($decoded->results as $value) {
            if (mb_stristr ($value->name, 'Sticker')  || mb_stristr ($value->name, 'Challengers') || mb_stristr ($value->name, 'Graffiti') || mb_stristr ($value->name, 'Legends')){
                continue;
            }
            else{
                if(mb_stristr ($value->name, 'Souvenir')){
                    if(!!(!stristr ($query[$stickernum]['name'], 'DreamHack%202014'))){
                        if(!!(!stristr ($query[$stickernum]['name'], 'Katowice%202014'))){
                            if(!!(!stristr ($query[$stickernum]['name'], 'Katowice%202015'))){
                                if(!!(!stristr ($query[$stickernum]['name'], 'Cologne%202014'))){
                                    if(!!(!stristr ($query[$stickernum]['name'], 'Shooter%20Close%20(Foil)'))){
                                        if(!!(!stristr ($query[$stickernum]['name'], 'Frosty%20the%20Hitman%20(Foil)'))){
                                            if(!!(!stristr ($query[$stickernum]['name'], 'Cologne%202015'))){
                                                if(!!(!stristr ($query[$stickernum]['name'], 'All-Stars'))){
                                                    if(!!(!stristr ($query[$stickernum]['name'], 'Mountain%20(Foil)'))){
                                                    continue;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if($value->name == "Howl Pin" || $value->name == "CS:GO Patch Pack" || $value->name == "Patch | The Boss" || $value->name == "Collectible Pins Capsule Series 3" || $value->name == "Patch | Howl"){
                    continue;
                }

                $rows = R::getAll('SELECT * FROM `guns` WHERE `name` = :stickname AND `gunname` = :gunname AND `count` = :countguns' ,
                [':stickname' => $query[$stickernum]['name'],
                ':gunname' =>  $value->name,
                ':countguns' =>  $querycount
                ]);

                if (empty($rows)){
                    $str1gunname = str_replace("|", "%7C", $value->name);
                    $str2gunname = str_replace("(", "%28", $str1gunname);
                    $str3gunname = str_replace(")", "%29", $str2gunname);
                    $gunname = str_replace(" ", "+", $str3gunname);

                    $stickername = str_replace("%20", "+", $query[$stickernum]['name']);

                    $answsn = str_replace("%20", " ", $query[$stickernum]['name']);

                    if ($querycount == 1){
                        $answer = "Proxy - $proxynum\n" . $value->name . "\n" . $answsn . "\nCount - " . $querycount . "\nPrice - " . $value->sell_price_text. "\n" .
                     'https://steamcommunity.com/market/listings/730/' . str_replace('+', '%20', $gunname) . '?filter=%22' . str_replace('+', '%20', $stickername) . '%22';
                    }
                    elseif ($querycount == 2){
                        $answer = "Proxy - $proxynum\n" . $value->name . "\n" . $answsn . "\nCount - " . $querycount . "\nPrice - " . $value->sell_price_text. "\n" .
                     'https://steamcommunity.com/market/listings/730/' . str_replace('+', '%20', $gunname) . '?filter=%22' . str_replace('+', '%20', $stickername) . '%20' . str_replace('+', '%20', $stickername) . '%22';
                    }
                    elseif ($querycount == 3){
                        $answer = "Proxy - $proxynum\n" . $value->name . "\n" . $answsn . "\nCount - " . $querycount . "\nPrice - " . $value->sell_price_text. "\n" .
                     'https://steamcommunity.com/market/listings/730/' . str_replace('+', '%20', $gunname) . '?filter=%22' . str_replace('+', '%20', $stickername) . '%20' . str_replace('+', '%20', $stickername) . '%20' . str_replace('+', '%20', $stickername) . '%22';
                    }
                    elseif ($querycount == 4){
                        $answer = "Proxy - $proxynum\n" . $value->name . "\n" . $answsn . "\nCount - " . $querycount . "\nPrice - " . $value->sell_price_text. "\n" .
                     'https://steamcommunity.com/market/listings/730/' . str_replace('+', '%20', $gunname) . '?filter=%22' . str_replace('+', '%20', $stickername) . '%20' . str_replace('+', '%20', $stickername) . '%20' . str_replace('+', '%20', $stickername) . '%20' . str_replace('+', '%20', $stickername) . '%22';
                    }

                    foreach ($telmes as $telid) {
                        try{
                            $bot->sendMessage($telid['userid'], $answer);
                        } catch (Exception $e) {

                        }
                    }

                    $book = R::dispense( 'guns' );
                    $book->name = $query[$stickernum]['name'];
                    $book->count = $querycount;
                    $book->gunname = $value->name;
                    $book->countguns = $value->sell_listings;
                    $id = R::store( $book );
                }
                else{
                    $countguns = ( int ) $rows[0]['countguns'];
                    $selllist = ( int ) $value->sell_listings;
                    if ($countguns < $selllist){
                        $str1gunname = str_replace("|", "%7C", $value->name);
                        $str2gunname = str_replace("(", "%28", $str1gunname);
                        $str3gunname = str_replace(")", "%29", $str2gunname);
                        $gunname = str_replace(" ", "+", $str3gunname);

                        $stickername = str_replace("%20", "+", $query[$stickernum]['name']);
                        $answsn = str_replace("%20", " ", $rows[0]['name']);
                        if ($querycount == 1){
                            $answer = "Proxy - $proxynum\n" . $rows[0]['gunname'] . "\n" . $answsn . "\nCount - " . $querycount . "\nPrice - " . $value->sell_price_text. "\n" .
                         'https://steamcommunity.com/market/listings/730/' . str_replace('+', '%20', $gunname) . '?filter=%22' . str_replace('+', '%20', $stickername) . '%22';
                        }
                        elseif ($querycount == 2){
                            $answer = "Proxy - $proxynum\n" . $rows[0]['gunname'] . "\n" . $answsn . "\nCount - " . $querycount . "\nPrice - " . $value->sell_price_text. "\n" .
                         'https://steamcommunity.com/market/listings/730/' . str_replace('+', '%20', $gunname) . '?filter=%22' . str_replace('+', '%20', $stickername) . '%20' . str_replace('+', '%20', $stickername) . '%22';
                        }
                        elseif ($querycount == 3){
                            $answer = "Proxy - $proxynum\n" . $rows[0]['gunname'] . "\n" . $answsn . "\nCount - " . $querycount . "\nPrice - " . $value->sell_price_text. "\n" .
                         'https://steamcommunity.com/market/listings/730/' . str_replace('+', '%20', $gunname) . '?filter=%22' . str_replace('+', '%20', $stickername) . '%20' . str_replace('+', '%20', $stickername) . '%20' . str_replace('+', '%20', $stickername) . '%22';
                        }
                        elseif ($querycount == 4){
                            $answer = "Proxy - $proxynum\n" . $rows[0]['gunname'] . "\n" . $answsn . "\nCount - " . $querycount . "\nPrice - " . $value->sell_price_text. "\n" .
                         'https://steamcommunity.com/market/listings/730/' . str_replace('+', '%20', $gunname) . '?filter=%22' . str_replace('+', '%20', $stickername) . '%20' . str_replace('+', '%20', $stickername) . '%20' . str_replace('+', '%20', $stickername) . '%20' . str_replace('+', '%20', $stickername) . '%22';
                        }
                        foreach ($telmes as $telid) {
                            try{
                                $bot->sendMessage($telid['userid'], $answer);
                            } catch (Exception $e) {

                            }
                        }

                        $cat = R::load('guns', $rows[0]['id']);
                        $cat->countguns = $selllist;
                        R::store($cat);
                      }
                    elseif ($countguns > $selllist){
                        $cat = R::load('guns', $rows[0]['id']);
                        $cat->countguns = $selllist;
                        R::store($cat);
                    }
                }
            }
        }

        $stickernum++;
        $cat = R::load('stickernums', $botid);
        $cat->stickernum = $stickernum;
        R::store($cat);
        sleep($sleeptime);
    }
?>
