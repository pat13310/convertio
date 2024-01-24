<?php

if (!function_exists('getMessageBtn')) {
    function getMessageBtn($action = "")
    {
        $btn = "Convertir";
        if ($action == "convert") {
            $btn = "Convertir";
        } elseif ($action == "improve") {
            $btn = "Améliorer";
        } elseif ($action == "erase") {
            $btn = "Modifier Fond";
        }
        return $btn;
    }
}
