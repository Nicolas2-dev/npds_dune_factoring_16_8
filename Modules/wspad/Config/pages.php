<?php


global $nuke_url;
$PAGES['modules.php?ModPath=' . $ModPath . '&ModStart=wspad*']['title'] = "[french]WS-Pad[/french][english]WS-PAd[/english][spanish]WS-Pad[/spanish][german]WS-Pad[/german][chinese]WS-Pad[/chinese]+|$title+";
$PAGES['modules.php?ModPath=' . $ModPath . '&ModStart=wspad*']['run'] = "yes";
$PAGES['modules.php?ModPath=' . $ModPath . '&ModStart=wspad*']['blocs'] = "0";
$PAGES['modules.php?ModPath=' . $ModPath . '&ModStart=wspad*']['TinyMce'] = 1;
$PAGES['modules.php?ModPath=' . $ModPath . '&ModStart=wspad*']['TinyMce-theme'] = "full+setup";
$PAGES['modules.php?ModPath=' . $ModPath . '&ModStart=wspad']['css'] = [$nuke_url . "/lib/bootstrap/dist/css/bootstrap-icons.css+"];
