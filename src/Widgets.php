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
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Html\Form\Link;
use Dotclear\Helper\Html\Form\Li;
use Dotclear\Helper\Html\Form\Ul;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Network\Feed\Reader;
use Dotclear\Plugin\widgets\WidgetsElement;
use Dotclear\Plugin\widgets\WidgetsStack;

class Widgets
{
    /**
     * Initializes the pages widget.
     *
     * @param      WidgetsStack  $widgets  The widgets
     */
    public static function initWidgets(WidgetsStack $widgets): void
    {
        $rs        = App::blog()->getCategories(['post_type' => 'post']);
        $helpcatID = [];
        $listcatID = ['' => '', __('Uncategorized') => 'null'];
        while ($rs->fetch()) {
            $helpcatID[str_repeat('&nbsp;&nbsp;', $rs->level - 1) . ($rs->level - 1 == 0 ? '' : '&bull; ') . Html::escapeHTML($rs->cat_title) . ' (ID=' . $rs->cat_id . ')'] = $rs->cat_id;
            $listcatID[str_repeat('&nbsp;&nbsp;', $rs->level - 1) . ($rs->level - 1 == 0 ? '' : '&bull; ') . Html::escapeHTML($rs->cat_title) . ' (ID=' . $rs->cat_id . ')'] = $rs->cat_id;
        }

        $widgets->create(
            'mrvbCategories',
            __('Mrvb: categories'),
            self::mrvb_Categories(...),
            null,
            __('categories list')
        );

        $widgets->mrvbCategories->setting(
            'title',
            __('Title (optional):'),
            __('Categories'),
            'text'
        );
        $widgets->mrvbCategories->setting(
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
        $widgets->mrvbCategories->setting(
            'countsubcat',
            __('Include sub cats in count'),
            0,
            'check'
        );
        $widgets->mrvbCategories->setting(
            'excludeID',
            __('Categories to exclude (ID separated by commas or line breaks):'),
            '',
            'textarea',
            ['class' => 'noeditor']
        );
        $widgets->mrvbCategories->setting(
            'helpcatID',
            __('For information, list of blog categories with their ID:'),
            '',
            'combo',
            $helpcatID
        );
        $widgets->mrvbCategories->setting(
            'modlist',
            __('Display Type:'),
            'list',
            'combo',
            [
                __('Bulleted list') => 'list',
                __('Paragraph')     => 'text',
            ]
        );
        $widgets->mrvbCategories->setting(
            'separator',
            __('Character(s) separating each item (if paragraph):'),
            ',',
            'text'
        );
        $widgets->mrvbCategories->setting(
            'endlist',
            __('Character(s) closing the paragraph:'),
            '.',
            'text'
        );
        $widgets->mrvbCategories->addHomeOnly();
        $widgets->mrvbCategories->addContentOnly();
        $widgets->mrvbCategories->addClass();
        $widgets->mrvbCategories->addOffline();

        $widgets->create(
            'mrvbFeed',
            __('Mrvb: feed reader'),
            self::mrvb_Feed(...),
            null,
            __('list of last entries from feed (RSS or Atom)')
        );

        $widgets->mrvbFeed->setting(
            'title',
            __('Title (optional):'),
            __('Somewhere else'),
            'text'
        );
        $widgets->mrvbFeed->setting(
            'url',
            __('Feed URL:'),
            '',
            'text'
        );
        $widgets->mrvbFeed->setting(
            'limit',
            __('Entries limit:'),
            10
        );
        $widgets->mrvbFeed->setting(
            'formdate',
            __('Format of the date:'),
            '%d/%m/%Y',
            'text'
        );
        $widgets->mrvbFeed->setting(
            'formitem',
            __('Format of items (%date%, %title%, %date+title%):'),
            '%title%',
            'textarea'
        );
        $widgets->mrvbFeed->setting(
            'separator',
            __('Character separating date and title (only if format = %date+title%):'),
            '-',
            'text'
        );
        $widgets->mrvbFeed->addHomeOnly();
        $widgets->mrvbFeed->addContentOnly();
        $widgets->mrvbFeed->addClass();
        $widgets->mrvbFeed->addOffline();

        $widgets->create(
            'mrvbLastComments',
            __('Mrvb: last comments'),
            self::mrvb_LastComments(...),
            null,
            __('list of last comments posted')
        );

        $widgets->mrvbLastComments->setting(
            'title',
            __('Title (optional):'),
            __('Last comments'),
            'text'
        );
        $widgets->mrvbLastComments->setting(
            'limit',
            __('Comments limit:'),
            10
        );
        $widgets->mrvbLastComments->setting(
            'notme',
            __('Except those of the post\' author'),
            false,
            'check'
        );
        $widgets->mrvbLastComments->addHomeOnly();
        $widgets->mrvbLastComments->addContentOnly();
        $widgets->mrvbLastComments->addClass();
        $widgets->mrvbLastComments->addOffline();

        $widgets->create(
            'mrvbLastPosts',
            __('Mrvb: last posts'),
            self::mrvb_LastPosts(...),
            null,
            __('list of last entries published')
        );

        $widgets->mrvbLastPosts->setting(
            'title',
            __('Title (optional):'),
            __('Last entries'),
            'text'
        );
        $widgets->mrvbLastPosts->setting(
            'selected',
            __('Selected posts only'),
            false,
            'check'
        );
        $widgets->mrvbLastPosts->setting(
            'typepost',
            __('Filter on one type or list of types (empty = post):'),
            '',
            'text'
        );
        $widgets->mrvbLastPosts->setting(
            'category',
            __('Having category:'),
            '',
            'combo',
            $listcatID
        );
        $widgets->mrvbLastPosts->setting(
            'excludeID',
            __('Or categories to exclude (ID separated by commas or line breaks):'),
            '',
            'textarea',
            ['class' => 'noeditor']
        );
        $widgets->mrvbLastPosts->setting(
            'tag',
            __('Tag:'),
            ''
        );
        $widgets->mrvbLastPosts->setting(
            'limit',
            __('Entries limit (0 = all):'),
            10
        );
        $widgets->mrvbLastPosts->addHomeOnly();
        $widgets->mrvbLastPosts->addContentOnly();
        $widgets->mrvbLastPosts->addClass();
        $widgets->mrvbLastPosts->addOffline();

        $widgets->create(
            'mrvbNavigation',
            __('Mrvb: navigation links'),
            self::mrvb_Navigation(...),
            null,
            __('list of navigation links')
        );

        $widgets->mrvbNavigation->setting(
            'title',
            __('Title (optional):'),
            '',
            'text'
        );
        $widgets->mrvbNavigation->setting(
            'CSSid',
            __('Default ID for this widget:'),
            'topnav',
            'text'
        );
        $widgets->mrvbNavigation->setting(
            'home',
            __('Text for \'Home\':'),
            __('Home'),
            'text'
        );
        $widgets->mrvbNavigation->setting(
            'separator',
            __('Character(s) separating each item (empty = \' - \'):'),
            '',
            'text'
        );
        $widgets->mrvbNavigation->setting(
            'archives',
            __('Text for \'Archives\':'),
            __('Archives'),
            'text'
        );
        $widgets->mrvbNavigation->addHomeOnly();
        $widgets->mrvbNavigation->addContentOnly();
        $widgets->mrvbNavigation->addClass();
        $widgets->mrvbNavigation->addOffline();

        $widgets->create(
            'mrvbPages',
            __('Mrvb: pages'),
            self::mrvb_Pages(...),
            null,
            __('pages list')
        );

        $widgets->mrvbPages->setting(
            'title',
            __('Title (optional):'),
            __('Pages'),
            'text'
        );
        $widgets->mrvbPages->setting(
            'excludeID',
            __('Pages to exclude (ID separated by commas or line breaks):'),
            '',
            'textarea',
            ['class' => 'noeditor']
        );
        $widgets->mrvbPages->setting(
            'modlist',
            __('Display Type:'),
            'list',
            'combo',
            [
                __('Bulleted list') => 'list',
                __('Paragraph')     => 'text',
            ]
        );
        $widgets->mrvbPages->setting(
            'separator',
            __('Character(s) separating each item (if paragraph):'),
            ',',
            'text'
        );
        $widgets->mrvbPages->setting(
            'endlist',
            __('Character(s) closing the paragraph:'),
            '.',
            'text'
        );
        $widgets->mrvbPages->setting(
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
        $widgets->mrvbPages->setting(
            'orderby',
            __('Sort:'),
            'asc',
            'combo',
            [
                __('Ascending')  => 'asc',
                __('Descending') => 'desc',
            ]
        );
        $widgets->mrvbPages->addHomeOnly();
        $widgets->mrvbPages->addContentOnly();
        $widgets->mrvbPages->addClass();
        $widgets->mrvbPages->addOffline();

        $widgets->create(
            'mrvbSearch',
            __('Mrvb: search engine'),
            self::mrvb_Search(...),
            null,
            __('search engine form')
        );

        $widgets->mrvbSearch->setting(
            'title',
            __('Title (optional):'),
            __('Search'),
            'text'
        );
        $widgets->mrvbSearch->setting(
            'CSSid',
            __('Default ID for this widget:'),
            'search',
            'text'
        );
        $widgets->mrvbSearch->addHomeOnly();
        $widgets->mrvbSearch->addContentOnly();
        $widgets->mrvbSearch->addClass();
        $widgets->mrvbSearch->addOffline();

        $widgets->create(
            'mrvbSubCategories',
            __('Mrvb: subcategories'),
            self::mrvb_SubCategories(...),
            null,
            __('hierarchical list of categories')
        );

        $widgets->mrvbSubCategories->setting(
            'title',
            __('Title (optional):'),
            __('Categories'),
            'text'
        );
        $widgets->mrvbSubCategories->setting(
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
        $widgets->mrvbSubCategories->setting(
            'maxlevel',
            __('Maximum levels (empty or zero means all):'),
            '',
            'text'
        );
        $widgets->mrvbSubCategories->setting(
            'countsubcat',
            __('Include sub cats in count'),
            0,
            'check'
        );
        $widgets->mrvbSubCategories->setting(
            'excludeID',
            __('Categories to exclude (ID separated by commas or line breaks):'),
            '',
            'textarea',
            ['class' => 'noeditor']
        );
        $widgets->mrvbSubCategories->setting(
            'helpcatID',
            __('For information, list of blog categories with their ID:'),
            '',
            'combo',
            $helpcatID
        );
        $widgets->mrvbSubCategories->addHomeOnly();
        $widgets->mrvbSubCategories->addContentOnly();
        $widgets->mrvbSubCategories->addClass();
        $widgets->mrvbSubCategories->addOffline();

        $widgets->create(
            'mrvbSubscribe',
            __('Mrvb: subscribe links'),
            self::mrvbSubscribe(...),
            null,
            __('feed subscription links')
        );

        $widgets->mrvbSubscribe->setting(
            'title',
            __('Title (optional):'),
            __('Subscribe'),
            'text'
        );
        $widgets->mrvbSubscribe->setting(
            'entriesAtom',
            __('Text for link "entries Atom feed":'),
            __('Entries Atom feed'),
            'text'
        );
        $widgets->mrvbSubscribe->setting(
            'commentsAtom',
            __('Text for link "comments Atom feed":'),
            __('Comments Atom feed'),
            'text'
        );
        $widgets->mrvbSubscribe->setting(
            'entriesRSS2',
            __('Text for link "entries RSS2 feed":'),
            __('Entries RSS2 feed'),
            'text'
        );
        $widgets->mrvbSubscribe->setting(
            'commentsRSS2',
            __('Text for link "comments RSS2 feed":'),
            __('Comments RSS2 feed'),
            'text'
        );
        $widgets->mrvbSubscribe->addHomeOnly();
        $widgets->mrvbSubscribe->addContentOnly();
        $widgets->mrvbSubscribe->addClass();
        $widgets->mrvbSubscribe->addOffline();

        $widgets->create(
            'mrvbTags',
            __('Mrvb: tags'),
            self::mrvb_Tags(...),
            null,
            __('tags list')
        );

        $widgets->mrvbTags->setting(
            'title',
            __('Title (optional):'),
            __('Tags'),
            'text'
        );
        $widgets->mrvbTags->setting(
            'limit',
            __('Limit (empty means no limit):'),
            '20'
        );
        $widgets->mrvbTags->setting(
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
        $widgets->mrvbTags->setting(
            'exclude',
            __('Tags to exclude (separated by commas or line breaks):'),
            '',
            'textarea',
            ['class' => 'noeditor']
        );
        $widgets->mrvbTags->setting(
            'sortby',
            __('Order by:'),
            'meta_id_lower',
            'combo',
            [
                __('Tag name')      => 'meta_id_lower',
                __('Entries count') => 'count',
            ]
        );
        $widgets->mrvbTags->setting(
            'orderby',
            __('Sort:'),
            'asc',
            'combo',
            [
                __('Ascending')  => 'asc',
                __('Descending') => 'desc',
            ]
        );
        $widgets->mrvbTags->setting(
            'alltagslinktitle',
            __('Link to all tags:'),
            __('All tags'),
            'text'
        );
        $widgets->mrvbTags->setting(
            'modlist',
            __('Display Type:'),
            'list',
            'combo',
            [
                __('Bulleted list') => 'list',
                __('Paragraph')     => 'text',
            ]
        );
        $widgets->mrvbTags->setting(
            'separator',
            __('Character(s) separating each item (if paragraph):'),
            ',',
            'text'
        );
        $widgets->mrvbTags->setting(
            'endlist',
            __('Character(s) closing the paragraph:'),
            '.',
            'text'
        );
        $widgets->mrvbTags->addHomeOnly();
        $widgets->mrvbTags->addContentOnly();
        $widgets->mrvbTags->addClass();
        $widgets->mrvbTags->addOffline();

        $widgets->create(
            'mrvbText',
            __('Mrvb: text'),
            self::mrvb_Text(...),
            null,
            __('text')
        );

        $widgets->mrvbText->setting(
            'title',
            __('Title (optional):'),
            '',
            'text'
        );
        $widgets->mrvbText->setting(
            'text',
            __('Text:'),
            '',
            'textarea'
        );
        $widgets->mrvbText->addHomeOnly();
        $widgets->mrvbText->addContentOnly();
        $widgets->mrvbText->addClass();
        $widgets->mrvbText->addOffline();
    }

    /**
     * Undocumented function
     *
     * @param [type] $list
     * @return void
     */
    public static function mrvb_ListToArray($list)
    {
        $tmp = str_replace("\n", ',', $list);
        $tmp = str_replace("\r", ',', $tmp);
        while (strpos($tmp, ',,') !== false) {
            $tmp = str_replace(',,', ',', $tmp);
        }
        if (!empty($tmp)) {
            $t   = explode(',', $tmp);
            $tab = [];
            $j   = 0;
            for ($i = 0 ; $i < count($t) ; $i++) {
                $t[$i] = Html::clean(trim($t[$i]));
                if (strlen($t[$i]) > 0) {
                    $tab[$j] = $t[$i];
                    ++$j;
                }
            }
        } else {
            $tab = [];
        }

        return $tab;
    }

    public static function mrvb_Categories(WidgetsElement $widgets)
    {
        if ($widgets->offline) {
            return;
        }

        if (!$widgets->checkHomeOnly(App::url()->type)) {
            return '';
        }

        $rs = App::blog()->getCategories();
        if ($rs->isEmpty()) {
            return;
        }
        $separator = __($widgets->separator) . ' ';
        $endlist   = __($widgets->endlist);
        $modlist   = ($widgets->modlist === 'list');
        $exclude   = self::mrvb_ListToArray($widgets->excludeID);

        $res = ($widgets->title ? $widgets->renderTitle(Html::escapeHTML($widgets->title)) . "\n" : '');
        ($modlist ? $res .= '<ul>' : $res .= '<p class="list">');
        $res .= "\n";
        while ($rs->fetch()) {
            if (!(in_array($rs->cat_id, $exclude))) {
                ($widgets->countsubcat ? $postcount = $rs->nb_total : $postcount = $rs->nb_post);
                $class = ' class="cat' . $rs->cat_id;
                if ((App::url()->type == 'category' && App::frontend()->context()->categories instanceof record && App::frontend()->context()->categories->cat_id == $rs->cat_id)
                || (App::url()->type == 'post' && App::frontend()->context()->posts instanceof record && App::frontend()->context()->posts->cat_id == $rs->cat_id)) {
                    $class .= ' category-current';
                }
                $class .= '"';
                if ($modlist) {
                    $res .= '<li' . $class . '><a href="' . App::blog()->url . App::url()->getBase('category') . '/' . $rs->cat_url . '"';
                    if ($widgets->showcount == 'showballoon') {
                        $res .= ' title="' . $postcount . '"';
                    }
                    $res .= '>' . Html::escapeHTML(__($rs->cat_title)) . '</a>';
                    if ($widgets->showcount == 'showafter') {
                        $res .= ' (' . $postcount . ')';
                    }
                    $res .= ' </li>';
                } else {
                    $res .= '<a' . $class . ' href="' . App::blog()->url . App::url()->getBase('category') . '/' . $rs->cat_url . '"';
                    if ($widgets->showcount == 'showballoon') {
                        $res .= ' title="' . $postcount . '"';
                    }
                    $res .= '>' . Html::escapeHTML(__($rs->cat_title)) . '</a>';
                    if ($widgets->showcount == 'showafter') {
                        $res .= ' (' . $postcount . ')';
                    }
                    $res .= '<span class="separator">' . $separator . '</span>';
                }
            }
            $res .= "\n";
        }
        if ($modlist) {
            $res .= '</ul>';
        } else {
            if (strlen($separator) > 0) {
                $res = substr($res, 0, strrpos($res, '<span class="separator">' . $separator)) . '<span class="last-separator">' . $endlist . '</span>';
            }
            $res .= ' </p>';
        }

        return $widgets->renderDiv((bool) $widgets->content_only, 'categories mrvbcategories ' . $widgets->CSSclass, '', $res);
    }

    public static function mrvb_feed(WidgetsElement $widget)
    {
        if (!$widget->get('url')) {
            return '';
        }

        if ($widget->offline) {
            return '';
        }

        if (!$widget->checkHomeOnly(App::url()->getType())) {
            return '';
        }

        $limit = abs((int) $widget->get('limit'));

        try {
            $feed = Reader::quickParse($widget->get('url'), App::config()->cacheRoot());
            if (!$feed || !count($feed->items)) {
                return '';
            }
        } catch (Exception) {
            return '';
        }

        $res = ($widget->title ? $widget->renderTitle(Html::escapeHTML($widget->title)) : '');

        $news = function ($items) use ($limit) {
            $i = 0;
            foreach ($items as $item) {
                $title = isset($item->title) && strlen(trim((string) $item->title)) ? $item->title : '';
                $link  = isset($item->link)  && strlen(trim((string) $item->link)) ? $item->link : '';

                if (!$link && !$title) {
                    continue;
                }

                if (!$title) {
                    $title = substr($link, 0, 25) . '...';
                }

                yield (new Li())
                    ->items([
                        $link ?
                        (new Link())
                            ->href(Html::escapeHTML($item->link))
                            ->text($title) :
                        (new Text($title)),
                    ]);

                $i++;
                if ($i >= $limit) {
                    break;
                }
            }
        };

        $res .= (new Ul())
            ->items([
                ...$news($feed->items),
            ])
        ->render();

        return $widget->renderDiv((bool) $widget->content_only, 'feed ' . $widget->class, '', $res);

    }

    public static function mrvb_LastComments(WidgetsElement $widgets)
    {
        if ($widgets->offline) {
            return;
        }
        if (!$widgets->checkHomeOnly(App::url()->type)) {
            return '';
        }
        $max             = abs((int) $widgets->limit);
        $params['order'] = 'comment_dt desc';
        $rs              = App::blog()->getComments($params);
        if ($rs->isEmpty() || ($max == 0)) {
            return;
        }

        $res = ($widgets->title ? $widgets->renderTitle(Html::escapeHTML($widgets->title)) . "\n" : '');
        $res .= '<ul>' . "\n";
        $i = 0;
        while ($rs->fetch()) {
            if (!($widgets->notme) || (($widgets->notme) && !($rs->isMe()))) {
                if ($i < $max) {
                    $res .= '<li class="' .
                    ((bool) $rs->comment_trackback ? 'last-tb' : 'last-comment') .
                    '"><a href="' . $rs->getPostURL() . '#c' . $rs->comment_id . '">' .
                    Html::escapeHTML($rs->post_title) . ' - ' .
                    Html::escapeHTML($rs->comment_author) .
                    '</a></li>' . "\n";
                    ++$i;
                } else {
                    break;
                }
            }
        }
        $res .= '</ul>';

        return $widgets->renderDiv((bool) $widgets->content_only, 'lastcomments mrvblastcomments ' . $widgets->CSSclass, '', $res);
    }

    public static function mrvb_LastPosts(WidgetsElement $widgets)
    {
        if ($widgets->offline) {
            return;
        }
        if (!$widgets->checkHomeOnly(App::url()->type)) {
            return '';
        }
        $params['order']      = 'post_dt desc';
        $params['no_content'] = true;
        $limit                = abs((int) $widgets->limit);
        $exclude              = [];
        $typepost             = str_replace(' ', '', $widgets->typepost);
        if (empty($typepost)) {
            $typepost = 'post';
        }
        $params['post_type'] = explode(',', $typepost);
        if ($widgets->selected) {
            $params['post_selected'] = true;
        }
        if ($widgets->category) {
            if ($widgets->category == 'null') {
                $params['sql'] = ' AND P.cat_id IS NULL ';
            } elseif (is_numeric($widgets->category)) {
                $params['cat_id'] = (int) $widgets->category;
            } else {
                $params['cat_url'] = $widgets->category;
            }
        } else {
            $exclude = self::mrvb_ListToArray($widgets->excludeID);
        }
        if ($widgets->tag) {
            $params['meta_id'] = $widgets->tag;
            $rs                = App::meta()->getPostsByMeta($params);
        } else {
            $rs = App::blog()->getPosts($params);
        }
        if ($rs->isEmpty()) {
            return;
        }

        $res = ($widgets->title ? $widgets->renderTitle(Html::escapeHTML($widgets->title)) . "\n" : '');
        $res .= '<ul>' . "\n";
        $i = 0;
        while ($rs->fetch()) {
            if (($i < $limit) || ($limit == 0)) {
                if (!(in_array($rs->cat_id, $exclude))) {
                    $class = '';
                    if (App::url()->type == 'post' && App::frontend()->context()->posts instanceof record && App::frontend()->context()->posts->post_id == $rs->post_id) {
                        $class = ' class="post-current"';
                    }
                    $res .= '<li' . $class . '><a href="' . $rs->getURL() . '">' .
                    Html::escapeHTML($rs->post_title) . '</a></li>' . "\n";
                    ++$i;
                }
            }
        }
        $res .= '</ul>';

        return $widgets->renderDiv((bool) $widgets->content_only, 'lastposts mrvblastposts ' . $widgets->CSSclass, '', $res);
    }

    public static function mrvb_Navigation(WidgetsElement $widgets)
    {
        if ($widgets->offline) {
            return;
        }
        if (!$widgets->checkHomeOnly(App::url()->type)) {
            return '';
        }
        $cssID = str_replace(' ', '', Html::escapeHTML($widgets->CSSid));

        $res = ($widgets->title ? $widgets->renderTitle(Html::escapeHTML($widgets->title)) . "\n" : '');
        $res .= '<ul>' . "\n";
        if (App::url()->type != 'default') {
            $res .= '<li class="topnav-home">' . '<a href="' . App::blog()->url . '">' . ($widgets->home ? $widgets->home : __('Home')) . '</a><span>' . ($widgets->separator ? $widgets->separator : '&nbsp;-&nbsp;') . '</span></li>' . "\n";
        }
        $res .= '<li class="topnav-arch">' . '<a href="' . App::blog()->url . App::url()->getURLFor('archive') . '">' . ($widgets->archives ? $widgets->archives : __('Archives')) . '</a>' . '</li>' . "\n";
        $res .= '</ul>';

        return $widgets->renderDiv((bool) $widgets->content_only, 'mrvbnavigation ' . $widgets->CSSclass, ($cssID ? ' id="' . $cssID . '"' : ''), $res);
    }

    public static function mrvb_Pages(WidgetsElement $widgets)
    {
        if ($widgets->offline) {
            return;
        }
        if (!$widgets->checkHomeOnly(App::url()->type)) {
            return '';
        }

        $separator               = __($widgets->separator) . ' ';
        $endlist                 = __($widgets->endlist);
        $modlist                 = ($widgets->modlist === 'list');
        $params['post_type']     = 'page';
        $params['limit']         = abs((int) $widgets->limit);
        $params['no_content']    = true;
        $params['post_selected'] = false;
        $sort                    = $widgets->sortby;
        if (!in_array($sort, ['post_title','post_position','post_dt'])) {
            $sort = 'post_title';
        }
        $order = $widgets->orderby;
        if ($order != 'asc') {
            $order = 'desc';
        }
        $params['order'] = $sort . ' ' . $order;
        $rs              = App::blog()->getPosts($params);
        if ($rs->isEmpty()) {
            return;
        }

        $res = ($widgets->title ? $widgets->renderTitle(Html::escapeHTML($widgets->title)) . "\n" : '');
        ($modlist ? $res .= '<ul>' : $res .= '<p class="list">');
        $res .= "\n";
        $exclude = self::mrvb_ListToArray($widgets->excludeID);
        while ($rs->fetch()) {
            $class = '';
            if ((App::url()->type == 'pages' && App::frontend()->context()->posts instanceof record && App::frontend()->context()->posts->post_id == $rs->post_id)) {
                $class = ' class="page-current"';
            }
            if (!(in_array($rs->post_id, $exclude))) {
                if ($modlist) {
                    $res .= '<li' . $class . '><a href="' . $rs->getURL() . '">' . Html::escapeHTML(__($rs->post_title)) . '</a></li>' . "\n";
                } else {
                    $res .= '<span' . $class . '><a href="' . $rs->getURL() . '">' . Html::escapeHTML(__($rs->post_title)) . '</a></span><span class="separator">' . $separator . '</span>' . "\n";
                }
            }
        }
        if ($modlist) {
            $res .= '</ul>';
        } else {
            if (strlen($separator) > 0) {
                $res = substr($res, 0, strrpos($res, '<span class="separator">' . $separator)) . '<span class="last-separator">' . $endlist . '</span>';
            }
            $res .= '</p>';
        }

        return $widgets->renderDiv((bool) $widgets->content_only, 'pages mrvbpages ' . $widgets->CSSclass, '', $res);
    }

    public static function mrvb_Search(WidgetsElement $widgets)
    {
        if ($widgets->offline) {
            return;
        }
        if (!$widgets->checkHomeOnly(App::url()->type)) {
            return '';
        }
        $value = isset($GLOBALS['_search']) ? Html::escapeHTML($GLOBALS['_search']) : '';
        $cssID = str_replace(' ', '', Html::escapeHTML($widgets->CSSid));

        $res = ($widgets->title ? $widgets->renderTitle(Html::escapeHTML($widgets->title)) . "\n" : '');
        $res .= '<form action="' . App::blog()->url . '" method="get">' . "\n" .
        '<fieldset>' . "\n" .
        '<p><input type="text" size="10" maxlength="255" id="q" name="q" value="' . $value . '" /> ' .
        '<input type="submit" class="submit" value="ok" /></p>' . "\n" .
        '</fieldset>' . "\n" .
        '</form>';

        return $widgets->renderDiv((bool) $widgets->content_only, 'mrvbsearch ' . $widgets->CSSclass, ($cssID ? 'id="' . $cssID . '"' : ''), $res);
    }

    public static function mrvb_SubCategories(WidgetsElement $widgets)
    {
        if ($widgets->offline) {
            return;
        }
        if (!$widgets->checkHomeOnly(App::url()->type)) {
            return '';
        }
        $rs = App::blog()->getCategories();
        if ($rs->isEmpty()) {
            return;
        }
        $exclude   = self::mrvb_ListToArray($widgets->excludeID);
        $max_level = abs((int) $widgets->maxlevel);
        if ($max_level == 0) {
            $max_level = 65535;
        }
        $ref_level = $level = $rs->level - 1;
        $cat_level = 0;

        $res = ($widgets->title ? $widgets->renderTitle(Html::escapeHTML($widgets->title)) : '');
        while ($rs->fetch()) {
            if (!(in_array($rs->cat_id, $exclude))) {
                if ($rs->level <= $max_level) {
                    ($widgets->countsubcat ? $postcount = $rs->nb_total : $postcount = $rs->nb_post);
                    $class = ' class="cat' . $rs->cat_id;
                    if ((App::url()->type == 'category' && App::frontend()->context()->categories instanceof record && App::frontend()->context()->categories->cat_id == $rs->cat_id)
                    || (App::url()->type == 'post' && App::frontend()->context()->posts instanceof record && App::frontend()->context()->posts->cat_id == $rs->cat_id)) {
                        $class .= ' category-current';
                    }
                    $class .= '"';
                    if ($rs->level > $level) {
                        $cat_level += 1;
                        $res .= str_repeat("\n" . '<ul>' . "\n" . '<li' . $class . '>', $rs->level - $level);
                    } elseif ($rs->level < $level) {
                        $cat_level -= 1;
                        $res .= str_repeat('</li>' . "\n" . '</ul>', -($rs->level - $level));
                    }
                    if ($rs->level <= $level) {
                        $res .= '</li>' . "\n" . '<li' . $class . '>';
                    }
                    $res .= '<a href="' . App::blog()->url . App::url()->getBase('category') . '/' . $rs->cat_url . '"';
                    if ($widgets->showcount == 'showballoon') {
                        $res .= ' title="' . $postcount . '"';
                    }
                    $res .= '>' . Html::escapeHTML(__($rs->cat_title)) . '</a>';
                    if ($widgets->showcount == 'showafter') {
                        $res .= ' (' . $postcount . ')';
                    }
                    $level = $rs->level;
                }
            }
        }
        if ($ref_level - $level < 0) {
            $res .= str_repeat('</li>' . "\n" . '</ul>', -($ref_level - $level));
        }

        return $widgets->renderDiv((bool) $widgets->content_only, 'categories mrvbsubcategories ' . $widgets->CSSclass, '', $res);
    }

    public static function mrvbSubscribe(WidgetsElement $widgets)
    {
        if ($widgets->offline) {
            return;
        }
        if (!$widgets->checkHomeOnly(App::url()->type)) {
            return '';
        }
        $entriesAtom  = __($widgets->entriesAtom);
        $commentsAtom = __($widgets->commentsAtom);
        $entriesRSS2  = __($widgets->entriesRSS2);
        $commentsRSS2 = __($widgets->commentsRSS2);

        $res = ($widgets->title ? $widgets->renderTitle(Html::escapeHTML($widgets->title)) . "\n" : '');
        if (strlen($entriesAtom) > 0 || strlen($commentsAtom) > 0) {
            $res .= '<ul class="feed-atom">' . "\n";
            $res .= ($entriesAtom ? '<li><a class="feed entries" href="' . App::blog()->url . App::url()->getBase('feed') . '/atom">' . $entriesAtom . '</a></li>' : '');
            $res .= "\n";
            if (App::blog()->settings->system->allow_comments || App::blog()->settings->system->allow_trackbacks) {
                $res .= ($commentsAtom ? '<li><a class="feed comments" href="' . App::blog()->url . App::url()->getBase('feed') . '/atom/comments">' . $commentsAtom . '</a></li>' . "\n" : '');
            }
            $res .= '</ul>' . "\n";
        }
        if (strlen($entriesRSS2) > 0 || strlen($commentsRSS2) > 0) {
            $res .= '<ul class="feed-rss2">' . "\n";
            $res .= ($entriesRSS2 ? '<li><a class="feed entries" href="' . App::blog()->url . App::url()->getBase('feed') . '/rss2">' . $entriesRSS2 . '</a></li>' . "\n" : '');
            if (App::blog()->settings->system->allow_comments || App::blog()->settings->system->allow_trackbacks) {
                $res .= ($commentsRSS2 ? '<li><a class="feed comments" href="' . App::blog()->url . App::url()->getBase('feed') . '/rss2/comments">' . $commentsRSS2 . '</a></li>' . "\n" : '');
            }
            $res .= '</ul>' . "\n";
        }

        return $widgets->renderDiv((bool) $widgets->content_only, 'syndicate mrvbsyndicate ' . $widgets->CSSclass, '', $res);
    }

    public static function mrvb_Tags(WidgetsElement $widgets)
    {
        if ($widgets->offline) {
            return;
        }
        if (!$widgets->checkHomeOnly(App::url()->type)) {
            return '';
        }
        if (App::url()->type == 'post' && App::frontend()->context()->posts instanceof record) {
            App::frontend()->context()->meta = App::meta()->getMetaRecordset(App::frontend()->context()->posts->post_meta, 'tag');
        }
        $separator = __($widgets->separator) . ' ';
        $endlist   = __($widgets->endlist);
        $modlist   = ($widgets->modlist === 'list');
        $limit     = abs((int) $widgets->limit);

        $params = ['meta_type' => 'tag'];

        if ($widgets->limit !== '') {
            $params['limit'] = abs((int) $widgets->limit);
        }

        $rs = App::meta()->computeMetaStats(
            App::meta()->getMetadata($params)
        );
        if ($rs->isEmpty()) {
            return;
        }
        $sort = $widgets->sortby;
        if (!in_array($sort, ['meta_id_lower','count'])) {
            $sort = 'meta_id_lower';
        }
        $order = $widgets->orderby;
        if ($order != 'asc') {
            $order = 'desc';
        }
        $rs->sort($sort, $order);
        $exclude = self::mrvb_ListToArray($widgets->exclude);

        $res = ($widgets->title ? $widgets->renderTitle(Html::escapeHTML($widgets->title)) . "\n" : '');
        ($modlist ? $res .= '<ul>' : $res .= '<p class="list">');
        $res .= "\n";
        while ($rs->fetch()) {
            $class = '';
            if (App::url()->type == 'post' && App::frontend()->context()->posts instanceof record) {
                while (App::frontend()->context()->meta->fetch()) {
                    if (App::frontend()->context()->meta->meta_id == $rs->meta_id) {
                        $class = ' tag-current';

                        break;
                    }
                }
            }
            if (!(in_array($rs->meta_id, $exclude))) {
                if ($modlist) {
                    $res .= '<li' . ($class ? ' class="' . $class . '"' : '') . '><a href="' . App::blog()->url . App::url()->getBase('tag') . '/' . rawurlencode((string) $rs->meta_id) . '" ' . 'class="tag' . $rs->roundpercent . '" ';
                    if ($widgets->showcount == 'showballoon') {
                        $res .= ' title="' . $rs->count . '"';
                    }
                    $res .= '>' . __((string) $rs->meta_id) . '</a>';
                    if ($widgets->showcount == 'showafter') {
                        $res .= ' (' . $rs->count . ')';
                    }
                    $res .= ' </li>';
                } else {
                    $res .= '<a href="' . App::blog()->url . App::url()->getBase('tag') . '/' . rawurlencode((string) $rs->meta_id) . '" ' . 'class="tag' . $rs->roundpercent . $class . '" ';
                    if ($widgets->showcount == 'showballoon') {
                        $res .= ' title="' . $rs->count . '"';
                    }
                    $res .= '>' . __((string) $rs->meta_id) . '</a>';
                    if ($widgets->showcount == 'showafter') {
                        $res .= ' (' . $rs->count . ')';
                    }
                    $res .= '<span class="separator">' . $separator . '</span>';
                }
            }
            $res .= "\n";
        }
        if ($modlist) {
            $res .= '</ul>';
        } else {
            if (strlen($separator) > 0) {
                $res = substr($res, 0, strrpos($res, '<span class="separator">' . $separator)) . '<span class="last-separator">' . $endlist . '</span>';
            }
            $res .= ' </p>';
        }
        $res .= "\n";
        if (App::url()->getBase('tags')) {
            $res .= '<p class="goTags"><a href="' . App::blog()->url . App::url()->getBase('tags') . '">' . Html::escapeHTML(__($widgets->alltagslinktitle)) . '</a></p>';
        }

        return $widgets->renderDiv((bool) $widgets->content_only, 'tags mrvbtags ' . $widgets->CSSclass, '', $res);
    }

    public static function mrvb_Text(WidgetsElement $widgets)
    {
        if ($widgets->offline) {
            return;
        }
        if (!$widgets->checkHomeOnly(App::url()->type)) {
            return '';
        }
        $res = ($widgets->title ? $widgets->renderTitle(Html::escapeHTML($widgets->title)) . "\n" : '') . __($widgets->text);

        return $widgets->renderDiv((bool) $widgets->content_only, 'text mrvbtext ' . $widgets->CSSclass, '', $res);
    }
}
