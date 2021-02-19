<?php
$file = "../composer.lock";
$json = file_get_contents($file);
$data = json_decode($json, true);
$version = '';
foreach($data['packages'] as $item){
    if($item['name'] == 'magento/magento2-base'){
        $version = $item['version'];break;
    }
}
$vs = explode('.',$version);
if($vs[1] < 2){
    include 'index21.php';
}else{
    include 'index22.php';
}