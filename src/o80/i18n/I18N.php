<?php
namespace o80\i18n;

/**
 * This class manage internationalization of an application.<br/>
 * <u>Usage :</u>
 * <pre>
 * $i18n = I18N::instance();
 * $i18n->setPath(__DIR__ . '/langs');
 * $i18n->get('Welcome');
 * </pre>
 *
 * @package o80
 */
class I18N {

    private static $instance;

    /**
     * @var string The default lang code
     */
    private $defaultLang = null;

    /**
     * @var array The lang dictionary
     */
    private $dict = null;

    /**
     * @var string The path of langs directory
     */
    private $path = null;

    /**
     * @var string The lang code of loaded lang
     */
    private $loadedLang = null;

    /**
     * @var Provider The lang provider
     */
    private $dictProvider = null;

    /**
     * @var bool Use or not the lang defined in $_GET['lang']
     */
    private $useLangFromGET = true;

    /**
     * @var array All available local code sets
     */
    private $codeSets = array(
        'af_ZA.UTF-8', 'sq_AL.UTF-8', 'ar_SA.UTF-8', 'eu_ES.UTF-8', 'be_BY.UTF-8', 'bs_BA.UTF-8', 'bg_BG.UTF-8',
        'ca_ES.UTF-8', 'hr_HR.UTF-8', 'zh_CN.UTF-8', 'zh_TW.UTF-8', 'cs_CZ.UTF-8', 'da_DK.UTF-8', 'nl_NL.UTF-8',
        'en.UTF-8', 'et_EE.UTF-8', 'fa_IR.UTF-8', 'ph_PH.UTF-8', 'fi_FI.UTF-8', 'fr_FR.UTF-8', 'fr_CH.UTF-8',
        'fr_BE.UTF-8', 'fr_CA.UTF-8', 'ga.UTF-8', 'gl_ES.UTF-8', 'ka_GE.UTF-8', 'de_DE.UTF-8', 'de_DE.UTF-8',
        'el_GR.UTF-8', 'gu.UTF-8', 'he_IL.utf8', 'hi_IN.UTF-8', 'hu_HU.UTF-8', 'is_IS.UTF-8', 'id_ID.UTF-8',
        'it_IT.UTF-8', 'ja_JP.UTF-8', 'kn_IN.UTF-8', 'km_KH.UTF-8', 'ko_KR.UTF-8', 'lo_LA.UTF-8', 'lt_LT.UTF-8',
        'lat.UTF-8', 'ml_IN.UTF-8', 'ms_MY.UTF-8', 'mi_NZ.UTF-8', 'mi_NZ.UTF-8', 'mn.UTF-8', 'no_NO.UTF-8',
        'no_NO.UTF-8', 'nn_NO.UTF-8', 'pl.UTF-8', 'pt_PT.UTF-8', 'pt_BR.UTF-8', 'ro_RO.UTF-8', 'ru_RU.UTF-8',
        'mi_NZ.UTF-8', 'sr_CS.UTF-8', 'sk_SK.UTF-8', 'sl_SI.UTF-8', 'so_SO.UTF-8', 'es_ES.UTF-8', 'sv_SE.UTF-8',
        'tl.UTF-8', 'ta_IN.UTF-8', 'th_TH.UTF-8', 'mi_NZ.UTF-8', 'tr_TR.UTF-8', 'uk_UA.UTF-8', 'vi_VN.UTF-8'
    );

    /**
     * Array of pluralRules
     * @see https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals
     * @var array 
     */
    private $pluralRules = array();

    /**
     * Plural rule
     * @var int 
     */
    private $pluralRule = 1;
    
