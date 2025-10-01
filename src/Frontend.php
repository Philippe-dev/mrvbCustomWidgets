<?php
/**
 * @brief mrvbCustomWidgets, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Mirovinben (https://www.mirovinben.fr/)
 *
 * @copyright AGPL-3.0
 */
declare(strict_types=1);

namespace Dotclear\Plugin\mrvbCustomWidgets;

use Dotclear\App;
use Dotclear\Helper\Process\TraitProcess;

class Frontend
{
    use TraitProcess;
    
    public static function init(): bool
    {
        return self::status(My::checkContext(My::FRONTEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        App::behavior()->addBehavior('initWidgets', [Widgets::class, 'initWidgets']);

        App::lang()->set(dirname(__FILE__) . '/locales/' . App::lang()->getLang() . '/main');

        return true;
    }
}
