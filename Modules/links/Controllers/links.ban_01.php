<?php



// Le système de bannière
global $banners;
if (($banners) and function_exists("viewbanner")) {
    echo '<p class="text-center">';
    viewbanner();
    echo '</p>';
}
