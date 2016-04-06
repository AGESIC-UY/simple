<?php

function matrix_to_html($matrix){
    $html='<table class="table" >';
    foreach($matrix as $row){
        $html.='<tr>';
        foreach($row as $data){
            $html.='<td style="border: 1px solid #ddd;">'.$data.'</td>';
        }
        $html.='</tr>';
    }
    $html.='</table>';
    return $html;
}