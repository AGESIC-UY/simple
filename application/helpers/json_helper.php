<?php

function xml_encode($mixed,$domElement=null,$DOMDocument=null){
    if(is_object($mixed))
        $mixed=get_object_vars($mixed);

    if(is_null($DOMDocument)){
        $DOMDocument=new DOMDocument;
        $DOMDocument->formatOutput=true;
        xml_encode($mixed,$DOMDocument,$DOMDocument);
        echo $DOMDocument->saveXML();
    }
    else{
        if(is_array($mixed)){
            foreach($mixed as $index=>$mixedElement){
                if(is_int($index)){
                    if($index==0){
                        $node=$domElement;
                    }
                    else{
                        $node=$DOMDocument->createElement($domElement->tagName);
                        $domElement->parentNode->appendChild($node);
                    }
                }
                else{
                    $plural=$DOMDocument->createElement($index);
                    $domElement->appendChild($plural);
                    $node=$plural;
                    //if(rtrim($index,'s')!==$index){
                    //    $singular=$DOMDocument->createElement(rtrim($index,'s'));
                    //    $plural->appendChild($singular);
                    //    $node=$singular;
                    //}
                }
                xml_encode($mixedElement,$node,$DOMDocument);
            }
        }
        else{
            $domElement->appendChild($DOMDocument->createTextNode($mixed));
        }
    }
}

/**
 * Indents a flat JSON string to make it more human-readable.
 *
 * @param string $json The original JSON string to process.
 *
 * @return string Indented version of the original JSON string.
 */
function json_indent($json) {

    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '    ';
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;

    for ($i=0; $i<=$strLen; $i++) {

        // Grab the next character in the string.
        $char = substr($json, $i, 1);

        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;

        // If this character is the end of an element,
        // output a new line and indent the next line.
        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        }

        // Add the character to the result string.
        $result .= $char;

        // If the last character was the beginning of an element,
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }

            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }

        $prevChar = $char;
    }

    return $result;
}