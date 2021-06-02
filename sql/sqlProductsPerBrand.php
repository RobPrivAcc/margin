<?php
    include("../class/Product.php");
    include("../class/DB.php");
    include("../class/XML.php");
    
    set_time_limit(0);
    ini_set('max_execution_time', 3000);
    ini_set('max_input_vars', 9000);
    
    $brandName = $_POST['brandName'];  //getting supplier name from select
    
    $isDiscontinued = $_POST['isDiscontinued'];
    
    $xml = new XML($_SERVER["DOCUMENT_ROOT"].'/dbXML.xml');
    $db = new DB($xml->getConnectionArray());
    
    $product = new Product($db->getDbConnection(2));

    
    $allProductsFromBrand = $product->allProdFromBrand($brandName,$isDiscontinued);
    
    echo "<input type = 'hidden' id='array' value='".json_encode($allProductsFromBrand,true)."'/>";