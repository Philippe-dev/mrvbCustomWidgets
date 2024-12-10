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
    '0.5.3',
    [
        'permissions' => dcCore::app()->auth->makePermissions([dcAuth::PERMISSION_CONTENT_ADMIN]),
        'type'        => 'plugin',
        'dc_min'      => '2.24',
        'support'     => 'https://www.mirovinben.fr/blog/index.php?post/id1428',
        'details'     => 'https://plugins.dotaddict.org/dc2/details/mrvbCustomWidgets',
    ]
);
