<?php

//==> core required dont remove
echo '
      <script type="text/javascript" src="assets/shared/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
      <script type="text/javascript" src="assets/shared/bootstrap-table/dist/bootstrap-table.min.js"></script>
      <script type="text/javascript" src="assets/shared/bootstrap-table/dist/locale/bootstrap-table-'.Language::language_iso(1,"-",1).'.min.js" async="async"></script>
      <script type="text/javascript" src="assets/shared/bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile.min.js" async="async"></script>
      <script type="text/javascript" src="assets/shared/bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js" async="async"></script>
      <script type="text/javascript" src="assets/shared/tableExport.jquery.plugin-master/tableExport.js" async="async"></script>
      <script type="text/javascript" src="assets/js/js.cookie.js" async="async"></script>
      <script type="text/javascript" src="assets/js/jquery-ui.min.js" ></script>
      <script type="text/javascript" src="assets/js/bootbox.min.js" async="async"></script>
      <script type="text/javascript" src="assets/js/prism.js"></script>
      <script type="text/javascript">
         //<![CDATA[
            (tarteaucitron.job = tarteaucitron.job || []).push("vimeo");
            (tarteaucitron.job = tarteaucitron.job || []).push("youtube");
            (tarteaucitron.job = tarteaucitron.job || []).push("dailymotion");
            //tarteaucitron.user.gtagUa = ""; /*uncomment the line and add your gtag*/
            //tarteaucitron.user.gtagMore = function () { /* uncomment the line add here your optionnal gtag() */ };
            //(tarteaucitron.job = tarteaucitron.job || []).push("gtag");
         //]]
      </script>';
// <== core

// ==> page-time pseudo-module : If you want to show the time used to generate each page uncomment those lines
   /*
   $time = round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],5); // php => 5.4 
   echo '
   <span class="small" id="build_time"><i class="bi bi-clock-history me-1"></i>'.Language::aff_langue('[french]Temps :[/french][english]Time:[/english][german]Zeit in Sekunden[/german][spanish]Tiempo en segundos :[/spanish][chinese]&#x5728;&#x51E0;&#x79D2;&#x949F;&#x7684;&#x65F6;&#x95F4; :[/chinese] '.$time.' [french]seconde(s)[/french][english]second(s)[/english]').'</span>';
   */
// <== page-time pseudo-module

echo '
   </footer>
</div>
<script type="text/javascript" src="assets/js/npds_adapt.js"></script>';
