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
 * \file    admin/setup.php
 * \ingroup smartassistant
 * \brief   Smart Assistant setup page: connect this Dolibarr to the hosted service
 */

// Load Dolibarr environment
$res = 0;
if (file_exists('../main.inc.php')) { $res = @include '../main.inc.php'; }
if (!$res && file_exists('../../main.inc.php')) { $res = @include '../../main.inc.php'; }
if (!$res && file_exists('../../../main.inc.php')) { $res = @include '../../../main.inc.php'; }
if (!$res) { die('Include of main fails'); }

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

$langs->load('admin');
$langs->load('smartassistant@smartassistant');

if (!$user->admin) {
	accessforbidden();
}

$action = GETPOST('action', 'aZ09');

// Current stored values
$hostedUrl = dolibarr_get_const($db, 'SMARTASSISTANT_HOSTED_URL', $conf->entity);
if (empty($hostedUrl)) { $hostedUrl = 'https://dolibarr.smartassistant.site'; }
$token = dolibarr_get_const($db, 'SMARTASSISTANT_TOKEN', $conf->entity);

// ---- Actions ----
if ($action == 'save') {
	$newHostedUrl = trim(GETPOST('hosted_url', 'none'));
	$dolibarrUrl = trim(GETPOST('dolibarr_url', 'none'));
	$dolibarrApiKey = trim(GETPOST('dolibarr_api_key', 'none'));

	// Basic validation: URLs must be http(s)
	if (!filter_var($newHostedUrl, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $newHostedUrl)) {
		setEventMessages($langs->trans('SmartAssistantInvalidUrl'), null, 'errors');
	} elseif (!filter_var($dolibarrUrl, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $dolibarrUrl)) {
		setEventMessages($langs->trans('SmartAssistantInvalidUrl'), null, 'errors');
	} elseif (empty($dolibarrApiKey)) {
		setEventMessages($langs->trans('SmartAssistantMissingApiKey'), null, 'errors');
	} else {
		dolibarr_set_const($db, 'SMARTASSISTANT_HOSTED_URL', $newHostedUrl, 'chaine', 0, '', $conf->entity);

		// Register this instance with the hosted service → get a token we control
		$payload = json_encode(array(
			'dolibarr_url' => $dolibarrUrl,
			'api_key' => $dolibarrApiKey,
			'module_version' => '1.0.2',
			'dolibarr_version' => DOL_VERSION,
			'entity' => $conf->entity,
		));

		$ch = curl_init($newHostedUrl.'/api/dolibarr/register');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlError = curl_error($ch);
		curl_close($ch);

		$json = json_decode($response, true);
		if ($httpCode == 200 && is_array($json) && !empty($json['token'])) {
			dolibarr_set_const($db, 'SMARTASSISTANT_TOKEN', $json['token'], 'chaine', 0, '', $conf->entity);
			// TODO: store token encrypted via Dolibarr mechanism if available; also persist dolibarr_url/api_key for status display
			dolibarr_set_const($db, 'SMARTASSISTANT_DOLIBARR_URL', $dolibarrUrl, 'chaine', 0, '', $conf->entity);
			setEventMessages($langs->trans('SmartAssistantConnected'), null, 'mesgs');
			$token = $json['token'];
		} else {
			$errMsg = !empty($json['error']) ? $json['error'] : ($curlError ? $curlError : 'HTTP '.$httpCode);
			setEventMessages($langs->trans('SmartAssistantConnectFailed', $errMsg), null, 'errors');
		}
	}
} elseif ($action == 'disconnect') {
	dolibarr_del_const($db, 'SMARTASSISTANT_TOKEN', $conf->entity);
	$token = '';
	setEventMessages($langs->trans('SmartAssistantDisconnected'), null, 'mesgs');
}

llxHeader('', $langs->trans('SmartAssistantSetupTitle'));

print load_fiche_titre($langs->trans('SmartAssistantSetupTitle'), '', 'title_setup');

$form = new Form($db);

print '<div class="fichecenter">';
print '<div class="fichehalfleft">';

// Connection status
print '<table class="noborder noshadow" width="100%">';
print '<tr class="liste_titre"><td colspan="2">'.$langs->trans('SmartAssistantStatus').'</td></tr>';
if (empty($token)) {
	print '<tr><td colspan="2"><div class="warning">'.$langs->trans('SmartAssistantNotConnected').'</div></td></tr>';
} else {
	print '<tr><td>'.$langs->trans('SmartAssistantStatus').'</td><td><span class="badge badge-status1">'.$langs->trans('SmartAssistantConnectedShort').'</span></td></tr>';
}
print '</table><br>';

// Setup form
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="save">';

print '<table class="noborder noshadow" width="100%">';
print '<tr class="liste_titre"><td colspan="2">'.$langs->trans('SmartAssistantConnectionSettings').'</td></tr>';

print '<tr><td>'.$langs->trans('SmartAssistantHostedUrl').'</td><td><input type="text" name="hosted_url" value="'.htmlentities($hostedUrl).'" size="50"></td></tr>';
print '<tr><td>'.$langs->trans('SmartAssistantDolibarrUrl').'</td><td><input type="text" name="dolibarr_url" value="'.htmlentities(dolibarr_get_const($db, 'SMARTASSISTANT_DOLIBARR_URL', $conf->entity)).'" size="50" placeholder="https://your-dolibarr.example.com"></td></tr>';
print '<tr><td>'.$langs->trans('SmartAssistantDolibarrApiKey').'</td><td><input type="password" name="dolibarr_api_key" value="" size="50" autocomplete="off"></td></tr>';

print '</table><br>';

print '<div class="center">';
print '<input type="submit" class="button" value="'.$langs->trans('SmartAssistantConnect').'">';
if (!empty($token)) {
	print ' <a class="button" href="'.$_SERVER['PHP_SELF'].'?action=disconnect&token='.newToken().'">'.$langs->trans('SmartAssistantDisconnect').'</a>';
}
print '</div>';

print '</form>';

print '</div>';
print '<div class="fichehalfright"><div class="ficheaddleft">';
print $langs->trans('SmartAssistantSetupHelp');
print '</div></div>';
print '</div>';

llxFooter();
