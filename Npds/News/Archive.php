<?php

namespace Npds\News;

use Npds\Contracts\News\ArchiveInterface;



/**
 * Archive class
 */
class Archive implements ArchiveInterface
{
    /**
     * [$overwrite description]
     *
     * @var [type]
     */
    var $overwrite = 0;

    /**
     * [$defaultperms description]
     *
     * @var [type]
     */
    var $defaultperms   = 0644;


    /**
     * [__construct description]
     *
     * @param   [type] $flags  [$flags description]
     * @param   array          [ description]
     *
     * @return  [type]         [return description]
     */
    public function __construct($flags = array())
    {
        if (isset($flags['overwrite'])) {
            $this->overwrite = $flags['overwrite'];
        }

        if (isset($flags['defaultperms'])) {
            $this->defaultperms = $flags['defaultperms'];
        }
    }

    /**
     * [adddirectories description]
     *
     * @param   [type]  $dirlist  [$dirlist description]
     *
     * @return  [type]            [return description]
     */
    function adddirectories($dirlist)
    {
        $pwd = getcwd();
        @chdir($this->cwd);
        $filelist = array();

        foreach ($dirlist as $current) {
            if (@is_dir($current)) {
                $temp = $this->parsedirectories($current);

                foreach ($temp as $filename) {
                    $filelist[] = $filename;
                }

            } elseif (@file_exists($current)) {
                $filelist[] = $current;
            }
        }

        @chdir($pwd);
        $this->addfiles($filelist);
    }

    /**
     * [parsedirectories description]
     *
     * @param   [type]  $dirname  [$dirname description]
     *
     * @return  [type]            [return description]
     */
    function parsedirectories($dirname)
    {
        $filelist = array();
        $dir = @opendir($dirname);

        while (false !== ($file = readdir($dir))) {
            if ($file == "." || $file == ".." || $file == "default.html" || $file == "index.html")
                continue;

            elseif (@is_dir($dirname . "/" . $file)) {
                if ($this->recursesd != 1) {
                    continue;
                }

                $temp = $this->parsedirectories($dirname . "/" . $file);

                foreach ($temp as $file2) {
                    $filelist[] = $file2;
                }
            } elseif (@file_exists($dirname . "/" . $file)) {
                $filelist[] = $dirname . "/" . $file;
            }
        }
        @closedir($dir);

        return $filelist;
    }

    /**
     * [filewrite description]
     *
     * @param   [type]  $filename  [$filename description]
     * @param   [type]  $perms     [$perms description]
     *
     * @return  [type]             [return description]
     */
    function filewrite($filename, $perms = null)
    {
        if ($this->overwrite != 1 && @file_exists($filename)) {
            return $this->error("Le fichier $filename existe déjà.");
        }

        if (@file_exists($filename)) {
            @unlink($filename);
        }

        $fp = @fopen($filename, "wb");

        if (!fwrite($fp, $this->arc_getdata()))
            return $this->error("Impossible d'écrire les données dans le fichier $filename.");

        @fclose($fp);
        if (!isset($perms))
            $perms = $this->defaultperms;

        @chmod($filename, $perms);
    }

    /**
     * [extractfile description]
     *
     * @param   [type]  $filename  [$filename description]
     *
     * @return  [type]             [return description]
     */
    function extractfile($filename)
    {
        if ($fp = @fopen($filename, "rb")) {
            if (filesize($filename) > 0)
                return $this->extract(fread($fp, filesize($filename)));
            else
                return $this->error("Fichier $filename vide.");

            @fclose($fp);
        } else
            return $this->error("Impossible d'ouvrir le fichier $filename.");
    }

    /**
     * [error description]
     *
     * @param   [type]  $error  [$error description]
     *
     * @return  [type]          [return description]
     */
    function error($error)
    {
        $this->errors[] = $error;

        return 0;
    }

}