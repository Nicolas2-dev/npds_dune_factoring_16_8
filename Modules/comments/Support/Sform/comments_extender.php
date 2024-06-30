<?php

use Npds\Sform\SformManager;
use Npds\Support\Facades\Language;


include_once("Npds/Sform/sform.php");

global $m;

$m = new SformManager();
//********************
$m->add_form_title("coolsus");
$m->add_form_method("post");
$m->add_form_check("false");
$m->add_mess("[french]* désigne un champ obligatoire[/french][english]* required field[/english]");
$m->add_submit_value("submitS");
$m->add_url("modules.php");


include("modules/comments/Support/Sform/$formulaire");

if (!isset($GLOBALS["submitS"])) {
    echo Language::aff_langue($m->print_form(''));
} else {
    $message = Language::aff_langue($m->aff_response('', "not_echo", ''));
}
