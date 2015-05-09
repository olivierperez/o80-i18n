<?php
use o80\i18n\I18N;

/**
 * This method is a shortcut to <code>I18N::instance()-&gt;get(...);</code>.
 *
 * Examples:
 * <ul>
 *  <li>__('Section', 'Key')</li>
 *  <li>__('Generic', 'Yes')</li>
 * </ul>
 *
 * @param string $section The Section of the translation
 * @param string $key The key of the translation
 * @return string The translation
 */
function __($section, $key) {
    return I18N::instance()->get($section, $key);
}

/**
 * This method is a shortcut to <code>I18N::instance()-&gt;format(...);</code>.
 *
 * @param string $section The Section of the translation
 * @param string $key The key of the translation
 * @param mixed $args [optional]
 * @return string The formatted translation
 */
function __f($section, $key, $args) {
    $args = array_slice(func_get_args(), 2);
    return I18N::instance()->format($section, $key, $args);
}
