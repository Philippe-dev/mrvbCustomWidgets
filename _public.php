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
require_once dirname(__FILE__) . '/_widgets.php';

function mrvb_ListToArray($list)
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
            $t[$i] = html::clean(trim($t[$i]));
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

class tplMrvbWidgets
{
    public static function mrvb_Categories($w)
    {
        if ($w->offline) {
            return;
        }
        if (($w->homeonly == 1 && dcCore::app()->url->type != 'default') || ($w->homeonly == 2 && dcCore::app()->url->type == 'default')) {
            return;
        }
        $rs = dcCore::app()->blog->getCategories();
        if ($rs->isEmpty()) {
            return;
        }
        $separator = __($w->separator) . ' ';
        $endlist   = __($w->endlist);
        $modlist   = ($w->modlist === 'list');
        $exclude   = mrvb_ListToArray($w->excludeID);

        $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) . "\n" : '');
        ($modlist ? $res .= '<ul>' : $res .= '<p class="list">');
        $res .= "\n";
        while ($rs->fetch()) {
            if (!(in_array($rs->cat_id, $exclude))) {
                ($w->countsubcat ? $postcount = $rs->nb_total : $postcount = $rs->nb_post);
                $class = ' class="cat' . $rs->cat_id;
                if ((dcCore::app()->url->type == 'category' && dcCore::app()->ctx->categories instanceof record && dcCore::app()->ctx->categories->cat_id == $rs->cat_id)
                || (dcCore::app()->url->type == 'post' && dcCore::app()->ctx->posts instanceof record && dcCore::app()->ctx->posts->cat_id == $rs->cat_id)) {
                    $class .= ' category-current';
                }
                $class .= '"';
                if ($modlist) {
                    $res .= '<li' . $class . '><a href="' . dcCore::app()->blog->url . dcCore::app()->url->getBase('category') . '/' . $rs->cat_url . '"';
                    if ($w->showcount == 'showballoon') {
                        $res .= ' title="' . $postcount . '"';
                    }
                    $res .= '>' . html::escapeHTML(__($rs->cat_title)) . '</a>';
                    if ($w->showcount == 'showafter') {
                        $res .= ' (' . $postcount . ')';
                    }
                    $res .= ' </li>';
                } else {
                    $res .= '<a' . $class . ' href="' . dcCore::app()->blog->url . dcCore::app()->url->getBase('category') . '/' . $rs->cat_url . '"';
                    if ($w->showcount == 'showballoon') {
                        $res .= ' title="' . $postcount . '"';
                    }
                    $res .= '>' . html::escapeHTML(__($rs->cat_title)) . '</a>';
                    if ($w->showcount == 'showafter') {
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

        return $w->renderDiv($w->content_only, 'categories mrvbcategories ' . $w->CSSclass, '', $res);
    }

    public static function mrvb_feed($w)
    {
        if (($w->offline) || (!$w->url)) {
            return;
        }

        if (($w->homeonly == 1 && dcCore::app()->url->type != 'default') || ($w->homeonly == 2 && dcCore::app()->url->type == 'default')) {
            return;
        }

        try {
            $feed = feedReader::quickParse($w->url, DC_TPL_CACHE);
            if ($feed == false || count($feed->items) == 0) {
                return;
            }
        } catch (Exception $e) {
            return;
        }
        $limit     = abs((int) $w->limit);
        $formdate  = $w->formdate;
        $formitem  = $w->formitem;
        $separator = ' ' . substr($w->separator, 0, 1) . ' ';
        setlocale(LC_ALL, '');

        $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) . "\n" : '');
        $res .= '<ul>' . "\n";
        $i = 0;
        foreach ($feed->items as $item) {
            $title = isset($item->title) && strlen(trim($item->title)) ? $item->title : '';
            $link  = isset($item->link)  && strlen(trim($item->link)) ? $item->link : '';
            if (!$link && !$title) {
                continue;
            }
            if (!$title) {
                $title = substr($link, 0, 25) . '...';
            }
            $date = isset($item->pubdate) && strlen(trim($item->pubdate)) ? $item->pubdate : '';
            if ($formdate) {
                $date = mb_convert_encoding(strftime(htmlentities($formdate, ENT_QUOTES, 'UTF-8'), strtotime($date)), 'UTF-8', 'ASCII');
            } else {
                $date = '';
            }
            $url = html::escapeHTML($item->link);
            $li  = $formitem;
            if (strpos($formitem, '%date%') !== false) {
                $li = str_replace('%date%', $date, $li);
            }
            if (strpos($formitem, '%title%') !== false) {
                $li = str_replace('%title%', $link ? '<a href="' . html::escapeHTML($url) . '">' . $title . '</a>' : $title, $li);
            }
            if (strpos($formitem, '%date+title%') !== false) {
                $li = str_replace('%date+title%', $link ? '<a href="' . html::escapeHTML($url) . '">' . $date . $separator . $title . '</a>' : $date . $separator . $title, $li);
            }
            $res .= '<li>' . $li . '</li>' . "\n";
            $i++;
            if ($i >= $limit) {
                break;
            }
        }
        $res .= '</ul>';

        return $w->renderDiv($w->content_only, 'feed mrvbfeed ' . $w->CSSclass, '', $res);
    }

    public static function mrvb_LastComments($w)
    {
        if ($w->offline) {
            return;
        }
        if (($w->homeonly == 1 && dcCore::app()->url->type != 'default') || ($w->homeonly == 2 && dcCore::app()->url->type == 'default')) {
            return;
        }
        $max             = abs((int) $w->limit);
        $params['order'] = 'comment_dt desc';
        $rs              = dcCore::app()->blog->getComments($params);
        if ($rs->isEmpty() || ($max == 0)) {
            return;
        }

        $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) . "\n" : '');
        $res .= '<ul>' . "\n";
        $i = 0;
        while ($rs->fetch()) {
            if (!($w->notme) || (($w->notme) && !($rs->isMe()))) {
                if ($i < $max) {
                    $res .= '<li class="' .
                    ((bool) $rs->comment_trackback ? 'last-tb' : 'last-comment') .
                    '"><a href="' . $rs->getPostURL() . '#c' . $rs->comment_id . '">' .
                    html::escapeHTML($rs->post_title) . ' - ' .
                    html::escapeHTML($rs->comment_author) .
                    '</a></li>' . "\n";
                    ++$i;
                } else {
                    break;
                }
            }
        }
        $res .= '</ul>';

        return $w->renderDiv($w->content_only, 'lastcomments mrvblastcomments ' . $w->CSSclass, '', $res);
    }

    public static function mrvb_LastPosts($w)
    {
        if ($w->offline) {
            return;
        }
        if (($w->homeonly == 1 && dcCore::app()->url->type != 'default') || ($w->homeonly == 2 && dcCore::app()->url->type == 'default')) {
            return;
        }
        $params['order']      = 'post_dt desc';
        $params['no_content'] = true;
        $limit                = abs((int) $w->limit);
        $exclude              = [];
        $typepost             = str_replace(' ', '', $w->typepost);
        if (empty($typepost)) {
            $typepost = 'post';
        }
        $params['post_type'] = explode(',', $typepost);
        if ($w->selected) {
            $params['post_selected'] = true;
        }
        if ($w->category) {
            if ($w->category == 'null') {
                $params['sql'] = ' AND P.cat_id IS NULL ';
            } elseif (is_numeric($w->category)) {
                $params['cat_id'] = (int) $w->category;
            } else {
                $params['cat_url'] = $w->category;
            }
        } else {
            $exclude = mrvb_ListToArray($w->excludeID);
        }
        if ($w->tag) {
            $params['meta_id'] = $w->tag;
            $rs                = dcCore::app()->meta->getPostsByMeta($params);
        } else {
            $rs = dcCore::app()->blog->getPosts($params);
        }
        if ($rs->isEmpty()) {
            return;
        }

        $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) . "\n" : '');
        $res .= '<ul>' . "\n";
        $i = 0;
        while ($rs->fetch()) {
            if (($i < $limit) || ($limit == 0)) {
                if (!(in_array($rs->cat_id, $exclude))) {
                    $class = '';
                    if (dcCore::app()->url->type == 'post' && dcCore::app()->ctx->posts instanceof record && dcCore::app()->ctx->posts->post_id == $rs->post_id) {
                        $class = ' class="post-current"';
                    }
                    $res .= '<li' . $class . '><a href="' . $rs->getURL() . '">' .
                    html::escapeHTML($rs->post_title) . '</a></li>' . "\n";
                    ++$i;
                }
            }
        }
        $res .= '</ul>';

        return $w->renderDiv($w->content_only, 'lastposts mrvblastposts ' . $w->CSSclass, '', $res);
    }

    public static function mrvb_Navigation($w)
    {
        if ($w->offline) {
            return;
        }
        if (($w->homeonly == 1 && dcCore::app()->url->type != 'default') || ($w->homeonly == 2 && dcCore::app()->url->type == 'default')) {
            return;
        }
        $cssID = str_replace(' ', '', html::escapeHTML($w->CSSid));

        $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) . "\n" : '');
        $res .= '<ul>' . "\n";
        if (dcCore::app()->url->type != 'default') {
            $res .= '<li class="topnav-home">' . '<a href="' . dcCore::app()->blog->url . '">' . ($w->home ? $w->home : __('Home')) . '</a><span>' . ($w->separator ? $w->separator : '&nbsp;-&nbsp;') . '</span></li>' . "\n";
        }
        $res .= '<li class="topnav-arch">' . '<a href="' . dcCore::app()->blog->url . dcCore::app()->url->getURLFor('archive') . '">' . ($w->archives ? $w->archives : __('Archives')) . '</a>' . '</li>' . "\n";
        $res .= '</ul>';

        return $w->renderDiv($w->content_only, 'mrvbnavigation ' . $w->CSSclass, ($cssID ? ' id="' . $cssID . '"' : ''), $res);
    }

    public static function mrvb_Pages($w)
    {
        if ($w->offline) {
            return;
        }
        if (($w->homeonly == 1 && dcCore::app()->url->type != 'default') || ($w->homeonly == 2 && dcCore::app()->url->type == 'default')) {
            return;
        }

        $separator               = __($w->separator) . ' ';
        $endlist                 = __($w->endlist);
        $modlist                 = ($w->modlist === 'list');
        $params['post_type']     = 'page';
        $params['limit']         = abs((int) $w->limit);
        $params['no_content']    = true;
        $params['post_selected'] = false;
        $sort                    = $w->sortby;
        if (!in_array($sort, ['post_title','post_position','post_dt'])) {
            $sort = 'post_title';
        }
        $order = $w->orderby;
        if ($order != 'asc') {
            $order = 'desc';
        }
        $params['order'] = $sort . ' ' . $order;
        $rs              = dcCore::app()->blog->getPosts($params);
        if ($rs->isEmpty()) {
            return;
        }

        $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) . "\n" : '');
        ($modlist ? $res .= '<ul>' : $res .= '<p class="list">');
        $res .= "\n";
        $exclude = mrvb_ListToArray($w->excludeID);
        while ($rs->fetch()) {
            $class = '';
            if ((dcCore::app()->url->type == 'pages' && dcCore::app()->ctx->posts instanceof record && dcCore::app()->ctx->posts->post_id == $rs->post_id)) {
                $class = ' class="page-current"';
            }
            if (!(in_array($rs->post_id, $exclude))) {
                if ($modlist) {
                    $res .= '<li' . $class . '><a href="' . $rs->getURL() . '">' . html::escapeHTML(__($rs->post_title)) . '</a></li>' . "\n";
                } else {
                    $res .= '<span' . $class . '><a href="' . $rs->getURL() . '">' . html::escapeHTML(__($rs->post_title)) . '</a></span><span class="separator">' . $separator . '</span>' . "\n";
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

        return $w->renderDiv($w->content_only, 'pages mrvbpages ' . $w->CSSclass, '', $res);
    }

    public static function mrvb_Search($w)
    {
        if ($w->offline) {
            return;
        }
        if (($w->homeonly == 1 && dcCore::app()->url->type != 'default') || ($w->homeonly == 2 && dcCore::app()->url->type == 'default')) {
            return;
        }
        $value = isset($GLOBALS['_search']) ? html::escapeHTML($GLOBALS['_search']) : '';
        $cssID = str_replace(' ', '', html::escapeHTML($w->CSSid));

        $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) . "\n" : '');
        $res .= '<form action="' . dcCore::app()->blog->url . '" method="get">' . "\n" .
        '<fieldset>' . "\n" .
        '<p><input type="text" size="10" maxlength="255" id="q" name="q" value="' . $value . '" /> ' .
        '<input type="submit" class="submit" value="ok" /></p>' . "\n" .
        '</fieldset>' . "\n" .
        '</form>';

        return $w->renderDiv($w->content_only, 'mrvbsearch ' . $w->CSSclass, ($cssID ? 'id="' . $cssID . '"' : ''), $res);
    }

    public static function mrvb_SubCategories($w)
    {
        if ($w->offline) {
            return;
        }
        if (($w->homeonly == 1 && dcCore::app()->url->type != 'default') || ($w->homeonly == 2 && dcCore::app()->url->type == 'default')) {
            return;
        }
        $rs = dcCore::app()->blog->getCategories();
        if ($rs->isEmpty()) {
            return;
        }
        $exclude   = mrvb_ListToArray($w->excludeID);
        $max_level = abs((int) $w->maxlevel);
        if ($max_level == 0) {
            $max_level = 65535;
        }
        $ref_level = $level = $rs->level - 1;
        $cat_level = 0;

        $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) : '');
        while ($rs->fetch()) {
            if (!(in_array($rs->cat_id, $exclude))) {
                if ($rs->level <= $max_level) {
                    ($w->countsubcat ? $postcount = $rs->nb_total : $postcount = $rs->nb_post);
                    $class = ' class="cat' . $rs->cat_id;
                    if ((dcCore::app()->url->type == 'category' && dcCore::app()->ctx->categories instanceof record && dcCore::app()->ctx->categories->cat_id == $rs->cat_id)
                    || (dcCore::app()->url->type == 'post' && dcCore::app()->ctx->posts instanceof record && dcCore::app()->ctx->posts->cat_id == $rs->cat_id)) {
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
                    $res .= '<a href="' . dcCore::app()->blog->url . dcCore::app()->url->getBase('category') . '/' . $rs->cat_url . '"';
                    if ($w->showcount == 'showballoon') {
                        $res .= ' title="' . $postcount . '"';
                    }
                    $res .= '>' . html::escapeHTML(__($rs->cat_title)) . '</a>';
                    if ($w->showcount == 'showafter') {
                        $res .= ' (' . $postcount . ')';
                    }
                    $level = $rs->level;
                }
            }
        }
        if ($ref_level - $level < 0) {
            $res .= str_repeat('</li>' . "\n" . '</ul>', -($ref_level - $level));
        }

        return $w->renderDiv($w->content_only, 'categories mrvbsubcategories ' . $w->CSSclass, '', $res);
    }

    public static function mrvbSubscribe($w)
    {
        if ($w->offline) {
            return;
        }
        if (($w->homeonly == 1 && dcCore::app()->url->type != 'default') || ($w->homeonly == 2 && dcCore::app()->url->type == 'default')) {
            return;
        }
        $entriesAtom  = __($w->entriesAtom);
        $commentsAtom = __($w->commentsAtom);
        $entriesRSS2  = __($w->entriesRSS2);
        $commentsRSS2 = __($w->commentsRSS2);

        $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) . "\n" : '');
        if (strlen($entriesAtom) > 0 || strlen($commentsAtom) > 0) {
            $res .= '<ul class="feed-atom">' . "\n";
            $res .= ($entriesAtom ? '<li><a class="feed entries" href="' . dcCore::app()->blog->url . dcCore::app()->url->getBase('feed') . '/atom">' . $entriesAtom . '</a></li>' : '');
            $res .= "\n";
            if (dcCore::app()->blog->settings->system->allow_comments || dcCore::app()->blog->settings->system->allow_trackbacks) {
                $res .= ($commentsAtom ? '<li><a class="feed comments" href="' . dcCore::app()->blog->url . dcCore::app()->url->getBase('feed') . '/atom/comments">' . $commentsAtom . '</a></li>' . "\n" : '');
            }
            $res .= '</ul>' . "\n";
        }
        if (strlen($entriesRSS2) > 0 || strlen($commentsRSS2) > 0) {
            $res .= '<ul class="feed-rss2">' . "\n";
            $res .= ($entriesRSS2 ? '<li><a class="feed entries" href="' . dcCore::app()->blog->url . dcCore::app()->url->getBase('feed') . '/rss2">' . $entriesRSS2 . '</a></li>' . "\n" : '');
            if (dcCore::app()->blog->settings->system->allow_comments || dcCore::app()->blog->settings->system->allow_trackbacks) {
                $res .= ($commentsRSS2 ? '<li><a class="feed comments" href="' . dcCore::app()->blog->url . dcCore::app()->url->getBase('feed') . '/rss2/comments">' . $commentsRSS2 . '</a></li>' . "\n" : '');
            }
            $res .= '</ul>' . "\n";
        }

        return $w->renderDiv($w->content_only, 'syndicate mrvbsyndicate ' . $w->CSSclass, '', $res);
    }

    public static function mrvb_Tags($w)
    {
        if ($w->offline) {
            return;
        }
        if (($w->homeonly == 1 && dcCore::app()->url->type != 'default') || ($w->homeonly == 2 && dcCore::app()->url->type == 'default')) {
            return;
        }
        if (dcCore::app()->url->type == 'post' && dcCore::app()->ctx->posts instanceof record) {
            dcCore::app()->ctx->meta = dcCore::app()->meta->getMetaRecordset(dcCore::app()->ctx->posts->post_meta, 'tag');
        }
        $separator = __($w->separator) . ' ';
        $endlist   = __($w->endlist);
        $modlist   = ($w->modlist === 'list');
        $limit     = abs((int) $w->limit);

        $params = ['meta_type' => 'tag'];

        if ($w->limit !== '') {
            $params['limit'] = abs((int) $w->limit);
        }

        $rs = dcCore::app()->meta->computeMetaStats(
            dcCore::app()->meta->getMetadata($params)
        );
        if ($rs->isEmpty()) {
            return;
        }
        $sort = $w->sortby;
        if (!in_array($sort, ['meta_id_lower','count'])) {
            $sort = 'meta_id_lower';
        }
        $order = $w->orderby;
        if ($order != 'asc') {
            $order = 'desc';
        }
        $rs->sort($sort, $order);
        $exclude = mrvb_ListToArray($w->exclude);

        $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) . "\n" : '');
        ($modlist ? $res .= '<ul>' : $res .= '<p class="list">');
        $res .= "\n";
        while ($rs->fetch()) {
            $class = '';
            if (dcCore::app()->url->type == 'post' && dcCore::app()->ctx->posts instanceof record) {
                while (dcCore::app()->ctx->meta->fetch()) {
                    if (dcCore::app()->ctx->meta->meta_id == $rs->meta_id) {
                        $class = ' tag-current';

                        break;
                    }
                }
            }
            if (!(in_array($rs->meta_id, $exclude))) {
                if ($modlist) {
                    $res .= '<li' . ($class ? ' class="' . $class . '"' : '') . '><a href="' . dcCore::app()->blog->url . dcCore::app()->url->getBase('tag') . '/' . rawurlencode((string) $rs->meta_id) . '" ' . 'class="tag' . $rs->roundpercent . '" ';
                    if ($w->showcount == 'showballoon') {
                        $res .= ' title="' . $rs->count . '"';
                    }
                    $res .= '>' . __((string) $rs->meta_id) . '</a>';
                    if ($w->showcount == 'showafter') {
                        $res .= ' (' . $rs->count . ')';
                    }
                    $res .= ' </li>';
                } else {
                    $res .= '<a href="' . dcCore::app()->blog->url . dcCore::app()->url->getBase('tag') . '/' . rawurlencode((string) $rs->meta_id) . '" ' . 'class="tag' . $rs->roundpercent . $class . '" ';
                    if ($w->showcount == 'showballoon') {
                        $res .= ' title="' . $rs->count . '"';
                    }
                    $res .= '>' . __((string) $rs->meta_id) . '</a>';
                    if ($w->showcount == 'showafter') {
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
        if (dcCore::app()->url->getBase('tags')) {
            $res .= '<p class="goTags"><a href="' . dcCore::app()->blog->url . dcCore::app()->url->getBase('tags') . '">' . html::escapeHTML(__($w->alltagslinktitle)) . '</a></p>';
        }

        return $w->renderDiv($w->content_only, 'tags mrvbtags ' . $w->CSSclass, '', $res);
    }

    public static function mrvb_Text($w)
    {
        if ($w->offline) {
            return;
        }
        if (($w->homeonly == 1 && dcCore::app()->url->type != 'default') || ($w->homeonly == 2 && dcCore::app()->url->type == 'default')) {
            return;
        }
        $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) . "\n" : '') . __($w->text);

        return $w->renderDiv($w->content_only, 'text mrvbtext ' . $w->CSSclass, '', $res);
    }
}
