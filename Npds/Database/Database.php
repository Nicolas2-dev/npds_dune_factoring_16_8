<?php

use Npds\Config\Config;

/**
 * [$sql_nbREQ description]
 *
 * @var [type]
 */
$sql_nbREQ = 0;

/**
 * Connexion plus détaillée ($mysql_p=true => persistente connexion) 
 * Attention : le type de SGBD n'a pas de lien avec le nom de cette fontion
 *
 * @return  [type]  [return description]
 */
function Mysql_Connexion()
{
    if (!$ret_p = sql_connect()) {

        $Titlesitename = "NPDS";

        if (file_exists("storage/meta/meta.php")) {
            include("storage/meta/meta.php");
        }

        if (file_exists("storage/static/database.txt")) {
            include("storage/static/database.txt");
        }

        die();
    }

    $config = Config::get('database');

    mysqli_set_charset($ret_p, $config['charset']);

    return $ret_p;
}

/**
 * Escape string
 *
 * @param   [type]  $arr  [$arr description]
 *
 * @return  [type]        [return description]
 */
function SQL_escape_string($arr)
{
    global $dblink;

    if (function_exists("mysqli_real_escape_string")) {
        @mysqli_real_escape_string($dblink, $arr);
    }
    
    return $arr;
}

/**
 * Connexion
 *
 * @return  [type]  [return description]
 */
function sql_connect()
{
    global $dblink;

    $config = Config::get('database');

    if (($config['mysql_p']) or (!isset($config['mysql_p']))) {
        $dblink = @mysqli_connect('p:' . $config['dbhost'], $config['dbuname'], $config['dbpass']);
    } else {
        $dblink = @mysqli_connect($config['dbhost'], $config['dbuname'], $config['dbpass']);
    }

    if (!$dblink) {
        return false;
    } else {
        if (!@mysqli_select_db($dblink, $config['dbname'])) {
            return false;
        } else {
            return $dblink;
        }
    }
}
 
/**
 * Prefixe la table
 *
 * @param   [type]  $table  [$table description]
 *
 * @return  [type]          [return description]
 */
function sql_table($table = '')
{
    $config = Config::get('database');

    return $config['prefix'] . $table;
}

/**
 * Erreur survenue
 *
 * @return  [type]  [return description]
 */
function sql_error()
{
    global $dblink;

    return mysqli_error($dblink);
}

/**
 * Exécution de requête
 *
 * @param   [type]  $sql  [$sql description]
 *
 * @return  [type]        [return description]
 */
function sql_query($sql)
{
    global $sql_nbREQ, $dblink;

    $sql_nbREQ++;
    if (!$query_id = @mysqli_query($dblink, SQL_escape_string($sql))) {
        return false;
    } else {
        return $query_id;
    }
}
 
/**
 * Tableau Associatif du résultat
 *
 * @param   [type]  $q_id  [$q_id description]
 *
 * @return  [type]         [return description]
 */
function sql_fetch_assoc($q_id = '')
{
    if (empty($q_id)) {
        global $query_id;
        $q_id = $query_id;
    }

    return @mysqli_fetch_assoc($q_id);
}
 
/**
 * Tableau Numérique du résultat
 *
 * @param   [type]  $q_id  [$q_id description]
 *
 * @return  [type]         [return description]
 */
function sql_fetch_row($q_id = '')
{
    if (empty($q_id)) {
        global $query_id;
        $q_id = $query_id;
    }

    return @mysqli_fetch_row($q_id);
}

/**
 * Tableau du résultat
 *
 * @param   [type]  $q_id  [$q_id description]
 *
 * @return  [type]         [return description]
 */
function sql_fetch_array($q_id = '')
{
    if (empty($q_id)) {
        global $query_id;
        $q_id = $query_id;
    }

    return @mysqli_fetch_array($q_id);
}

/**
 * Resultat sous forme d'objet
 *
 * @param   [type]  $q_id  [$q_id description]
 *
 * @return  [type]         [return description]
 */
function sql_fetch_object($q_id = '')
{
    if (empty($q_id)) {
        global $query_id;
        $q_id = $query_id;
    }

    return @mysqli_fetch_object($q_id);
}

/**
 * Nombre de lignes d'un résultat
 *
 * @param   [type]  $q_id  [$q_id description]
 *
 * @return  [type]         [return description]
 */
function sql_num_rows($q_id = '')
{
    if (empty($q_id)) {
        global $query_id;
        $q_id = $query_id;
    }

    return @mysqli_num_rows($q_id);
}

/**
 * Nombre de champs d'une requête
 *
 * @param   [type]  $q_id  [$q_id description]
 *
 * @return  [type]         [return description]
 */
function sql_num_fields($q_id = '')
{
    global $dblink;

    if (empty($q_id)) {
        global $query_id;
        $q_id = $query_id;
    }

    return mysqli_field_count($dblink);
}
 
/**
 * Nombre de lignes affectées par les requêtes de type INSERT, UPDATE et DELETE
 *
 * @return  [type]  [return description]
 */
function sql_affected_rows()
{   global $dblink;

    return @mysqli_affected_rows($dblink);
}
 
/**
 * Le dernier identifiant généré par un champ de type AUTO_INCREMENT
 *
 * @return  [type]  [return description]
 */
function sql_last_id()
{
    global $dblink;

    return @mysqli_insert_id($dblink);
}

/**
 * Lister les tables
 *
 * @param   [type]  $dbnom  [$dbnom description]
 *
 * @return  [type]          [return description]
 */
function sql_list_tables($dbnom = '')
{
    if (empty($dbnom)) {
        global $dbname;
        $dbnom = $dbname;
    }

    return @sql_query("SHOW TABLES FROM $dbnom");
}

/**
 * Controle
 *
 * @return  [type]  [return description]
 */
function sql_select_db()
{
    global $dbname, $dblink;

    if (!@mysqli_select_db($dblink, $dbname)) {
        return false;
    } else {
        return true;
    }
}

/**
 * Libère toute la mémoire et les ressources utilisées par la requête $query_id
 *
 * @param   [type]  $q_id  [$q_id description]
 *
 * @return  [type]         [return description]
 */
function sql_free_result($q_id)
{
    if ($q_id instanceof mysqli_result) {
        return @mysqli_free_result($q_id);
    }
}

/**
 * Ferme la connexion avec la Base de données
 *
 * @return  [type]  [return description]
 */
function sql_close()
{
    global $dblink, $mysql_p;  

    if (!$mysql_p) {
        return @mysqli_close($dblink);
    }
}
