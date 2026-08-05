<?php
/* Copyright (C) 2026 Integmia <contact@integmia.example>
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
		.'&user_id='.((int) $user->id)
		.'&lang='.urlencode($langs->getDefaultLang());
	print '<iframe src="'.htmlentities($iframeUrl).'" style="width:100%;height:calc(100vh - 180px);border:0;border-radius:8px;"></iframe>';
}

llxFooter();
