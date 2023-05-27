<?php

namespace app\View;

class getData
{
    public static function getData($gateway)
{
    $storeData = "";
    
    $storeData = $gateway->getDataList();
    echo $storeData;
}

}

?>
