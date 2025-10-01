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
    '0.8',
    [
        'date'        => '2025-10-01T00:00:08+0100',
        'requires'    => [['core', '2.36']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'support'     => 'https://www.mirovinben.fr/blog/index.php?post/id1428',
        'details'     => 'https://plugins.dotaddict.org/dc2/details/mrvbCustomWidgets',
    ]
);
