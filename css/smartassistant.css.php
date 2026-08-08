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
 * \file    css/smartassistant.css.php
 * \ingroup smartassistant
 * \brief   Global stylesheet override (loaded on every Dolibarr page)
 *
 * Dolibarr themes render the module picto as the top-bar menu item's
 * background-image (rule like div.mainmenu.smartassistant { background-image:
 * url(.../smartassistant.png) }), which shows the black-line picto behind the
 * white-line icon rendered by the menu prefix. Remove that background so only
 * the prefix icon is visible.
 */

header('Content-Type: text/css');
?>
div.mainmenu.smartassistant {
	background-image: none !important;
}
