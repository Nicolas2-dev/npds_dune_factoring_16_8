<?php

/**
 * [win_upload description]
 *
 * @param   [type]  $apli     [$apli description]
 * @param   [type]  $IdPost   [$IdPost description]
 * @param   [type]  $IdForum  [$IdForum description]
 * @param   [type]  $IdTopic  [$IdTopic description]
 * @param   [type]  $typeL    [$typeL description]
 *
 * @return  [type]            [return description]
 */
function win_upload($apli, $IdPost, $IdForum, $IdTopic, $typeL)
{
    if ($typeL == 'win') {
        echo "
        <script type=\"text/javascript\">
            //<![CDATA[
                window.open('modules.php?ModPath=upload&ModStart=include_forum/upload_forum2&apli=$apli&IdPost=$IdPost&IdForum=$IdForum&IdTopic=$IdTopic','wtmpForum', 'menubar=no,location=no,directories=no,status=no,copyhistory=no,toolbar=no,scrollbars=yes,resizable=yes, width=575, height=480');
            //]]>
        </script>";
    } else {
        return ("'modules.php?ModPath=upload&ModStart=include_forum/upload_forum2&apli=$apli&IdPost=$IdPost&IdForum=$IdForum&IdTopic=$IdTopic','wtmpForum', 'menubar=no,location=no,directories=no,status=no,copyhistory=no,toolbar=no,scrollbars=yes,resizable=yes, width=575, height=480'");
    }
}
