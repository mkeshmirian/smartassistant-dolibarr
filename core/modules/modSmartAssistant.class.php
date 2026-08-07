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
	/** @var array<string,string> Module dependencies */
	public $dependencies = array();
	/** @var bool Can be disabled without data loss */
	public $canbeunconfigured = true;

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
		$this->version = '1.0.6';
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

		// Permissions (modern numeric-index format used since Dolibarr 18+
		// [0]=id [1]=label [2]=type [3]=default [4]=perms [5]=subperms)
		// Right ids are in our reserved module range (194000-194019)
		$this->rights = array();
		$r = 0;
		$this->rights[$r][0] = 194001; // id
		$this->rights[$r][1] = 'SmartAssistantRead'; // label
		$this->rights[$r][2] = 'r'; // type (deprecated)
		$this->rights[$r][3] = 1; // default
		$this->rights[$r][4] = 'read'; // perms

		$r++;
		$this->rights[$r][0] = 194002; // id
		$this->rights[$r][1] = 'SmartAssistantSetup'; // label
		$this->rights[$r][2] = 'r'; // type (deprecated)
		$this->rights[$r][3] = 1; // default
		$this->rights[$r][4] = 'setup'; // perms

		// Menu entries: one top menu + dashboard/setup sub-menus
		// Top-bar icon: eldy/augment render the menu 'prefix' — either a Font Awesome
		// class (fa-xxx) or a raw HTML snippet. The top bar is dark, so module icons
		// there use WHITE lines (like the other module icons); the black-line picto
		// (img/smartassistant.png) stays the module picto for light contexts.
		$smartassistantTopIcon = '<span class=""><img src="'.(defined('DOL_URL_ROOT') ? DOL_URL_ROOT : '').'/custom/smartassistant/img/smartassistant-white.png" style="height:16px;width:16px;vertical-align:middle" alt=""></span>';
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
				'prefix' => $smartassistantTopIcon,
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
	 * Delegates to the parent (Dolibarr 18+/23 API): creates menus, rights,
	 * constants, boxes and data directories defined in the constructor.
	 *
	 * @param string $options Options when enabling module ('', 'newboxdefonly', 'noboxes', 'menuonly')
	 * @return int 1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		return parent::init($options);
	}

	/**
	 * Function called when module is disabled.
	 * Delegates to the parent: removes menus, rights, constants and boxes
	 * defined in the constructor. Data directories are not deleted.
	 *
	 * @param string $options Options when disabling module
	 * @return int 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		return parent::remove($options);
	}
}
