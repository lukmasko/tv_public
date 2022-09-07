<?php

namespace Kernel\Auxiliary;

function getIntFromGet($key) : ?int {
    return isset($_GET[$key]) ? intval($_GET[$key]) : NULL;
}

function getStringFromGet($key) : ?string {
    return isset($_GET[$key]) ? $_GET[$key] : NULL;
}

function getIntFromPost($key) : ?int {
    return isset($_POST[$key]) ? intval($_POST[$key]) : NULL;
}

function getStringFromPost($key) : ?string {
    return isset($_POST[$key]) ? $_POST[$key] : NULL;
}

function getFile($key) : ?array {
    return isset($_FILES[$key]) ? $_FILES[$key] : NULL;
}