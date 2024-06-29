<?php

use Npds\Block\Block;
use Npds\Language\Language;
use Npds\Metalang\Metalang;
use Npds\Support\Facades\Theme;

/************************************************************************/
/* Fermeture ou ouverture et fermeture according with $pdst :           */
/*       col_LB +|| col_princ +|| col_RB                                */
/* Fermeture : div > div"#corps"> $ContainerGlobal>                     */
/*                    ouverts dans le Header.php                        */
/* =====================================================================*/

$moreclass = 'col-12';

global $pdst;
switch ($pdst) {
    case '-1':
    case '3':
    case '5':
        echo '
                </div>
            </div>
        </div>';
        break;

    case '1':
    case '2':
        echo '</div>';

        Theme::colsyst('#col_RB');

        echo '
            <div id="col_RB" class="collapse show col-lg-3 ">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::rightblocks($moreclass);

            echo '
                    </div>
                </div>
            </div>
        </div>';
        break;

    case '4':
        echo '</div>';

        Theme::colsyst('#col_LB');

        echo '
            <div id="col_LB" class="collapse show col-lg-3">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::leftblocks($moreclass);

        echo '
            </div>
        </div>';

        Theme::colsyst('#col_RB');

        echo '
            <div id="col_RB" class="collapse show col-lg-3">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::rightblocks($moreclass);
        echo '
                    </div>
                </div>
            </div>
        </div>';
        break;

    case '6':
        echo '</div>';

        Theme::colsyst('#col_LB');

        echo '
        <div id="col_LB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::leftblocks($moreclass);
        echo '
                </div>
            </div>
        </div>
        </div>';
        break;

    default:
        echo '
                </div>
            </div>
        </div>';
        break;
}

// ContainerGlobal permet de transmettre · Theme-Dynamic un élément de personnalisation après
// le chargement de footer.html / Si vide alors rien de plus n'est affiché par TD
$ContainerGlobal = '</div>';

global $theme;

$rep = false;

// settype($ContainerGlobal, 'string');

if (file_exists("themes/" . $theme . "/html/footer.html")) {
    $rep = $theme;
} elseif (file_exists("themes/default/html/footer.html")) {
    $rep = "default";
} else {
    echo "footer.html manquant / not find !<br />";
    die();
}

if ($rep) {
    ob_start();
        include("themes/" . $rep . "/html/footer.html");
        $Xcontent = ob_get_contents();
    ob_end_clean();

    if ($ContainerGlobal) {
        $Xcontent .= $ContainerGlobal;
    }

    echo Metalang::meta_lang(Language::aff_langue($Xcontent));
}
