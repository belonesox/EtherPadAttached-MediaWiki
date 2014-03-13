<?php

/**
 * MediaWiki EtherPadAttached extension
 * Version 1.4 compatible with MediaWiki 1.16 and Vector skin
 *
 * Copyright © 2008-2011 Stas Fomin
 * http://wiki.4intra.net/EtherPadAttached
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

/**
 * INSTALLATION:
 *   Put require_once("$IP/extensions/EtherPadAttached/EtherPadAttached.php"); into your LocalSettings.php
 *
 * FEATURES:
 */

if (!defined('MEDIAWIKI'))
{
    ?>
<p>This is the EtherPadAttached extension. To enable it, put </p>
<pre>require_once("$IP/extensions/EtherPadAttached/EtherPadAttached.php");</pre>
<p>at the bottom of your LocalSettings.php.</p>
    <?php
    exit(1);
}

$wgHooks['SkinTemplateToolboxEnd'][]     = 'EtherPadAttached::SkinTemplateToolboxEnd';

$wgExtensionMessagesFiles['EtherPadAttached'] = dirname(__FILE__).'/EtherPadAttached.i18n.php';
$wgExtensionFunctions[] = 'EtherPadAttached::Setup';
$wgExtensionCredits['other'][] = array(
    'name'        => 'EtherPadAttached',
    'author'      => 'Stas Fomin',
    'version'     => EtherPadAttached::$version,
    'description' => 'Adds two new actions for pages: go to attached pad or attached sheet',
    'url'         => 'http://wiki.4intra.net/EtherPadAttached',
);

$wgEtherPadAttachedPadUrl  = "";
$wgEtherPadAttachedCalcUrl = "";
$wgEtherPadAttachedDrawUrl = "";

class EtherPadAttached
{
    static $version     = '1.1 (2013-06-06)';
    static $required_mw = '1.18';
    static $actions     = NULL;
    static $css         = '';

    static function Setup()
    {
        // A current MW-Version is required so check for it...
        wfUseMW(self::$required_mw);
    }


    // Output our TOOLBOX links
    static function SkinTemplateToolboxEnd($tpl)
    {
        self::fillActions();
        foreach (array('go2pad', 'go2calc', 'go2draw') as $link)
            if (!empty(self::$actions[$link]))
                print '<li id="t-'.$link.'" title="'.
                    htmlspecialchars(self::$actions[$link]['tooltip']).
                    '"><a href="'.self::$actions[$link]['href'].'">'.
                    htmlspecialchars(self::$actions[$link]['text']).
                    '</a></li>';
        return true;
    }

    //// non-hooks ////

    // fills self::$actions for current title
    static function fillActions()
    {
        // Actions already filled?
        if (self::$actions !== NULL)
            return true;

        global $wgEtherPadAttachedPadUrl;
        global $wgEtherPadAttachedCalcUrl;
        global $wgEtherPadAttachedDrawUrl;

        self::$actions = array();

        global $wgTitle, $wgRequest, $egDocexportCleanHtmlParams;

        $disallow_actions = array('edit', 'submit'); // disallowed actions
        $action = $wgRequest->getVal('action');
        $current_ns = $wgTitle->getNamespace();

        // Disable for special pages
        if ($current_ns < 0)
            return false;

        // Disable for edit/preview
        if (in_array($action, $disallow_actions))
            return false;

        if (function_exists("wfLoadExtensionMessages")){
            wfLoadExtensionMessages('EtherPadAttached');
            // Just for compatibility with older versions of MW
            // wfLoadExtensionMessages need to be removed
        }

        if (strlen($wgEtherPadAttachedPadUrl)>1){
            $padurl = $wgEtherPadAttachedPadUrl . 'article-' . $wgTitle->getArticleID()
                                    . '?monospaced-font=true'
                                    . '&showChat=false&showLineNumbers=false&showControls=false';

            self::$actions['go2pad'] = array(
                'text' => wfMsg('etherpad-link' ),
                'tooltip' => wfMsg('tooltip-etherpad-link'),
                'href' => $padurl,
                'class' => '',
            );
        }

        if (strlen($wgEtherPadAttachedCalcUrl)>1){
            $calcurl = $wgEtherPadAttachedCalcUrl . 'article-' . $wgTitle->getArticleID();

            self::$actions['go2calc'] = array(
                'text' => wfMsg('ethercalc-link' ),
                'tooltip' => wfMsg('tooltip-ethercalc-link'),
                'href' => $calcurl,
                'class' => '',
            );
        }

        if (strlen($wgEtherPadAttachedDrawUrl)>1){
            $drawurl = $wgEtherPadAttachedDrawUrl . 'article-' . $wgTitle->getArticleID();

            self::$actions['go2draw'] = array(
                'text' => wfMsg('etherdraw-link' ),
                'tooltip' => wfMsg('tooltip-etherdraw-link'),
                'href' => $drawurl,
                'class' => '',
            );
        }
        return true;
    }
}
