<?php


function win_upload($typeL)
{
    if ($typeL == 'win') {
        echo "
        <script type=\"text/javascript\">
            //<![CDATA[
                window.open('modules.php?ModPath=f-manager&ModStart=f-manager&FmaRep=minisite-ges','wtmpMinisite', 'menubar=no,location=no,directories=no,status=no,copyhistory=no,toolbar=no,scrollbars=yes,resizable=yes, width=780, height=500');
            //]]>
        </script>";
    } else {
        return ("'modules.php?ModPath=f-manager&ModStart=f-manager&FmaRep=minisite-ges','wtmpMinisite', 'menubar=no,location=no,directories=no,status=no,copyhistory=no,toolbar=no,scrollbars=yes,resizable=yes, width=780, height=500'");
    }
}
