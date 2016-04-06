<?php

// $date is (YYYY-mm-dd)
function add_working_days($date, $days) {
    $timestamp=strtotime($date);
    $skipdays = array("Saturday", "Sunday");
    $skipdates= array();
    $feriados=Doctrine::getTable('Feriado')->findAll();
    foreach($feriados as $f)
        $skipdates[]=$f->fecha;
    
    $i = 1;
    while ($days >= $i) {
        $timestamp = strtotime("+1 day", $timestamp);
        if (in_array(date("l", $timestamp), $skipdays)) {
            $days++;
        }else if (in_array(date("Y-m-d",$timestamp), $skipdates)){
            $days++;
        }
        $i++;
    }

    return date("Y-m-d",$timestamp);
}