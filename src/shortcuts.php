<?php
use o80\i18n\I18N;

/**
 * This method is a shortcut to <code>I18N::instance()-&gt;get($key);</code>.
 *
 * @param string $key The key of the translation
 * @return string The translation
 */
function __($key) {
    return I18N::instance()->get($key);
}
