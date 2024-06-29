<?php


namespace Npds\Boot\Bootstrap;

use Npds\Support\Facades\Request;


class InitLanguage
{

    /**
     * [bootstrap description]
     *
     * @return  [type]  [return description]
     */
    public function bootstrap()
    {
        global $language, $languageslist;

        // Multi-language
        $local_path = '';

        // settype($user_language, 'string');

        if (isset($module_mark)) {
            $local_path = '../../';
        }

        if (file_exists($local_path . 'storage/language/language.php')) {
            include($local_path . 'storage/language/language.php');
        } else {
            include($local_path . 'manuels/list.php');
        }

        $choice_user_language = Request::input('choice_user_language');

        if (isset($choice_user_language)) {
            if ($choice_user_language != '') {
                
                $user_cook_duration = config('user_cook_duration');

                if ($user_cook_duration <= 0) {
                    $user_cook_duration = 1;
                }

                $timeX = time() + (3600 * $user_cook_duration);

                if ((stristr($languageslist, $choice_user_language)) and ($choice_user_language != ' ')) {
                    
                    setcookie('user_language', $choice_user_language, $timeX);
                    
                    $user_language = $choice_user_language;
                }
            }
        }

        $language = config('language');

        if (config('multi_langue')) {
            if (($user_language != '') and ($user_language != " ")) {
                $tmpML = stristr($languageslist, $user_language);
                $tmpML = explode(' ', $tmpML);

                if ($tmpML[0])
                    $language = $tmpML[0];
                    
            }
        }

        $this->loadLanguage($language);
    }

    /**
     * [loadLanguage description]
     *
     * @param   [type]  $language  [$language description]
     *
     * @return  [type]             [return description]
     */
    private function loadLanguage($language)
    {
        include("Language/lang-$language.php");
    }
}
