<?php

use Npds\Support\Facades\Log;


if (stristr($_SERVER['PHP_SELF'], 'sitemap.php')) {
    die();
}

/**
 * [sitemapforum description]
 *
 * @param   [type]  $prio  [$prio description]
 *
 * @return  [type]         [return description]
 */
function sitemapforum($prio)
{
    global $nuke_url;

    $tmp = '';

    $result = sql_query("SELECT forum_id 
                         FROM " . sql_table('forums') . " 
                         WHERE forum_access='0' 
                         ORDER BY forum_id");

    while (list($forum_id) = sql_fetch_row($result)) {
        // Forums
        $tmp .= "<url>\n";
        $tmp .= "<loc>$nuke_url/viewforum.php?forum=$forum_id</loc>\n";
        $tmp .= "<lastmod>" . date("Y-m-d", time()) . "</lastmod>\n";
        $tmp .= "<changefreq>hourly</changefreq>\n";
        $tmp .= "<priority>$prio</priority>\n";
        $tmp .= "</url>\n\n";

        $sub_result = sql_query("SELECT topic_id, topic_time 
                                 FROM " . sql_table('forumtopics') . " 
                                 WHERE forum_id='$forum_id' 
                                 AND topic_status!='2' 
                                 ORDER BY topic_id");

        while (list($topic_id, $topic_time) = sql_fetch_row($sub_result)) {
            // Topics
            $tmp .= "<url>\n";
            $tmp .= "<loc>$nuke_url/viewtopic.php?topic=$topic_id&amp;forum=$forum_id</loc>\n";
            $tmp .= "<lastmod>" . substr($topic_time, 0, 10) . "</lastmod>\n";
            $tmp .= "<changefreq>hourly</changefreq>\n";
            $tmp .= "<priority>$prio</priority>\n";
            $tmp .= "</url>\n\n";
        }
    }

    return $tmp;
}

/**
 * [sitemaparticle description]
 *
 * @param   [type]  $prio  [$prio description]
 *
 * @return  [type]         [return description]
 */
function sitemaparticle($prio)
{
    global $nuke_url;

    $tmp = '';

    $result = sql_query("SELECT sid, time 
                         FROM " . sql_table('stories') . " 
                         WHERE ihome='0' 
                         AND archive='0'
                        ORDER BY sid");

    while (list($sid, $time) = sql_fetch_row($result)) {
        // Articles
        $tmp .= "<url>\n";
        $tmp .= "<loc>$nuke_url/article.php?sid=$sid</loc>\n";
        $tmp .= "<lastmod>" . substr($time, 0, 10) . "</lastmod>\n";
        $tmp .= "<changefreq>daily</changefreq>\n";
        $tmp .= "<priority>$prio</priority>\n";
        $tmp .= "</url>\n\n";
    }

    return $tmp;
}

/**
 * [sitemaprub description]
 *
 * @param   [type]  $prio  [$prio description]
 *
 * @return  [type]         [return description]
 */
function sitemaprub($prio)
{
    global $nuke_url;

    $tmp = '';

    // Sommaire des rubriques
    $tmp .= "<url>\n";
    $tmp .= "<loc>$nuke_url/sections.php</loc>\n";
    $tmp .= "<lastmod>" . date("Y-m-d", time()) . "</lastmod>\n";
    $tmp .= "<changefreq>weekly</changefreq>\n";
    $tmp .= "<priority>$prio</priority>\n";
    $tmp .= "</url>\n\n";

    $result = sql_query("SELECT artid, timestamp 
                         FROM " . sql_table('seccont') . " 
                         WHERE userlevel='0' 
                         ORDER BY artid");

    while (list($artid, $timestamp) = sql_fetch_row($result)) {
        // Rubriques
        $tmp .= "<url>\n";
        $tmp .= "<loc>$nuke_url/sections.php?op=viewarticle&amp;artid=$artid</loc>\n";
        $tmp .= "<lastmod>" . date("Y-m-d", $timestamp) . "</lastmod>\n";
        $tmp .= "<changefreq>weekly</changefreq>\n";
        $tmp .= "<priority>$prio</priority>\n";
        $tmp .= "</url>\n\n";
    }

    return $tmp;
}

/**
 * [sitemapdown description]
 *
 * @param   [type]  $prio  [$prio description]
 *
 * @return  [type]         [return description]
 */
function sitemapdown($prio)
{
    global $nuke_url;

    $tmp = '';

    // Sommaire des downloads
    $tmp .= "<url>\n";
    $tmp .= "<loc>$nuke_url/download.php</loc>\n";
    $tmp .= "<lastmod>" . date("Y-m-d", time()) . "</lastmod>\n";
    $tmp .= "<changefreq>weekly</changefreq>\n";
    $tmp .= "<priority>$prio</priority>\n";
    $tmp .= "</url>\n\n";

    $result = sql_query("SELECT did, ddate 
                         FROM " . sql_table('downloads') . " 
                         WHERE perms='0' 
                         ORDER BY did");

    while (list($did, $ddate) = sql_fetch_row($result)) {
        $tmp .= "<url>\n";
        $tmp .= "<loc>$nuke_url/download.php?op=geninfo&amp;did=$did</loc>\n";
        $tmp .= "<lastmod>$ddate</lastmod>\n";
        $tmp .= "<changefreq>weekly</changefreq>\n";
        $tmp .= "<priority>$prio</priority>\n";
        $tmp .= "</url>\n\n";
    }

    return $tmp;
}

/**
 * [sitemapothers description]
 *
 * @param   [type]  $PAGES  [$PAGES description]
 *
 * @return  [type]          [return description]
 */
function sitemapothers($PAGES)
{
    global $nuke_url;

    $tmp = '';
    foreach ($PAGES as $name => $loc) {
        if (isset($PAGES[$name]['sitemap'])) {
            if (($PAGES[$name]['run'] == "yes") 
            and ($name != "article.php") 
            and ($name != "forum.php") 
            and ($name != "sections.php") 
            and ($name != "download.php")) 
            {
                $tmp .= "<url>\n";
                $tmp .= "<loc>$nuke_url/" . str_replace("&", "&amp;", $name) . "</loc>\n";
                $tmp .= "<lastmod>" . date("Y-m-d", time()) . "</lastmod>\n";
                $tmp .= "<changefreq>daily</changefreq>\n";
                $tmp .= "<priority>" . $PAGES[$name]['sitemap'] . "</priority>\n";
                $tmp .= "</url>\n\n";
            }
        }
    }

    return $tmp;
}

/**
 * [sitemap_create description]
 *
 * @param   [type]  $PAGES     [$PAGES description]
 * @param   [type]  $filename  [$filename description]
 *
 * @return  [type]             [return description]
 */
function sitemap_create($PAGES, $filename)
{

    $ibid  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $ibid .= "<urlset\n";
    $ibid .= "xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"\n";
    $ibid .= "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n";
    $ibid .= "xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\n http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n\n";

    if (isset($PAGES['article.php']['sitemap'])) {
        $ibid .= sitemaparticle($PAGES['article.php']['sitemap']);
    }

    if (isset($PAGES['forum.php']['sitemap'])) {
        $ibid .= sitemapforum($PAGES['forum.php']['sitemap']);
    }

    if (isset($PAGES['sections.php']['sitemap'])) {
        $ibid .= sitemaprub($PAGES['sections.php']['sitemap']);
    }

    if (isset($PAGES['download.php']['sitemap'])) {
        $ibid .= sitemapdown($PAGES['download.php']['sitemap']);
    }

    $ibid .= sitemapothers($PAGES);
    $ibid .= "</urlset>";

    $file = fopen($filename, "w");
    fwrite($file, $ibid);
    fclose($file);

    Log::Ecr_Log("sitemap", "sitemap generated : " . date("H:i:s", time()), "");
}

/* -----------------------------------------*/
// http://www.example.com/cache/sitemap.xml 
$filename = "storage/cache/sitemap.xml";

// delais = 6 heures (21600 secondes)
$refresh = 21600;

global $PAGES;

if (file_exists($filename)) {
    if (time() - filemtime($filename) - $refresh > 0) {
        sitemap_create($PAGES, $filename);
    }
} else {
    sitemap_create($PAGES, $filename);
}
