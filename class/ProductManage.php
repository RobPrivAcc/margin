<?php
class ProductManage{
        function nameChange($name,$getSet){
        $nameChange = $name;
        $array[0] = array(
                            'char' => '\'',
                            'charChange' => '|apo|');
        $array[1] = array(
                            'char' => '"',
                            'charChange' => '|dQuote|');
        //print_r($array);
        for($i=0; $i < count($array); $i++){
            if($getSet == 'set'){
                $nameChange = str_replace($array[$i]['char'],$array[$i]['charChange'],$nameChange);
            }elseif($getSet == 'get'){
                $nameChange = str_replace($array[$i]['charChange'],$array[$i]['char'],$nameChange);
            }
        }
        return $nameChange;
    }
}