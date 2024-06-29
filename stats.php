<?php

use Npds\Stat\Stat;
use Npds\Theme\Theme;
use Npds\Utility\Str;


if (!function_exists("Mysql_Connexion")) {
   include("Bootstrap/Boot.php");
}

include("header.php");

$dkn = sql_query("SELECT type, var, count 
                  FROM " . sql_table('counter') . " 
                  ORDER BY type DESC");

while (list($type, $var, $count) = sql_fetch_row($dkn)) {
   if (($type == "total") && ($var == "hits")) {
      $total = $count;
   } elseif ($type == "browser") {
      if ($var == "Netscape") {
         $netscape = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "MSIE") {
         $msie = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "Konqueror") {
         $konqueror = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "Opera") {
         $opera = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "Lynx") {
         $lynx = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "WebTV") {
         $webtv = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "Chrome") {
         $chrome = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "Safari") {
         $safari = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "Bot") {
         $bot = Stat::generate_pourcentage_and_total($count, $total);
      } elseif (($type == "browser") && ($var == "Other")) {
         $b_other = Stat::generate_pourcentage_and_total($count, $total);
      }

   } elseif ($type == "os") {
      if ($var == "Windows") {
         $windows = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "Mac") {
         $mac = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "Linux") {
         $linux = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "FreeBSD") {
         $freebsd = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "SunOS") {
         $sunos = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "IRIX") {
         $irix = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "BeOS") {
         $beos = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "OS/2") {
         $os2 = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "AIX") {
         $aix = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "Android") {
         $andro = Stat::generate_pourcentage_and_total($count, $total);
      } elseif ($var == "iOS") {
         $ios = Stat::generate_pourcentage_and_total($count, $total);
      } elseif (($type == "os") && ($var == "Other")) {
         $os_other = Stat::generate_pourcentage_and_total($count, $total);
      }
   }
}

echo '
   <h2>' . translate("Statistiques") . '</h2>
   <div class="card card-body lead">
      <div>
      ' . translate("Nos visiteurs ont visualisé") . ' <span class="badge bg-secondary">' . Str::wrh($total) . '</span> ' . translate("pages depuis le") . ' ' . $startdate . '
      </div>
   </div>
   <h3 class="my-4">' . translate("Navigateurs web") . '</h3>
   <table data-toggle="table" data-mobile-responsive="true">
      <thead>
         <tr>
            <th data-sortable="true" >' . translate("Navigateurs web") . '</th>
            <th data-sortable="true" data-halign="center" data-align="right" >%</th>
            <th data-align="right" ></th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td><img src="' . Theme::image('stats/explorer.gif', 'stats/explorer.gif') . '" alt="MSIE_ico" loading="lazy"/> MSIE </td>
            <td>
               <div class="text-center small">' . $msie[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $msie[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $msie[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $msie[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/firefox.gif', 'stats/firefox.gif') . '" alt="Mozilla_ico" loading="lazy"/> Mozilla </td>
            <td>
               <div class="text-center small">' . $netscape[1] . ' %</div>
                  <div class="progress bg-light">
                     <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $netscape[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $netscape[1] . '%; height:1rem;"></div>
                  </div>
            </td>
            <td> ' . $netscape[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/opera.gif', 'stats/opera.gif') . '" alt="Opera_ico" loading="lazy"/> Opera </td>
            <td>
               <div class="text-center small">' . $opera[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $opera[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $opera[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $opera[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/chrome.gif', 'stats/chrome.gif') . '" alt="Chrome_ico" loading="lazy"/> Chrome </td>
            <td>
               <div class="text-center small">' . $chrome[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $chrome[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $chrome[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $chrome[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/safari.gif', 'stats/safari.gif') . '" alt="Safari_ico" loading="lazy"/> Safari </td>
            <td>
               <div class="text-center small">' . $safari[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $safari[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $safari[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $safari[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/webtv.gif', 'stats/webtv.gif') . '"  alt="WebTV_ico" loading="lazy"/> WebTV </td>
            <td>
               <div class="text-center small">' . $webtv[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $webtv[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $webtv[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $webtv[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/konqueror.gif', 'stats/konqueror.gif') . '" alt="Konqueror_ico" loading="lazy"/> Konqueror </td>
            <td>
               <div class="text-center small">' . $konqueror[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $konqueror[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $konqueror[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $konqueror[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/lynx.gif', 'stats/lynx.gif') . '" alt="Lynx_ico" loading="lazy"/> Lynx </td>
            <td>
               <div class="text-center small">' . $lynx[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $lynx[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $lynx[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $lynx[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/altavista.gif', 'stats/altavista.gif') . '" alt="' . translate("Moteurs de recherche") . '_ico" /> ' . translate("Moteurs de recherche") . ' </td>
            <td>
               <div class="text-center small">' . $bot[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $bot[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $bot[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $bot[0] . '</td>
         </tr>
         <tr>
            <td><i class="fa fa-question fa-3x align-middle"></i> ' . translate("Inconnu") . ' </td>
            <td>
               <div class="text-center small">' . $b_other[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $b_other[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $b_other[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $b_other[0] . '</td>
         </tr>
      </tbody>
   </table>
   <br />
   <h3 class="my-4">' . translate("Systèmes d'exploitation") . '</h3>
   <table data-toggle="table" data-mobile-responsive="true" >
      <thead>
         <tr>
            <th data-sortable="true" >' . translate("Systèmes d'exploitation") . '</th>
            <th data-sortable="true" data-halign="center" data-align="right">%</th>
            <th data-align="right"></th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td ><img src="' . Theme::image('stats/windows.gif', 'stats/windows.gif') . '"  alt="Windows" loading="lazy"/>&nbsp;Windows</td>
            <td>
               <div class="text-center small">' . $windows[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $windows[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $windows[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $windows[0] . '</td>
         </tr>
         <tr>
            <td ><img src="' . Theme::image('stats/linux.gif', 'stats/linux.gif') . '"  alt="Linux" loading="lazy"/>&nbsp;Linux</td>
            <td>
               <div class="text-center small">' . $linux[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $linux[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $linux[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $linux[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/mac.gif', 'stats/mac.gif') . '"  alt="Mac/PPC" loading="lazy"/>&nbsp;Mac/PPC</td>
            <td>
               <div class="text-center small">' . $mac[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $mac[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $mac[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $mac[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/bsd.gif', 'stats/bsd.gif') . '"  alt="FreeBSD" loading="lazy"/>&nbsp;FreeBSD</td>
            <td>
               <div class="text-center small">' . $freebsd[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $freebsd[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $freebsd[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $freebsd[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/sun.gif', 'stats/sun.gif') . '"  alt="SunOS" loading="lazy"/>&nbsp;SunOS</td>
            <td>
               <div class="text-center small">' . $sunos[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $sunos[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $sunos[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $sunos[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/irix.gif', 'stats/irix.gif') . '"  alt="IRIX" loading="lazy"/>&nbsp;IRIX</td>
            <td>
               <div class="text-center small">' . $irix[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $irix[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $irix[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $irix[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/be.gif', 'stats/be.gif') . '" alt="BeOS" loading="lazy"/>&nbsp;BeOS</td>
            <td>
               <div class="text-center small">' . $beos[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $beos[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $beos[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $beos[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/os2.gif', 'stats/os2.gif') . '" alt="OS/2" loading="lazy"/>&nbsp;OS/2</td>
            <td>
               <div class="text-center small">' . $os2[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $os2[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $os2[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $os2[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/aix.gif', 'stats/aix.gif') . '" alt="AIX" loading="lazy"/>&nbsp;AIX</td>
            <td>
               <div class="text-center small">' . $aix[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $aix[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $aix[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $aix[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/android.gif', 'stats/android.gif') . '" alt="Android" loading="lazy"/>&nbsp;Android</td>
            <td>
               <div class="text-center small">' . $andro[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $andro[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $andro[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $andro[0] . '</td>
         </tr>
         <tr>
            <td><img src="' . Theme::image('stats/ios.gif', 'stats/ios.gif') . '" alt="Ios" loading="lazy"/> Ios</td>
            <td>
               <div class="text-center small">' . $ios[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $ios[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $ios[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $ios[0] . '</td>
         </tr>
         <tr>
            <td><i class="fa fa-question fa-3x align-middle"></i>&nbsp;' . translate("Inconnu") . '</td>
            <td>
               <div class="text-center small">' . $os_other[1] . ' %</div>
               <div class="progress bg-light">
                  <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $os_other[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $os_other[1] . '%; height:1rem;"></div>
               </div>
            </td>
            <td>' . $os_other[0] . '</td>
         </tr>
      </tbody>
   </table>
   ' . Theme::theme_distinct() . '
   <h3 class="my-4">' . translate("Statistiques diverses") . '</h3>
   <ul class="list-group">
      <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-user fa-2x text-body-secondary me-1"></i>' . translate("Utilisateurs enregistrés") . ' <span class="badge bg-secondary ms-auto">' . Str::wrh(Stat::stat_user()) . ' </span></li>
      <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-users fa-2x text-body-secondary me-1"></i>' . translate("Groupe") . ' <span class="badge bg-secondary ms-auto">' . Str::wrh(Stat::stat_groupes()) . ' </span></li>
      <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-user-edit fa-2x text-body-secondary me-1"></i>' . translate("Auteurs actifs") . ' <span class="badge bg-secondary ms-auto">' . Str::wrh(Stat::stat_authors()) . ' </span></li>
      <li class="list-group-item d-flex justify-content-start align-items-center"><img class="me-1" src="' . Theme::image('stats/postnew.png', 'admin/postnew.png') . '" alt="" loading="lazy"/>' . translate("Articles publiés") . ' <span class="badge bg-secondary ms-auto">' . Str::wrh(Stat::stat_stories()) . ' </span></li>
      <li class="list-group-item d-flex justify-content-start align-items-center"><img class="me-1" src="' . Theme::image('stats/topicsman.png', 'admin/topicsman.png') . '" alt="" loading="lazy"/>' . translate("Sujets actifs") . ' <span class="badge bg-secondary ms-auto">' . Str::wrh(Stat::stat_topics()) . ' </span></li>
      <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-comments fa-2x text-body-secondary me-1"></i>' . translate("Commentaires") . ' <span class="badge bg-secondary ms-auto">' . Str::wrh(Stat::stat_posts()) . ' </span></li>
      <li class="list-group-item d-flex justify-content-start align-items-center"><img class="me-1" src="' . Theme::image('stats/sections.png', 'admin/sections.png') . '" alt="" loading="lazy"/>' . translate("Rubriques spéciales") . ' <span class="badge bg-secondary ms-auto">' . Str::wrh(Stat::stat_sections()) . ' </span></li>
      <li class="list-group-item d-flex justify-content-start align-items-center"><img class="me-1" src="' . Theme::image('stats/sections.png', 'admin/sections.png') . '" alt="" loading="lazy"/>' . translate("Articles présents dans les rubriques") . ' <span class="badge bg-secondary ms-auto">' . Str::wrh(Stat::stat_seccont()) . ' </span></li>
      <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-link fa-2x text-body-secondary me-1"></i>' . translate("Liens présents dans la rubrique des liens web") . ' <span class="badge bg-secondary ms-auto">' . Str::wrh(Stat::stat_links()) . ' </span></li>
      <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-link fa-2x text-body-secondary me-1"></i>' . translate("Catégories dans la rubrique des liens web") . ' <span class="badge bg-secondary ms-auto">' . Str::wrh(Stat::stat_total_link_categories()) . ' </span></li>
      <li class="list-group-item d-flex justify-content-start align-items-center"><img class="me-1" src="' . Theme::image('stats/submissions.png', 'admin/submissions.png') . '"  alt="" />' . translate("Article en attente d'édition") . ' <span class="badge bg-secondary ms-auto">' . Str::wrh(Stat::stat_queue()) . ' </span></li>
      <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-cogs fa-2x text-body-secondary me-1"></i>Version Num <span class="badge bg-danger ms-auto">' . $Version_Num . '</span></li>
      <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-cogs fa-2x text-body-secondary me-1"></i>Version Id <span class="badge bg-danger ms-auto">' . $Version_Id . '</span></li>
      <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-cogs fa-2x text-body-secondary me-1"></i>Version Sub <span class="badge bg-danger ms-auto">' . $Version_Sub . '</span></li>
   </ul>
   <br />
   <p class="text-center"><a href="http://www.npds.org" >http://www.npds.org</a> - French Portal Generator Gnu/Gpl Licence</p><br />';

include("footer.php");
