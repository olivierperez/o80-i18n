<?php
namespace o80\i18n;

class CantLoadDictionaryException extends \Exception {
    const NO_MATCHING_FILES = "There is no file matching the languages selected.";
    const NO_DICTIONARY_FILES = "There is no files in the dictionaries path.";
}
