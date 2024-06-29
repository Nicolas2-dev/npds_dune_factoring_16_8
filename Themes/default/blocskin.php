<?php


global $Default_Skin, $Default_Theme;
$skinOn = '';
if ($user) {
    $user2 = base64_decode($user);
    $cookie = explode(':', $user2);
    $ibix = explode('+', urldecode($cookie[9]));
    $skinOn = substr($ibix[0], -3) != '_sk' ? '' : $ibix[1];
} else
    $skinOn = substr($Default_Theme, -3) != '_sk' ? '' : $Default_Skin;

$content = '';
if ($skinOn != '')
    $content .= '
<div class="form-floating">
   <select class="form-select" id="blocskinchoice"><option>' . $skinOn . '</option></select>
   <label for="blocskinchoice">Choisir un skin</label>
</div>
<script type="text/javascript">
   //<![CDATA[
   fetch("api/skins.json")
      .then(response => response.json())
      .then(data => load(data));
   function load(data) {
      const skins = data.skins;
      const select = document.querySelector("#blocskinchoice");
      skins.forEach((value, index) => {
         const option = document.createElement("option");
         option.value = index;
         option.textContent = value.name;
         select.append(option);
      });
      select.addEventListener("change", (e) => {
         const skin = skins[e.target.value];
         if (skin) {
            document.querySelector("#bsth").setAttribute("href", skin.css);
            document.querySelector("#bsthxtra").setAttribute("href", skin.cssxtra);
         }
      });
      const changeEvent = new Event("change");
      select.dispatchEvent(changeEvent);
   }
   //]]>
</script>';
else
    $content .= '<div class="alert alert-danger">Thème non skinable</div>';
