<?php

function ci_validacion($d) {
    $cd = explode('-', $d);

    if ($cd[0] == "uy" && $cd[1] == "ci") {
        $d = $cd[2];
        $d = str_replace('.', '', $d);
        if (!is_numeric($d)) {
            return false;
        } else {
            if (strlen($d) < 6 || strlen($d) > 8) {
                return false;
            }

            $rep = str_replace(substr($d, 0, 1), "", $d);
            if ($rep == "") {
                return false;
            }

            $d = str_replace("[^\\d]", "", $d);
            $d1 = substr($d, 0, strlen($d) - 1);
            $d2 = substr($d, strlen($d) - 1, strlen($d));

            $s = calcularDigitoCedulaIdentidadUruguaya($d1);
            $z = (int) $d2[0];
            if ($s == $z) {
                return true;
            } else {
                return false;
            }
        }
    } else {
        return true;
    }
}

function calcularDigitoCedulaIdentidadUruguaya($ci) {
    if (!$ci) {
        throw new InvalidArgumentException("");
    }

    $ci = str_replace("[^\\d]", "", $ci);
    $ciLen = (int) strlen($ci);

    $s = 0;
    $v = [2, 9, 8, 7, 6, 3, 4];
    for ($i = count($v) - $ciLen; $i < count($v); $i++) {
        $a = $v[$i];
        $b = (int) $ci[$i - (count($v) - $ciLen)];
        $s = ($s + ($a * $b)) % 10;
    }

    $s = (10 - ($s % 10)) % 10;

    return $s;
}
