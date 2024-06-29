<?php

use Npds\Config\Config;
use Npds\Support\Facades\Date;
use Npds\Support\Facades\News;
use Npds\Support\Facades\Request;
use Npds\Support\Facades\Language;
use Npds\Support\Facades\Metalang;


include("Bootstrap/Boot.php");

/**
 * [fab_feed description]
 *
 * @param   [type]  $type      [$type description]
 * @param   [type]  $filename  [$filename description]
 * @param   [type]  $timeout   [$timeout description]
 *
 * @return  [type]             [return description]
 */
function fab_feed($type, $filename, $timeout)
{
    include("Npds/Feed/feedcreator.class.php");

    $rss = new UniversalFeedCreator();
    $rss->useCached($type, $filename, $timeout);

    $rss->title                     = Config::get('npds.sitename');
    $rss->description               = Config::get('npds.slogan');
    $rss->descriptionTruncSize      = 250;
    $rss->descriptionHtmlSyndicated = true;

    $rss->link                      = site_url();
    $rss->syndicationURL            = site_url('backend.php?op=' . $type);

    $image                          = new FeedImage();
    $image->title                   = Config::get('npds.sitename');
    $image->url                     = Config::get('backend.image');
    $image->link                    = site_url();
    $image->description             = Config::get('backend.title');
    $image->width                   = Config::get('backend.width');
    $image->height                  = Config::get('backend.height');
    $rss->image                     = $image;

    $storyhome = Config::get('storie.storyhome');

    $xtab = News::news_aff('index', "WHERE ihome='0' AND archive='0'", $storyhome, '');

    $story_limit = 0;
    while (($story_limit < $storyhome) and ($story_limit < sizeof($xtab))) {

        list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[$story_limit];

        $story_limit++;

        $item                               = new FeedItem();
        $item->title                        = Language::preview_local_langue(Config::get('backend.language'), str_replace('&quot;', '\"', $title));
        $item->link                         = site_url('article.php?sid='. $sid);
        $item->description                  = Metalang::meta_lang(Language::preview_local_langue(Config::get('backend.language'), $hometext));
        $item->descriptionHtmlSyndicated    = true;
        $item->date                         = Date::convertdateTOtimestamp($time) + ((int) Config::get('date.gmt') * 3600);
        $item->source                       = site_url();
        $item->author                       = $aid;

        $rss->addItem($item);
    }

    echo $rss->saveFeed($type, $filename);
}

// Format : RSS0.91, RSS1.0, RSS2.0, MBOX, OPML, ATOM
switch (Request::query('op')) 
{
    case 'MBOX':
        fab_feed('MBOX', 'storage/feed/MBOX-feed', 3600);
        break;

    case 'OPML':
        fab_feed('OPML', 'storage/feed/OPML-feed.xml', 3600);
        break;

    case 'ATOM':
        fab_feed('ATOM', 'storage/feed/ATOM-feed.xml', 3600);
        break;

    case 'RSS1.0':
        fab_feed('RSS1.0', 'storage/feed/RSS1.0-feed.xml', 3600);
        break;

    case 'RSS2.0':
        fab_feed('RSS2.0', 'storage/feed/RSS2.0-feed.xml', 3600);
        break;

    case 'RSS0.91':
        fab_feed('RSS0.91', 'storage/feed/RSS0.91-feed.xml', 3600);
        break;

    default:
        fab_feed('RSS1.0', 'storage/feed/RSS1.0-feed.xml', 3600);
        break;
}
