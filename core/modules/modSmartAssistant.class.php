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
 * \file    htdocs/custom/smartassistant/core/modules/modSmartAssistant.class.php
 * \ingroup smartassistant
 * \brief   Description and activation file for module Smart Assistant
 */

include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 * Class descriptor of the Smart Assistant module.
 *
 * Module ID range 194000-194019 claimed on the Dolibarr wiki (2026-08-03):
 * https://wiki.dolibarr.org/index.php/List_of_modules_id
 */
class modSmartAssistant extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions.
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;

		$this->db = $db;
		parent::__construct($db);

		// Module unique id (reserved range: 194000-194019)
		$this->numero = 194000;

		$this->rights_class = 'smartassistant';
		$this->family = 'other';
		$this->module_position = 55;
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		$this->description = "SmartAssistantModuleDesc";
		$this->descriptionlong = "SmartAssistantModuleLongDesc";
		$this->editor_name = 'Integmia';
		$this->editor_url = 'https://dolibarr.smartassistant.site';
		$this->version = '1.0.0';
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->picto = 'smartassistant@smartassistant';
		$this->module_parts = array('menus' => 1, 'theme' => 0, 'tpl' => 0, 'hooks' => 0, 'moduleforexternal' => 0, 'css' => 0, 'js' => 0, 'models' => 0, 'docs' => 0);
		$this->dirs = array('/smartassistant');
		$this->config_page_url = array('setup.php@smartassistant');
		$this->langfiles = array('smartassistant@smartassistant');
		$this->dependencies = array();
		$this->depends = array();
		$this->requiredby = array();
		$this->conflictwith = array();
		$this->phpmin = array(7, 0);
		$this->need_dolibarr_version = array(18, 0);
		$this->canbeunconfigured = true;

		// Constants defined by module (none for now; tokens are stored at runtime in llx_const)
		$this->const = array();

		// Tabs on object sheets (none for now)
		$this->tabs = array();

		// Boxes (none for now)
		$this->boxes = array();

		// Permissions
		$this->rights = array(
			0 => array(
				'fk_permission' => 0,
				'type' => 'r',
				'langfile' => 'smartassistant@smartassistant',
				'perms' => 'read',
				'label' => 'SmartAssistantRead',
				'default' => 1,
			),
			1 => array(
				'fk_permission' => 0,
				'type' => 'r',
				'langfile' => 'smartassistant@smartassistant',
				'perms' => 'setup',
				'label' => 'SmartAssistantSetup',
				'default' => 1,
			),
		);

		// Menu entries: one top menu + dashboard/setup sub-menus
		$this->menu = array(
			0 => array(
				'fk_menu' => 'fk_mainmenu=home',
				'type' => 'top',
				'titre' => 'SmartAssistantMenu',
				'mainmenu' => 'smartassistant',
				'leftmenu' => 'smartassistant',
				'url' => '/smartassistant/index.php',
				'langs' => 'smartassistant@smartassistant',
				'position' => 100,
				'enabled' => '$conf->smartassistant->enabled',
				'user' => '0',
				'target' => '',
			),
			1 => array(
				'fk_menu' => 'fk_mainmenu=smartassistant',
				'type' => 'left',
				'titre' => 'SmartAssistantMenuDashboard',
				'mainmenu' => 'smartassistant',
				'leftmenu' => 'smartassistant_dashboard',
				'url' => '/smartassistant/index.php',
				'langs' => 'smartassistant@smartassistant',
				'position' => 10,
				'enabled' => '$conf->smartassistant->enabled',
				'user' => '0',
				'target' => '',
			),
			2 => array(
				'fk_menu' => 'fk_mainmenu=smartassistant',
				'type' => 'left',
				'titre' => 'SmartAssistantMenuSetup',
				'mainmenu' => 'smartassistant',
				'leftmenu' => 'smartassistant_setup',
				'url' => '/smartassistant/admin/setup.php',
				'langs' => 'smartassistant@smartassistant',
				'position' => 20,
				'enabled' => '$conf->smartassistant->enabled',
				'user' => '0',
				'target' => '',
			),
		);
	}

	/**
	 * Function called when module is enabled.
	 *
	 * @return int 1 if OK, -1 if KO
	 */
	public function init()
	{
		$sql = array();

		$this->tables = array();
		$this->module_tables = array();
		$this->module_const = array();

		$result = $this->load_tables();
		if ($result < 0) {
			return -1;
		}

		$result = $this->load_menus();
		if ($result < 0) {
			return -1;
		}

		return 1;
	}

	/**
	 * Function called when module is disabled.
	 *
	 * @return int 1 if OK, -1 if KO
	 */
	public function uninstall()
	{
		$sql = array();

		$this->tables = array();
		$this->module_tables = array();
		$this->module_const = array();

		$result = $this->load_tables();
		if ($result < 0) {
			return -1;
		}

		$result = $this->load_menus();
		if ($result < 0) {
			return -1;
		}

		return 1;
	}
}
