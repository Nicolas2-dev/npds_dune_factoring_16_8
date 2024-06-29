<?php

use Npds\Sform\SformManager;


$sform_path = 'Npds/Sform/';

global $m;
$m = new SformManager();
//********************
$m->add_form_title("Bugs_Report");
$m->add_form_method("post");
$m->add_form_check("false");
$m->add_mess(" * d&eacute;signe un champ obligatoire ");
$m->add_submit_value("submitS");
$m->add_url("newtopic.php");

/************************************************/
include($sform_path . "forum/$formulaire");
/************************************************/

if (!$submitS)
    echo $m->print_form('');
else
    $message = $m->aff_response('', 'not_echo', '');
