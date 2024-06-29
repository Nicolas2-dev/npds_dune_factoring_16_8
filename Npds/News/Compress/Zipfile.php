<?php

namespace Npds\News\Compress;

use Npds\News\Archive;
use Npds\Contracts\News\ZipfileInterface;


/**
 * Zipfile Class
 */
class Zipfile extends Archive implements ZipfileInterface
{
    /**
     * [$cwd description]
     *
     * @var [type]
     */
    var $cwd = "./";

    /**
     * [$comment description]
     *
     * @var [type]
     */
    var $comment = "";

    /**
     * [$level description]
     *
     * @var [type]
     */
    var $level = 9;

    /**
     * [$offset description]
     *
     * @var [type]
     */
    var $offset = 0;

    /**
     * [$recursesd description]
     *
     * @var [type]
     */
    var $recursesd = 1;

    /**
     * [$storepath description]
     *
     * @var [type]
     */
    var $storepath = 1;

    /**
     * [$replacetime description]
     *
     * @var [type]
     */
    var $replacetime = 0;

    /**
     * [$central description]
     *
     * @var [type]
     */
    var $central = array();

    /**
     * [$zipdata description]
     *
     * @var [type]
     */
    var $zipdata = array();

    /**
     * [__construct description]
     *
     * @param   [type] $cwd    [$cwd description]
     * @param   [type] $flags  [$flags description]
     * @param   array          [ description]
     *
     * @return  [type]         [return description]
     */
    public function __construct($cwd = "./", $flags = array())
    {
        $this->cwd = $cwd;
        if (isset($flags['time'])) {
            $this->replacetime = $flags['time'];
        }

        if (isset($flags['recursesd'])) {
            $this->recursesd = $flags['recursesd'];
        }

        if (isset($flags['storepath'])) {
            $this->storepath = $flags['storepath'];
        }

        if (isset($flags['level'])) {
            $this->level = $flags['level'];
        }

        if (isset($flags['comment'])) {
            $this->comment = $flags['comment'];
        }

        $this->archive($flags);
    }

    /**
     * [addfile description]
     *
     * @param   [type] $data      [$data description]
     * @param   [type] $filename  [$filename description]
     * @param   [type] $flags     [$flags description]
     * @param   array             [ description]
     *
     * @return  [type]            [return description]
     */
    function addfile($data, $filename, $flags = array())
    {
        if ($this->storepath != 1) {
            $filename = strstr($filename, "/") ? substr($filename, strrpos($filename, "/") + 1) : $filename;
        } else {
            $filename = preg_replace("/^(\.{1,2}(\/|\\\))+/", "", $filename);
        }

        $mtime = !empty($this->replacetime) ? getdate($this->replacetime) : (isset($flags['time']) ? getdate($flags['time']) : getdate());
        $mtime = preg_replace("/(..){1}(..){1}(..){1}(..){1}/", "\\x\\4\\x\\3\\x\\2\\x\\1", dechex(($mtime['year'] - 1980 << 25) | ($mtime['mon'] << 21) | ($mtime['mday'] << 16) | ($mtime['hours'] << 11) | ($mtime['minutes'] << 5) | ($mtime['seconds'] >> 1)));
        
        eval('$mtime = "' . $mtime . '";');

        $crc32 = crc32($data);
        $normlength = strlen($data);
        $data = gzcompress($data, $this->level);
        $data = substr($data, 2, strlen($data) - 6);
        $complength = strlen($data);
        
        $this->zipdata[] = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00" . $mtime . pack("VVVvv", $crc32, $complength, $normlength, strlen($filename), 0x00) . $filename . $data . pack("VVV", $crc32, $complength, $normlength);
        $this->central[] = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00" . $mtime . pack("VVVvvvvvVV", $crc32, $complength, $normlength, strlen($filename), 0x00, 0x00, 0x00, 0x00, 0x0000, $this->offset) . $filename;
        $this->offset = strlen(implode("", $this->zipdata));
    }

    /**
     * [addfiles description]
     *
     * @param   [type]  $filelist  [$filelist description]
     *
     * @return  [type]             [return description]
     */
    function addfiles($filelist)
    {
        $pwd = getcwd();
        @chdir($this->cwd);

        foreach ($filelist as $current) {
            if (!@file_exists($current)) {
                continue;
            }

            $stat = stat($current);

            if ($fp = @fopen($current, "rb")) {
                if ($stat[7] > 0) {
                    $data = fread($fp, $stat[7]);
                }
                fclose($fp);
            } else {
                $data = "";
            }

            $flags = array('time' => $stat[9]);
            $this->addfile($data, $current, $flags);
        }

        @chdir($pwd);
    }

    /**
     * [arc_getdata description]
     *
     * @return  [type]  [return description]
     */
    function arc_getdata()
    {
        $central = implode("", $this->central);
        $zipdata = implode("", $this->zipdata);
        
        return $zipdata . $central . "\x50\x4b\x05\x06\x00\x00\x00\x00" . pack("vvVVv", sizeof($this->central), sizeof($this->central), strlen($central), strlen($zipdata), strlen($this->comment)) . $this->comment;
    }

    /**
     * [filedownload description]
     *
     * @param   [type]  $filename  [$filename description]
     *
     * @return  [type]             [return description]
     */
    function filedownload($filename)
    {
        @header("Content-Type: application/zip; name=\"$filename\"");
        @header("Content-Disposition: attachment; filename=\"$filename\"");
        @header("Pragma: no-cache");
        @header("Expires: 0");

        print($this->arc_getdata());
    }

}