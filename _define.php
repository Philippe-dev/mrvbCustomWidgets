<?php

# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of mrvbCustomWidgets, a plugin for Dotclear 2
#
# © Mirovinben (https://www.mirovinben.fr/)
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'mrvbCustomWidgets',
    'Mrvb customizations of DC2 widgets',
    'Mirovinben (origin : DC-Team)',
    '0.6',
    [
        'date'        => '2025-08-11T00:00:17+0100',
        'permissions' => 'My',
        'type'        => 'plugin',
        'dc_min'      => '2.34',
        'support'     => 'https://www.mirovinben.fr/blog/index.php?post/id1428',
        'details'     => 'https://plugins.dotaddict.org/dc2/details/mrvbCustomWidgets',
    ]
);
