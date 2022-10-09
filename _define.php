<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of mrvbCustomWidgets, a plugin for Dotclear 2
#
# © Mirovinben (http://www.mirovinben.fr/)
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_RC_PATH')) { return; }

$this->registerModule(
	/* Name        */	"mrvbCustomWidgets",
	/* Description */	"Mrvb customizations of DC2 widgets",
	/* Author      */	"Mirovinben (origin : DC-Team)",
	/* Version     */	'0.5',
	/* Properties  */	array(
							'permissions' => 'usage,contentadmin',
							'type'        => 'plugin',
							'dc_min'      => '2.24',
							'support'     => 'http://www.mirovinben.fr/blog/index.php?post/id1428',
							'details'     => 'http://plugins.dotaddict.org/dc2/details/mrvbCustomWidgets'
						)
);