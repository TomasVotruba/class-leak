<?php

// scoper-autoload.php @generated by PhpScoper

// Backup the autoloaded Composer files
if (isset($GLOBALS['__composer_autoload_files'])) {
    $existingComposerAutoloadFiles = $GLOBALS['__composer_autoload_files'];
}

$loader = require_once __DIR__.'/autoload.php';
// Ensure InstalledVersions is available
$installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

// Restore the backup
if (isset($existingComposerAutoloadFiles)) {
    $GLOBALS['__composer_autoload_files'] = $existingComposerAutoloadFiles;
} else {
    unset($GLOBALS['__composer_autoload_files']);
}

// Class aliases. For more information see:
// https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#class-aliases
if (!function_exists('humbug_phpscoper_expose_class')) {
    function humbug_phpscoper_expose_class(string $exposed, string $prefixed): void {
        if (!class_exists($exposed, false) && !interface_exists($exposed, false) && !trait_exists($exposed, false)) {
            spl_autoload_call($prefixed);
        }
    }
}
humbug_phpscoper_expose_class('ComposerAutoloaderInit3563a64a195e574a006fa7cd018b18da', 'ClassLeak202411\ComposerAutoloaderInit3563a64a195e574a006fa7cd018b18da');

// Function aliases. For more information see:
// https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#function-aliases
if (!function_exists('formatErrorMessage')) { function formatErrorMessage() { return \ClassLeak202411\formatErrorMessage(...func_get_args()); } }
if (!function_exists('grapheme_extract')) { function grapheme_extract() { return \ClassLeak202411\grapheme_extract(...func_get_args()); } }
if (!function_exists('grapheme_stripos')) { function grapheme_stripos() { return \ClassLeak202411\grapheme_stripos(...func_get_args()); } }
if (!function_exists('grapheme_stristr')) { function grapheme_stristr() { return \ClassLeak202411\grapheme_stristr(...func_get_args()); } }
if (!function_exists('grapheme_strlen')) { function grapheme_strlen() { return \ClassLeak202411\grapheme_strlen(...func_get_args()); } }
if (!function_exists('grapheme_strpos')) { function grapheme_strpos() { return \ClassLeak202411\grapheme_strpos(...func_get_args()); } }
if (!function_exists('grapheme_strripos')) { function grapheme_strripos() { return \ClassLeak202411\grapheme_strripos(...func_get_args()); } }
if (!function_exists('grapheme_strrpos')) { function grapheme_strrpos() { return \ClassLeak202411\grapheme_strrpos(...func_get_args()); } }
if (!function_exists('grapheme_strstr')) { function grapheme_strstr() { return \ClassLeak202411\grapheme_strstr(...func_get_args()); } }
if (!function_exists('grapheme_substr')) { function grapheme_substr() { return \ClassLeak202411\grapheme_substr(...func_get_args()); } }
if (!function_exists('parseArgs')) { function parseArgs() { return \ClassLeak202411\parseArgs(...func_get_args()); } }
if (!function_exists('setproctitle')) { function setproctitle() { return \ClassLeak202411\setproctitle(...func_get_args()); } }
if (!function_exists('showHelp')) { function showHelp() { return \ClassLeak202411\showHelp(...func_get_args()); } }
if (!function_exists('trigger_deprecation')) { function trigger_deprecation() { return \ClassLeak202411\trigger_deprecation(...func_get_args()); } }

return $loader;