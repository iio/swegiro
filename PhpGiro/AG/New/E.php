<?php
/**
 * This file is part of PhpGiro.
 *
 * PhpGiro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpGiro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpGiro.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @copyright Copyright (c) 2011, Hannes Forsgård
 * @license http://www.gnu.org/licenses/ GNU Public License
 *
 * @package PhpGiro
 */


/**
 * AG layout E, feedback on new and removed consents. New file layout.
 * @package PhpGiro
 */
class PhpGiro_AG_New_E extends PhpGiro_AG_E {

	/**
	 * @param string $customerNr
	 * @param string $bg
	 */
	public function __construct($customerNr=false, $bg=false){
		parent::__construct($customerNr, $bg);
		$this->map['01'] = array("/^01AUTOGIRO.{12}..(\d{8}).{12}AG-MEDAVI.{11}(\d{6})(\d{10})\s*$/", 'parseHeadDateCustBg');
	}

}

?>
