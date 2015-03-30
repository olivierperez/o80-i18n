<?php
use o80\i18n\I18N;

/**
 * This method is a shortcut to <code>I18N::instance()-&gt;get($key);</code>.
 *
 * Examples:
 * <ul>
 *  <li>__('SimpleKey')</li>
 *  <li>__('Generic', 'Yes')</li>
 * </ul>
 *
 * @param string $sectionOkKey The Section of the translation (ex: 'Generic'), or the key if no section is used
 * @param string $key The key of the translation (the first arguments must be the name of the Section)
 * @return string The translation
 */
function __($sectionOkKey, $key = null) {
    return I18N::instance()->get($sectionOkKey, $key);
}
