<?php

use Npds\Sform\SformManager;


$sform_path = 'Npds/Sform/';

global $m;
$m = new SformManager();
//********************
$m->add_form_title('Register');
$m->add_form_id('Register');
$m->add_form_method('post');
$m->add_form_check('false');
$m->add_url('admin.php');

/************************************************/
include($sform_path . 'extend-user/adm_formulaire.php');
/************************************************/
echo $m->print_form('');
