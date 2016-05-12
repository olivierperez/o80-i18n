<?php
namespace o80\i18n;

interface Provider {

    /**
     * @param string $path The path of the directory containing the dictionaries files
     */
    public function setLangsPath($path);

    /**
     * Load the best dictionary looking at the prefered languages given in parameter.
     *
     * @param array $langs Ordered list of accepted languages, prefered ones are first
     * @return array|null The dictionary or null if not found
     * @throws CantLoadDictionaryException Thrown when there is no files in the directories path
     */
    public function load($langs);

    /**
     * This method gives the code of loaded lang. It must be called AFTER the "load" method.
     *
     * @return string The code of the loaded lang.
     */
    public function getLoadedLang();

}
