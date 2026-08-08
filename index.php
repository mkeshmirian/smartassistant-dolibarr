<?php
/* Copyright (C) 2026 Integmia <contact@integmia.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    index.php
 * \ingroup smartassistant
 * \brief   Smart Assistant dashboard page (embeds the hosted app)
 */

// Load Dolibarr environment
$res = 0;
if (file_exists('../main.inc.php')) { $res = @include '../main.inc.php'; }
if (!$res && file_exists('../../main.inc.php')) { $res = @include '../../main.inc.php'; }
if (!$res && file_exists('../../../main.inc.php')) { $res = @include '../../../main.inc.php'; }
if (!$res) { die('Include of main fails'); }

$langs->load('smartassistant@smartassistant');

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

if (!$user->rights->smartassistant->read) {
	accessforbidden();
}

$token = dolibarr_get_const($db, 'SMARTASSISTANT_TOKEN', $conf->entity);
$hostedUrl = dolibarr_get_const($db, 'SMARTASSISTANT_HOSTED_URL', $conf->entity);
if (empty($hostedUrl)) { $hostedUrl = 'https://dolibarr.smartassistant.site'; }

// Effective dark-mode setting (global or per-user): 0=disabled, 1=according to
// browser, 2=always enabled - passed to the dashboard so it matches the CRM look.
$darkMode = (int) getDolGlobalInt('THEME_DARKMODEENABLED');

// Self-heal the top-bar menu icon: the menu 'prefix' is written to llx_menu
// when the module is ENABLED, so file-only upgrades can leave a stale icon.
// Rewrites the DB entry to the canonical icon whenever it differs (icon file,
// alignment, or future tweaks) - no re-enable needed.
$menuIcon = '<span class=""><img src="'.(defined('DOL_URL_ROOT') ? DOL_URL_ROOT : '').'/custom/smartassistant/img/smartassistant-white.png" style="height:16px;width:16px;vertical-align:text-bottom;margin-top:2px" alt=""></span>';
$resql = $db->query('SELECT rowid, prefix FROM '.MAIN_DB_PREFIX.'menu WHERE module = '.$db->escape('smartassistant').' AND type = '.$db->escape('top').' AND entity IN (0, '.((int) $conf->entity).')');
if ($resql) {
	while ($obj = $db->fetch_object($resql)) {
		if ($obj->prefix !== $menuIcon) {
			$db->query('UPDATE '.MAIN_DB_PREFIX.'menu SET prefix = '.$db->escape($menuIcon).' WHERE rowid = '.((int) $obj->rowid));
		}
	}
}

llxHeader('', $langs->trans('SmartAssistant'));

if (empty($token)) {
	print '<div class="warning">'.$langs->trans('SmartAssistantNotConnected')
		.' <a href="'.DOL_URL_ROOT.'/custom/smartassistant/admin/setup.php">'.$langs->trans('SmartAssistantGoToSetup').'</a></div>';
} else {
	// Embed the hosted dashboard. The token is scoped to this Dolibarr instance;
	// TODO(security): exchange the stored token for a short-lived session token
	// server-side instead of passing it in the iframe URL (log leakage risk).
	$iframeUrl = $hostedUrl.'/app/dolibarr'
		.'?token='.urlencode($token)
		.'&theme='.urlencode($conf->theme) // effective theme (global or per-user override) → dashboard matches CRM look
		.'&dark='.$darkMode // THEME_DARKMODEENABLED: 0/1/2 → off/according-to-browser/always
		.'&user_id='.((int) $user->id)
		.'&lang='.urlencode($langs->getDefaultLang());
	print '<iframe src="'.htmlentities($iframeUrl).'" style="width:100%;height:calc(100vh - 180px);border:0;border-radius:8px;"></iframe>';
}

llxFooter();