    public function __construct($dictProvider = null) {
        $this->dictProvider = $dictProvider != null ? $dictProvider : new JsonProvider();
        $this->pluralRules = array(
            function ($number) { // Plural rule 0 (Chinese)
                return 0;
            },
            function ($number) { // Plural rule 1 (English)
                return $number == 1 ? 0 : 1;
            },
            function ($number) { // Plural rule 2 (French)
                return $number <= 1 ? 0 : 1;
            },
            function ($number) { // Plural rule 3 (Latvian)
                return $number == 0 ? 0 : ($number % 100 != 11 && $number % 10 == 1 ? 1 : 2);
            },
            function ($number) { // Plural rule 4 (Scottish Gaelic)
                return $number == 1 || $number == 11 ? 0 : ($number == 2 || $number == 12 ? 1 : (($number >= 3 && $number <= 10) || ($number >= 13 && $number <= 19) ? 2 : 3));
            },
            function ($number) { // Plural rule 5 (Romanian)
                return $number == 1 ? 0 : ($number == 0 || ($number < 20 && $number % 100 < 20) ? 1 : 2);
            },
            function ($number) { // Plural rule 6 (Lithuanian)
                return $number != 11 && $number % 10 == 1 ? 0 : ($number % 10 == 0 || ($number % 100 >= 11 && $number % 100 <= 19) ? 1 : 2);
            },
            function ($number) { // Plural rule 7 (Russian)
                return $number % 10 == 1 && $number != 11 ? 0 : (($number % 10 >= 2 && $number % 10 <= 4) && ($number != 12 && $number != 14) ? 1 : 2);
            },
            function ($number) { // Plural rule 8 (Slovak)
                return $number == 1 ? 0 : ($number >= 2 && $number <= 4 ? 1 : 2);
            },
            function ($number) { // Plural rule 9 (Polish)
                return $number == 1 ? 0 : (($number % 10 >= 2 && $number % 10 <= 4) && ($number != 12 && $number != 14) ? 1 : 2);
            },
            function ($number) { // Plural rule 10 (Slovenian)
                return $number % 100 == 1 ? 0 : ($number % 100 == 2 ? 1 : ($number % 100 == 3 || $number % 100 == 4 ? 2 : 3));
            },
            function ($number) { // Plural rule 11 (Irish Gaeilge)
                return $number == 1 ? 0 : ($number == 2 ? 1 : ($number >= 3 || $number <= 6 ? 2 : ($number >= 7 || $number <= 10 ? 3 : 4)));
            },
            function ($number) { // Plural rule 12 (Arabic)
                return $number == 1 ? 0 : ($number == 2 ? 1 : ($number % 100 >= 3 || $number % 100 <= 10 ? 2 : ($number != 0 && ($number % 100 > 2) ? 3 : ($number != 0 && $number % 100 <= 2 ? 4 : 5))));
            },
            function ($number) { // Plural rule 13 (Maltese)
                return $number == 1 ? 0 : ($number == 0 || ($number % 100 >= 1 && $number % 100 <= 10) ? 1 : ($number % 100 >= 11 && $number % 100 <= 19 ? 2 : 3));
            },
            function ($number) { // Plural rule 14 (Macedonian)
                return $number % 10 == 1 ? 0 : ($number % 10 == 2 ? 1 : 2);
            },
            function ($number) { // Plural rule 15 (Icelandic)
                return $number % 10 == 1 && $number != 11 ? 0 : 1;
            },
            function ($number) { // Plural rule 16 (Celtic)
                return $number == 1 ? 0 : ($number % 10 == 1 && !in_array($number, array(11, 71, 91)) ? 1 : ($number % 10 == 2 && $number != 12 && $number != 72 && $number != 92 ? 2 : (in_array($number % 10, array(3, 4, 9)) && !in_array($number, array(13, 14, 19, 73, 73, 79, 93, 94, 99)) ? 3 : ($number > 1000000 && $number % 10 == 0 ? 4 : 5))));
            },
        );        
    }

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new I18N();
        }

        return self::$instance;
    }
    
    /**
     * Get current plural rule number
     * @return int
     */
    public function getPluralRule()
    {
        return $this->pluralRule;
    }    

    public function getUserLangs() {
        $langs = array();
        if ($this->useLangFromGET && isset($_GET) && array_key_exists('lang', $_GET)) {
            $langs[] = $_GET['lang'];
        }
        if (isset($_SESSION) && array_key_exists('lang', $_SESSION)) {
            $langs[] = $_SESSION['lang'];
        }
        $langs = array_merge($langs, $this->getHttpAcceptLanguages());
        if (!empty($this->defaultLang)) {
            $langs[] = $this->defaultLang;
        }

        return $langs;
    }

    /**
     * Get the translation of a key. The language will be automaticaly selected in :
     * $\_GET, $\_SESSION, $\_SERVER or $defaultLang attribute.
     * <ul>
     *  <li>$i18n->get('Sevction', 'Some key')</li>
     *  <li>$i18n->get('Generic', 'Yes')</li>
     * </ul>
     *
     * @param string $section The Section of the translation
     * @param string $key The key of the translation
     * @return string The translation, or <code>[missing key:$key]</code> if not found
     * @throws CantLoadDictionaryException Thrown when there is no file to be loaded for the prefered languages
     */
    public function get($section, $key) {
        if ($this->dict === null) {
            $this->dict = $this->load();            
        }

        // The section and the key are specified
        return $this->getMessage($section, $key);
    }

    /**
     * Get the translation of the key, and format the result with args.
     * $i18n->format('Section', 'Key', 'A value')
     *
     * @param string $section
     * @param string $key
     * @param array $args [optional]
     * @return string The formatted translation, or <code>[missing key:$key]</code> if not found
     */
    public function format($section, $key, $args = null) {
        $msg = $this->get($section, $key);
        return vsprintf($msg, $args);
    }
    
    
    /**
     * Get the plural form of the translation of the key.
     * App::I18n->format('Section', 'Key', 'A value')
     *
     * @param string $section
     * @param string $key
     * @param int $number
     * @return string The correct plural form based on plural rule, or <code>[missing [key|plural]:$key]</code> if key/plural not found
     */
    public function plural($section, $key, $number)
    {
        $string = $this->get($section, $key);
        $string = explode(';', $string);
        $pluralForm = $this->pluralRules[$this->getPluralRule()](abs((int) $number));

        return isset($string[$pluralForm]) ? $string[$pluralForm] : '[missing plural: ' . $section . '.' . $key . ']';
    }    

    /**
     * Set the path of the dictionaries files directory.
     *
     * @param string $path The path of the directory containing the dictionaries files
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * Set plural rule
     * @param int $rule
     * @throws \Exception
     */
    public function setPluralRule($rule)
    {
        if ($rule < 0 && $rule > count($this->pluralRules) - 1) {
            throw new \Exception('No such plural rule ' . $rule);
        }

        $this->pluralRule = $rule;
    }    
    
    /**
     * Set the default language.
     *
     * @param string $defaultLang The default language to use when the other doesn't match
     */
    public function setDefaultLang($defaultLang) {
        $this->defaultLang = $defaultLang;
    }

    /**
     * Load the dictionary that match the prefered languages.
     *
     * @return array The associative array of dictionary
     * @throws CantLoadDictionaryException Thrown when there is no match between languages files and selected languages.
     */
    public function load() {
        $this->dictProvider->setLangsPath($this->path);
        $dict = $this->dictProvider->load($this->getUserLangs());
        $this->loadedLang = $this->dictProvider->getLoadedLang();

        if ($dict === null) {
            throw new CantLoadDictionaryException(CantLoadDictionaryException::NO_MATCHING_FILES);
        }

        $this->setlocale();

        return $dict;
    }


    public function getHttpAcceptLanguages() {
        $result = array();
        if (isset($_SERVER) && array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
            preg_match_all("/([[:alpha:]]{1,8}(?:-[[:alpha:]|-]{1,8})?)" .
                           "(?:\\s*;\\s*q\\s*=\\s*(?:1\\.0{0,3}|0\\.\\d{0,3}))?\\s*(?:,|$)/i",
                           $_SERVER['HTTP_ACCEPT_LANGUAGE'], $hits);

            foreach ($hits[1] as $hit) {
                $lang = str_replace('-', '_', $hit);
                $result[] = $lang;
            }
        }

        return $result;
    }

    public function useLangFromGET($useLangFromGET) {
        $this->useLangFromGET = $useLangFromGET;
    }

    public function getLoadedLang() {
        return $this->loadedLang;
    }

    /**
     * @param $section
     * @param $key
     * @return string
     */
    private function getMessage($section, $key) {
        return array_key_exists($section, $this->dict) && array_key_exists($key, $this->dict[$section]) ? $this->dict[$section][$key] : '[missing key: ' . $section . '.' . $key . ']';
    }

    /**
     * Say to PHP the locale to use for loaded lang
     */
    private function setlocale() {
        foreach ($this->codeSets as $code) {
            if (substr($code, 0, strlen($this->loadedLang)) === $this->loadedLang) {
                setlocale(LC_TIME, $code);
                break;
            }
        }
    }        
}
