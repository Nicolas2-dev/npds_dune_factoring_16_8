<?php

use Npds\Support\Facades\News;
use Npds\Cache\SuperCacheEmpty;
use Npds\Support\Facades\Theme;
use Npds\Cache\SuperCacheManager;
use Npds\Support\Facades\Language;
use Npds\Support\Facades\Paginator;

if (strstr($ModPath, '..') 
|| strstr($ModStart, '..') 
|| stristr($ModPath, 'script') 
|| stristr($ModPath, 'cookie') 
|| stristr($ModPath, 'iframe') 
|| stristr($ModPath, 'applet') 
|| stristr($ModPath, 'object') 
|| stristr($ModPath, 'meta') 
|| stristr($ModStart, 'script') 
|| stristr($ModStart, 'cookie') 
|| stristr($ModStart, 'iframe') 
|| stristr($ModStart, 'applet') 
|| stristr($ModStart, 'object') 
|| stristr($ModStart, 'meta')) {
    die();
}

if (!function_exists("Mysql_Connexion")) {
    include("Bootstrap/Boot.php");
}

include("modules/$ModPath/Config/archive-stories.conf.php");
include("modules/$ModPath/Config/cache.timings.php");

if (!isset($start)) {
    $start = 0;
}

include("header.php");

// Include cache manager
if ($SuperCache) {
    $cache_obj = new SuperCacheManager();
    $cache_obj->startCachingPage();
} else {
    $cache_obj = new SuperCacheEmpty();
}

if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache)) {
    if ($arch_titre) {
        echo Language::aff_langue($arch_titre);
    }

    echo '
    <hr />
    <table id ="lst_art_arch" data-toggle="table"  data-striped="true" data-search="true" data-show-toggle="true" data-show-columns="true" data-mobile-responsive="true" data-icons-prefix="fa" data-buttons-class="outline-secondary" data-icons="icons">
        <thead>
            <tr>
                <th data-sortable="true" data-sorter="htmlSorter" data-halign="center" class="n-t-col-xs-4">' . translate("Articles") . '</th>
                <th data-sortable="true" data-halign="center" data-align="right" class="n-t-col-xs-1">' . translate("lus") . '</th>
                <th data-halign="center" data-align="right">' . translate("Posté le") . '</th>
                <th data-sortable="true" data-halign="center" data-align="left">' . translate("Auteur") . '</th>
                <th data-halign="center" data-align="center" class="n-t-col-xs-2">' . translate("Fonctions") . '</th>
            </tr>
        </thead>
        <tbody>';

    if (!isset($count)) {
        $result0 = Q_select("SELECT COUNT(sid) AS count 
                             FROM " . sql_table('stories') . " 
                             WHERE archive='$arch'", 3600);

        $count = $result0[0];
        $count = $count['count'];
    }

    $nbPages = ceil($count / $maxcount);
    $current = 1;

    if ($start >= 1) {
        $current = $start / $maxcount;
    } else if ($start < 1) {
        $current = 0;
    } else {
        $current = $nbPages;
    }

    $xtab = $arch == 0 
        ? News::news_aff("libre", "WHERE archive='$arch' ORDER BY sid DESC LIMIT $start,$maxcount", $start, $maxcount) 
        : News::news_aff("archive", "WHERE archive='$arch' ORDER BY sid DESC LIMIT $start,$maxcount", $start, $maxcount);

    $ibid = 0;
    $story_limit = 0;

    while (($story_limit < $maxcount) and ($story_limit < sizeof($xtab))) {
        list($s_sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant) = $xtab[$story_limit];
        
        $story_limit++;

        $printP = '<a href="print.php?sid=' . $s_sid . '&amp;archive=' . $arch . '"><i class="fa fa-print fa-lg" title="' . translate("Page spéciale pour impression") . '" data-bs-toggle="tooltip" data-bs-placement="left"></i></a>';
        $sendF = '<a class="ms-4" href="friend.php?op=FriendSend&amp;sid=' . $s_sid . '&amp;archive=' . $arch . '"><i class="fa fa-at fa-lg" title="' . translate("Envoyer cet article à un ami") . '" data-bs-toggle="tooltip" data-bs-placement="left" ></i></a>';
        
        $sid = $s_sid;

        if ($catid != 0) {
            $resultm = sql_query("SELECT title 
                                  FROM " . sql_table('stories_cat') . " 
                                  WHERE catid='$catid'");

            list($title1) = sql_fetch_row($resultm);

            $title = '<a href="article.php?sid=' . $sid . '&amp;archive=' . $arch . '" >' . Language::aff_langue(ucfirst($title)) . '</a> [ <a href="index.php?op=newindex&amp;catid=' . $catid . '">' . Language::aff_langue($title1) . '</a> ]';
        } else {
            $title = '<a href="article.php?sid=' . $sid . '&amp;archive=' . $arch . '" >' . Language::aff_langue(ucfirst($title)) . '</a>';
        }

        preg_match('#^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$#', $time, $datetime);

        $datetime = $datetime[3] . '-' . $datetime[2] . '-' . $datetime[1] . ' ' . $datetime[4] . ':' . $datetime[5] . ':' . $datetime[6];

        echo '
            <tr>
            <td>' . $title . '</td>
            <td>' . $counter . '</td>
            <td><small>' . $datetime . '</small></td>
            <td>' . Theme::userpopover($informant, 40, 2) . ' ' . $informant . '</td>
            <td>' . $printP . $sendF . '</td>
            </tr>';
    }

    echo '
            </tbody>
        </table>
        <div class="d-flex my-3 justify-content-between flex-wrap">
        <ul class="pagination pagination-sm">
            <li class="page-item disabled"><a class="page-link" href="#" >' . translate("Nb. d'articles") . ' ' . $count . ' </a></li>
            <li class="page-item disabled"><a class="page-link" href="#" >' . $nbPages . ' ' . translate("pages") . '</a></li>
        </ul>';

    echo Paginator::paginate('modules.php?ModPath=archive-stories&amp;ModStart=archive-stories&amp;start=', '&amp;count=' . $count, $nbPages, $current, 1, $maxcount, $start);
    echo '</div>';
}

if ($SuperCache) {
    $cache_obj->endCachingPage();
}

include("footer.php");
