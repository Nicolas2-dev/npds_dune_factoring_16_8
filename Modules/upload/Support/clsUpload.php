<?php


class Upload
{
    
    /**
     * [$maxupload_size description]
     *
     * @var [type]
     */
    var $maxupload_size;

    /**
     * [$errors description]
     *
     * @var [type]
     */
    var $errors;

    /**
     * [$isPosted description]
     *
     * @var [type]
     */
    var $isPosted;

    /**
     * [$HTTP_POST_FILES description]
     *
     * @var [type]
     */
    var $HTTP_POST_FILES;


    /**
     * [__construct description]
     *
     * @return  [type]  [return description]
     */
    public function __construct()
    {
        global $HTTP_POST_FILES, $_FILES;

        if (!empty($HTTP_POST_FILES)) {
            $fic = $HTTP_POST_FILES;
        } else {
            $fic = $_FILES;
        }

        $this->HTTP_POST_FILES = $fic;

        if (empty($fic)) {
            $this->isPosted = false;
        } else {
            $this->isPosted = true;
        }
    }

    /**
     * [saveAs description]
     *
     * @param   [type]  $filename   [$filename description]
     * @param   [type]  $directory  [$directory description]
     * @param   [type]  $field      [$field description]
     * @param   [type]  $overwrite  [$overwrite description]
     * @param   [type]  $mode       [$mode description]
     *
     * @return  [type]              [return description]
     */
    function saveAs($filename, $directory, $field, $overwrite, $mode = 0766)
    {
        if ($this->isPosted) {
            if ($this->HTTP_POST_FILES[$field]['size'] < $this->maxupload_size && $this->HTTP_POST_FILES[$field]['size'] > 0) {

                $noerrors = true;
                $tempName = $this->HTTP_POST_FILES[$field]['tmp_name'];
                $all      = $directory . $filename;

                if (file_exists($all)) {
                    if ($overwrite) {
                        @unlink($all) || $noerrors = false;
                        $this->errors  = upload_translate("Erreur de téléchargement du fichier - fichier non sauvegardé.");

                        @move_uploaded_file($tempName, $all) || $noerrors = false;
                        $this->errors .= upload_translate("Erreur de téléchargement du fichier - fichier non sauvegardé.");
                        @chmod($all, $mode);
                    }
                } else {
                    @move_uploaded_file($tempName, $all) || $noerrors = false;
                    $this->errors  = upload_translate("Erreur de téléchargement du fichier - fichier non sauvegardé.");
                    @chmod($all, $mode);
                }

                return $noerrors;
            } elseif ($this->HTTP_POST_FILES[$field]['size'] > $this->maxupload_size) {
                $this->errors = upload_translate("La taille de ce fichier excède la taille maximum autorisée") . " => " . number_format(($this->maxupload_size / 1024), 2) . " Kbs";
                
                return false;
            } elseif ($this->HTTP_POST_FILES[$field]['size'] == 0) {
                $this->errors = upload_translate("Erreur de téléchargement du fichier - fichier non sauvegardé.");
                
                return false;
            }
        }
    }

    /**
     * [getFilename description]
     *
     * @param   [type]  $field  [$field description]
     *
     * @return  [type]          [return description]
     */
    function getFilename($field)
    {
        return $this->HTTP_POST_FILES[$field]['name'];
    }

    /**
     * [getFileMimeType description]
     *
     * @param   [type]  $field  [$field description]
     *
     * @return  [type]          [return description]
     */
    function getFileMimeType($field)
    {
        return $this->HTTP_POST_FILES[$field]['type'];
    }

    /**
     * [getFileSize description]
     *
     * @param   [type]  $field  [$field description]
     *
     * @return  [type]          [return description]
     */
    function getFileSize($field)
    {
        return $this->HTTP_POST_FILES[$field]['size'];
    }
    
}
