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

use Dotclear\Helper\Html\Html;

if (!defined('DC_RC_PATH')) {
    return;
}

dcCore::app()->addBehavior('initWidgets', ['mrvbWidgetsBehaviors','initWidgets']);

class mrvbWidgetsBehaviors
{
    public static function initWidgets($w)
    {
        $rs        = dcCore::app()->blog->getCategories(['post_type' => 'post']);
        $helpcatID = [];
        $listcatID = ['' => '', __('Uncategorized') => 'null'];
        while ($rs->fetch()) {
            $helpcatID[str_repeat('&nbsp;&nbsp;', $rs->level - 1) . ($rs->level - 1 == 0 ? '' : '&bull; ') . Html::escapeHTML($rs->cat_title) . ' (ID=' . $rs->cat_id . ')'] = $rs->cat_id;
            $listcatID[str_repeat('&nbsp;&nbsp;', $rs->level - 1) . ($rs->level - 1 == 0 ? '' : '&bull; ') . Html::escapeHTML($rs->cat_title) . ' (ID=' . $rs->cat_id . ')'] = $rs->cat_id;
        }

        $w->create('mrvbCategories', __('Mrvb: categories'), ['tplMrvbWidgets','mrvb_Categories'], null, __('categories list'));
        $w->mrvbCategories->setting(
            'title',
            __('Title (optional):'),
            __('Categories'),
            'text'
        );
        $w->mrvbCategories->setting(
            'showcount',
            __('With entries counts:'),
            'shownever',
            'combo',
            [
                __('Never')           => 'shownever',
                __('After each item') => 'showafter',
                __('In balloon')      => 'showballoon',
            ]
        );
        $w->mrvbCategories->setting(
            'countsubcat',
            __('Include sub cats in count'),
            0,
            'check'
        );
        $w->mrvbCategories->setting(
            'excludeID',
            __('Categories to exclude (ID separated by commas or line breaks):'),
            '',
            'textarea',
            ['class' => 'noeditor']
        );
        $w->mrvbCategories->setting(
            'helpcatID',
            __('For information, list of blog categories with their ID:'),
            '',
            'combo',
            $helpcatID
        );
        $w->mrvbCategories->setting(
            'modlist',
            __('Display Type:'),
            'list',
            'combo',
            [
                __('Bulleted list') => 'list',
                __('Paragraph')     => 'text',
            ]
        );
        $w->mrvbCategories->setting(
            'separator',
            __('Character(s) separating each item (if paragraph):'),
            ',',
            'text'
        );
        $w->mrvbCategories->setting(
            'endlist',
            __('Character(s) closing the paragraph:'),
            '.',
            'text'
        );
        $w->mrvbCategories->addHomeOnly();
        $w->mrvbCategories->addContentOnly();
        $w->mrvbCategories->addClass();
        $w->mrvbCategories->addOffline();

        $w->create('mrvbFeed', __('Mrvb: feed reader'), ['tplMrvbWidgets','mrvb_Feed'], null, __('list of last entries from feed (RSS or Atom)'));
        $w->mrvbFeed->setting(
            'title',
            __('Title (optional):'),
            __('Somewhere else'),
            'text'
        );
        $w->mrvbFeed->setting(
            'url',
            __('Feed URL:'),
            '',
            'text'
        );
        $w->mrvbFeed->setting(
            'limit',
            __('Entries limit:'),
            10
        );
        $w->mrvbFeed->setting(
            'formdate',
            __('Format of the date:'),
            '%d/%m/%Y',
            'text'
        );
        $w->mrvbFeed->setting(
            'formitem',
            __('Format of items (%date%, %title%, %date+title%):'),
            '%title%',
            'textarea'
        );
        $w->mrvbFeed->setting(
            'separator',
            __('Character separating date and title (only if format = %date+title%):'),
            '-',
            'text'
        );
        $w->mrvbFeed->addHomeOnly();
        $w->mrvbFeed->addContentOnly();
        $w->mrvbFeed->addClass();
        $w->mrvbFeed->addOffline();

        $w->create('mrvbLastComments', __('Mrvb: last comments'), ['tplMrvbWidgets','mrvb_LastComments'], null, __('list of last comments posted'));
        $w->mrvbLastComments->setting(
            'title',
            __('Title (optional):'),
            __('Last comments'),
            'text'
        );
        $w->mrvbLastComments->setting(
            'limit',
            __('Comments limit:'),
            10
        );
        $w->mrvbLastComments->setting(
            'notme',
            __('Except those of the post\' author'),
            false,
            'check'
        );
        $w->mrvbLastComments->addHomeOnly();
        $w->mrvbLastComments->addContentOnly();
        $w->mrvbLastComments->addClass();
        $w->mrvbLastComments->addOffline();

        $w->create('mrvbLastPosts', __('Mrvb: last posts'), ['tplMrvbWidgets','mrvb_LastPosts'], null, __('list of last entries published'));
        $w->mrvbLastPosts->setting(
            'title',
            __('Title (optional):'),
            __('Last entries'),
            'text'
        );
        $w->mrvbLastPosts->setting(
            'selected',
            __('Selected posts only'),
            false,
            'check'
        );
        $w->mrvbLastPosts->setting(
            'typepost',
            __('Filter on one type or list of types (empty = post):'),
            '',
            'text'
        );
        $w->mrvbLastPosts->setting(
            'category',
            __('Having category:'),
            '',
            'combo',
            $listcatID
        );
        $w->mrvbLastPosts->setting(
            'excludeID',
            __('Or categories to exclude (ID separated by commas or line breaks):'),
            '',
            'textarea',
            ['class' => 'noeditor']
        );
        $w->mrvbLastPosts->setting(
            'tag',
            __('Tag:'),
            ''
        );
        $w->mrvbLastPosts->setting(
            'limit',
            __('Entries limit (0 = all):'),
            10
        );
        $w->mrvbLastPosts->addHomeOnly();
        $w->mrvbLastPosts->addContentOnly();
        $w->mrvbLastPosts->addClass();
        $w->mrvbLastPosts->addOffline();

        $w->create('mrvbNavigation', __('Mrvb: navigation links'), ['tplMrvbWidgets','mrvb_Navigation'], null, __('list of navigation links'));
        $w->mrvbNavigation->setting(
            'title',
            __('Title (optional):'),
            '',
            'text'
        );
        $w->mrvbNavigation->setting(
            'CSSid',
            __('Default ID for this widget:'),
            'topnav',
            'text'
        );
        $w->mrvbNavigation->setting(
            'home',
            __('Text for \'Home\':'),
            __('Home'),
            'text'
        );
        $w->mrvbNavigation->setting(
            'separator',
            __('Character(s) separating each item (empty = \' - \'):'),
            '',
            'text'
        );
        $w->mrvbNavigation->setting(
            'archives',
            __('Text for \'Archives\':'),
            __('Archives'),
            'text'
        );
        $w->mrvbNavigation->addHomeOnly();
        $w->mrvbNavigation->addContentOnly();
        $w->mrvbNavigation->addClass();
        $w->mrvbNavigation->addOffline();
        
        $w->create('mrvbPages', __('Mrvb: pages'), ['tplMrvbWidgets','mrvb_Pages'], null, __('pages list'));
        $w->mrvbPages->setting(
            'title',
            __('Title (optional):'),
            __('Pages'),
            'text'
        );
        $w->mrvbPages->setting(
            'excludeID',
            __('Pages to exclude (ID separated by commas or line breaks):'),
            '',
            'textarea',
            ['class' => 'noeditor']
        );
        $w->mrvbPages->setting(
            'modlist',
            __('Display Type:'),
            'list',
            'combo',
            [
                __('Bulleted list') => 'list',
                __('Paragraph')     => 'text',
            ]
        );
        $w->mrvbPages->setting(
            'separator',
            __('Character(s) separating each item (if paragraph):'),
            ',',
            'text'
        );
        $w->mrvbPages->setting(
            'endlist',
            __('Character(s) closing the paragraph:'),
            '.',
            'text'
        );
        $w->mrvbPages->setting(
            'sortby',
            __('Order by:'),
            'post_title',
            'combo',
            [
                __('Page title')       => 'post_title',
                __('Page position')    => 'post_position',
                __('Publication date') => 'post_dt',
            ]
        );
        $w->mrvbPages->setting(
            'orderby',
            __('Sort:'),
            'asc',
            'combo',
            [
                __('Ascending')  => 'asc',
                __('Descending') => 'desc',
            ]
        );
        $w->mrvbPages->addHomeOnly();
        $w->mrvbPages->addContentOnly();
        $w->mrvbPages->addClass();
        $w->mrvbPages->addOffline();

        $w->create('mrvbSearch', __('Mrvb: search engine'), ['tplMrvbWidgets','mrvb_Search'], null, __('search engine form'));
        $w->mrvbSearch->setting(
            'title',
            __('Title (optional):'),
            __('Search'),
            'text'
        );
        $w->mrvbSearch->setting(
            'CSSid',
            __('Default ID for this widget:'),
            'search',
            'text'
        );
        $w->mrvbSearch->addHomeOnly();
        $w->mrvbSearch->addContentOnly();
        $w->mrvbSearch->addClass();
        $w->mrvbSearch->addOffline();

        $w->create('mrvbSubCategories', __('Mrvb: subcategories'), ['tplMrvbWidgets','mrvb_SubCategories'], null, __('hierarchical list of categories'));
        $w->mrvbSubCategories->setting(
            'title',
            __('Title (optional):'),
            __('Categories'),
            'text'
        );
        $w->mrvbSubCategories->setting(
            'showcount',
            __('With entries counts:'),
            'shownever',
            'combo',
            [
                __('Never')           => 'shownever',
                __('After each item') => 'showafter',
                __('In balloon')      => 'showballoon',
            ]
        );
        $w->mrvbSubCategories->setting(
            'maxlevel',
            __('Maximum levels (empty or zero means all):'),
            '',
            'text'
        );
        $w->mrvbSubCategories->setting(
            'countsubcat',
            __('Include sub cats in count'),
            0,
            'check'
        );
        $w->mrvbSubCategories->setting(
            'excludeID',
            __('Categories to exclude (ID separated by commas or line breaks):'),
            '',
            'textarea',
            ['class' => 'noeditor']
        );
        $w->mrvbSubCategories->setting(
            'helpcatID',
            __('For information, list of blog categories with their ID:'),
            '',
            'combo',
            $helpcatID
        );
        $w->mrvbSubCategories->addHomeOnly();
        $w->mrvbSubCategories->addContentOnly();
        $w->mrvbSubCategories->addClass();
        $w->mrvbSubCategories->addOffline();

        $w->create('mrvbSubscribe', __('Mrvb: subscribe links'), ['tplMrvbWidgets','mrvbSubscribe'], null, __('feed subscription links'));
        $w->mrvbSubscribe->setting(
            'title',
            __('Title (optional):'),
            __('Subscribe'),
            'text'
        );
        $w->mrvbSubscribe->setting(
            'entriesAtom',
            __('Text for link "entries Atom feed":'),
            __('Entries Atom feed'),
            'text'
        );
        $w->mrvbSubscribe->setting(
            'commentsAtom',
            __('Text for link "comments Atom feed":'),
            __('Comments Atom feed'),
            'text'
        );
        $w->mrvbSubscribe->setting(
            'entriesRSS2',
            __('Text for link "entries RSS2 feed":'),
            __('Entries RSS2 feed'),
            'text'
        );
        $w->mrvbSubscribe->setting(
            'commentsRSS2',
            __('Text for link "comments RSS2 feed":'),
            __('Comments RSS2 feed'),
            'text'
        );
        $w->mrvbSubscribe->addHomeOnly();
        $w->mrvbSubscribe->addContentOnly();
        $w->mrvbSubscribe->addClass();
        $w->mrvbSubscribe->addOffline();

        $w->create('mrvbTags', __('Mrvb: tags'), ['tplMrvbWidgets','mrvb_Tags'], null, __('tags list'));
        $w->mrvbTags->setting(
            'title',
            __('Title (optional):'),
            __('Tags'),
            'text'
        );
        $w->mrvbTags->setting(
            'limit',
            __('Limit (empty means no limit):'),
            '20'
        );
        $w->mrvbTags->setting(
            'showcount',
            __('With entries counts:'),
            'shownever',
            'combo',
            [
                __('Never')           => 'shownever',
                __('After each item') => 'showafter',
                __('In balloon')      => 'showballoon',
            ]
        );
        $w->mrvbTags->setting(
            'exclude',
            __('Tags to exclude (separated by commas or line breaks):'),
            '',
            'textarea',
            ['class' => 'noeditor']
        );
        $w->mrvbTags->setting(
            'sortby',
            __('Order by:'),
            'meta_id_lower',
            'combo',
            [
                __('Tag name')      => 'meta_id_lower',
                __('Entries count') => 'count',
            ]
        );
        $w->mrvbTags->setting(
            'orderby',
            __('Sort:'),
            'asc',
            'combo',
            [
                __('Ascending')  => 'asc',
                __('Descending') => 'desc',
            ]
        );
        $w->mrvbTags->setting(
            'alltagslinktitle',
            __('Link to all tags:'),
            __('All tags'),
            'text'
        );
        $w->mrvbTags->setting(
            'modlist',
            __('Display Type:'),
            'list',
            'combo',
            [
                __('Bulleted list') => 'list',
                __('Paragraph')     => 'text',
            ]
        );
        $w->mrvbTags->setting(
            'separator',
            __('Character(s) separating each item (if paragraph):'),
            ',',
            'text'
        );
        $w->mrvbTags->setting(
            'endlist',
            __('Character(s) closing the paragraph:'),
            '.',
            'text'
        );
        $w->mrvbTags->addHomeOnly();
        $w->mrvbTags->addContentOnly();
        $w->mrvbTags->addClass();
        $w->mrvbTags->addOffline();

        $w->create('mrvbText', __('Mrvb: text'), ['tplMrvbWidgets','mrvb_Text'], null, __('text'));
        $w->mrvbText->setting(
            'title',
            __('Title (optional):'),
            '',
            'text'
        );
        $w->mrvbText->setting(
            'text',
            __('Text:'),
            '',
            'textarea'
        );
        $w->mrvbText->addHomeOnly();
        $w->mrvbText->addContentOnly();
        $w->mrvbText->addClass();
        $w->mrvbText->addOffline();
    }
}
