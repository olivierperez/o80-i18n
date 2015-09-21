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
        'el_GR.UTF-8', 'gu.UTF-8', 'he_IL.utf8', 'hi_IN.UTF-8', 'hu.UTF-8', 'is_IS.UTF-8', 'id_ID.UTF-8',
        'it_IT.UTF-8', 'ja_JP.UTF-8', 'kn_IN.UTF-8', 'km_KH.UTF-8', 'ko_KR.UTF-8', 'lo_LA.UTF-8', 'lt_LT.UTF-8',
        'lat.UTF-8', 'ml_IN.UTF-8', 'ms_MY.UTF-8', 'mi_NZ.UTF-8', 'mi_NZ.UTF-8', 'mn.UTF-8', 'no_NO.UTF-8',
        'no_NO.UTF-8', 'nn_NO.UTF-8', 'pl.UTF-8', 'pt_PT.UTF-8', 'pt_BR.UTF-8', 'ro_RO.UTF-8', 'ru_RU.UTF-8',
        'mi_NZ.UTF-8', 'sr_CS.UTF-8', 'sk_SK.UTF-8', 'sl_SI.UTF-8', 'so_SO.UTF-8', 'es_ES.UTF-8', 'sv_SE.UTF-8',
        'tl.UTF-8', 'ta_IN.UTF-8', 'th_TH.UTF-8', 'mi_NZ.UTF-8', 'tr_TR.UTF-8', 'uk_UA.UTF-8', 'vi_VN.UTF-8'
    );

    public function __construct($dictProvider = null) {
        $this->dictProvider = $dictProvider != null ? $dictProvider : new JsonProvider();
    }

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new I18N();
        }

        return self::$instance;
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
     * Set the path of the dictionaries files directory.
     *
     * @param string $path The path of the directory containing the dictionaries files
     */
    public function setPath($path) {
        $this->path = $path;
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
