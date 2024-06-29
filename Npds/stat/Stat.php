<?php

namespace Npds\Stat;

use Npds\Support\Facades\Str;
use Npds\Contracts\Stat\StatInterface;


/**
 * Stat class
 */
class Stat implements StatInterface
{
    /**
     * [$instance description]
     *
     * @var [type]
     */
    protected static $instance;


    /**
     * [getInstance description]
     *
     * @return  [type]  [return description]
     */
    public static function getInstance()
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    /**
     * [generate_pourcentage_and_total description]
     *
     * @param   [type]  $count  [$count description]
     * @param   [type]  $total  [$total description]
     *
     * @return  [type]          [return description]
     */
    public static function generate_pourcentage_and_total($count, $total)
    {
        $tab[] = Str::wrh($count);
        $tab[] = substr(sprintf('%f', 100 * $count / $total), 0, 5);

        return $tab;
    }

    /**
     * Retourne un tableau contenant les nombres pour les statistiques du site (stats.php)
     *
     * @return  [type]  [return description]
     */
    public static function req_stat()
    {
        // Les membres
        $xtab[0] = static::stat_user();

        // Les Nouvelles (News)
        $xtab[1] = static::stat_stories();

        // Les Critiques (Reviews))
        $xtab[2] = static::stat_reviews();

        // Les Forums
        $xtab[3] = static::stat_forum();

        // Les Sujets (topics)
        $xtab[4] = static::stat_topics();

        // Nombre de pages vues
        $xtab[5] = static::stat_page_vue();

        return $xtab;
    }    

    /**
     * [stat_page_vue description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_page_vue() 
    {
        $result = sql_query("SELECT count FROM " . sql_table('counter') . " WHERE type='total'");

        if ($result) {
            list($totalz) = sql_fetch_row($result);

        }

        $totalz++;
        return $totalz++;
    }

    /**
     * [stat_user description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_user() 
    {
        $result = sql_query("SELECT uid FROM " . sql_table('users'));
        return $result ? sql_num_rows($result) - 1 : 0;
    }

    /**
     * [stat_forum description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_forum() 
    {
        $result = sql_query("SELECT forum_id FROM " . sql_table('forums'));
        return $result ? sql_num_rows($result) : '0';
    }

    /**
     * [stat_groupes description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_groupes() 
    {
        $result = sql_query("SELECT groupe_id FROM " . sql_table('groupes'));
        return $result ? sql_num_rows($result) : 0;
    }

    /**
     * [stat_reviews description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_reviews() 
    {
        $result = sql_query("SELECT id FROM " . sql_table('reviews'));
        return $result ? sql_num_rows($result) : '0';
    }

    /**
     * [stat_stories description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_stories() 
    {
        $result = sql_query("SELECT sid FROM " . sql_table('stories'));
        return $result ? sql_num_rows($result) : 0;
    }

    /**
     * [stat_authors description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_authors() 
    {
        $result = sql_query("SELECT aid FROM " . sql_table('authors'));
        return $result ? sql_num_rows($result) : 0;
    }

    /**
     * [stat_posts description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_posts() 
    {
        $result = sql_query("SELECT post_id FROM " . sql_table('posts') . " WHERE forum_id<0");
        return $result ? sql_num_rows($result) : 0;
    }

    /**
     * [stat_sections description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_sections() 
    {
        $result = sql_query("SELECT secid FROM " . sql_table('sections'));
        return $result ? sql_num_rows($result) : 0;
    }

    /**
     * [stat_seccont description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_seccont() 
    {
        $result = sql_query("SELECT artid FROM " . sql_table('seccont'));
        return $result ? sql_num_rows($result) : 0;
    }   

    /**
     * [stat_queue description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_queue() 
    {
        $result = sql_query("SELECT qid FROM " . sql_table('queue'));
        return $result ? sql_num_rows($result) : 0;
    }

    /**
     * [stat_topics description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_topics() 
    {
        $result = sql_query("SELECT topicid FROM " . sql_table('topics'));
        return $result ? sql_num_rows($result) : 0;
    }

    /**
     * [stat_links description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_links() 
    {
        $result = sql_query("SELECT lid FROM " . sql_table('links_links'));
        return $result ? sql_num_rows($result) : 0;
    }

    /**
     * [stat_links_categories description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_links_categories() 
    {
        $result = sql_query("SELECT cid FROM " . sql_table('links_categories'));
        return $result ? sql_num_rows($result) : 0;
    }

    /**
     * [stat_links_subcategories description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_links_subcategories() 
    {
        $result = sql_query("SELECT sid FROM " . sql_table('links_subcategories'));
        return $result ? sql_num_rows($result) : 0;
    }

    /**
     * [stat_total_link_categories description]
     *
     * @return  [type]  [return description]
     */
    public static function stat_total_link_categories() 
    {
        return  static::stat_links_categories() + static::stat_links_subcategories();
    }

}