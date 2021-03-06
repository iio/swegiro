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
 * AG layout E, feedback on new and removed consents.
 * @package PhpGiro
 */
class PhpGiro_AG_E extends PhpGiro_AG_Object {

	/**
	 * Status descriptions
	 * @var array $statusMsgs
	 */
	protected $statusMsgs = array(
		2 => "Medgivandet är makulerat på initiativ av betalaren.",
		3 => "Kontoslaget är inte godkänt för autogiro",
		4 => "Medgivandet saknas i Bankgirots medgivanderegister.",
		5 => "Felaktiga bankkonto- eller personuppgifter.",
		7 => "Medgivandet är makulerat av Bankgirot på grund av obesvarad kontoförfrågan.",
		9 => "Bankgironummret saknas hos Bankgirot.",
		10 => "Medgivandet finns redan upplagt i Bankgirots register eller är under förfrågan.",
		20 => "Felaktigt personnummer.",
		21 => "Felaktigt betalarnummer.",
		23 => "Felaktigt bankkontonummer.",
		29 => "Felaktigt mottagande bankgironummer.",
		30 => "Mottagarbankgironummer saknas.",
		32 => "Nytt medgivande.",
		33 => "Makulerad.",
		98 => "Medgivandet är makulerat på grund av makulerat betalarnummer.",
	);


	/* FILE STRUCTURE */


	/**
	 * Layout id
	 * @var string $layout
	 */
	protected $layout = 'E';


	/**
	 * Regex represention a valid file structure
	 * @var string $struct
	 */
	protected $struct = "/^(01(73)*09)+$/";


	/**
	 * Map transaction codes (TC) to line-parsing regexp and receiving method
	 * @var array $map
	 */
	protected $map = array(
		'01' => array("/^01(\d{8})9900(\d{10})AG-MEDAVI\s*$/", 'parseHeadDateBg'),
		'73' => array("/^73(\d{10})(\d{16})(.{4})(.{12})(.{12}).{5}(\d\d)(\d\d)(\d{8})(\d{0,6})\s*$/", 'parseConsentInfo'),
		'09' => array("/^09(\d{8})9900(\d{7})\s*$/", 'parseFoot'),
	);



	/* PARSING FUNCTIONS */


	/**
	 * TC == 73, register post
	 * @param string $bg
	 * @param string $betNr
	 * @param string $clearing
	 * @param string $account
	 * @param string $orgNr
	 * @param string $info
	 * @param string $status
	 * @param string $date
	 * @param string $validDate
	 * @return bool TRUE on success, FALSE on failure
	 */
	protected function parseConsentInfo($bg, $betNr, $clearing, $account, $orgNr, $info, $status, $date, $validDate=false){
		if ( !$this->validBg($bg) ) return false;
		
		//Set action
		$action = "?";
		switch ( $info ) {
			case "05":
				if ( $status == "33" ) {
					$action = 'D';
					break;
				}
			case "04":
			case "42":
				$action = ( $status=="32" ) ? 'A' : 'E';
				break;

			case "03":
				$action = ( $status=="33" ) ? 'D' : 'E';
				break;
			
			case "10":
			case "43":
			case "44":
			case "46":
				$action = "D";
		}

		//set pers/org number
		self::buildStateIdNr($orgNr, $orNrType);
	
		$consent = array(
			'action' => $action,
			'betNr' => ltrim($betNr, '0'),
			'account' => self::buildAccountNr($betNr, $clearing, $account),
			'info' => (int)$info,
			'status' => (int)$status,
			'statusMsg' => $this->statusMsgs[(int)$status],
			'date' => $date,
			$orNrType => $orgNr,
		);

		//set valid from
		if ( !preg_match("/^[0 ]*$/", $validDate) ) {
			$consent['validFrom'] = $validDate;
		}

		$this->push($consent);
		return true;
	}


	/**
	 * TC == 09, footer layout E style
	 * @param string $date
	 * @param string $nrPosts
	 * @return bool TRUE on success, FALSE on failure
	 */
	protected function parseFoot($date, $nrPosts){
		if ( !$this->validDate($date) ) return false;
		if ( (int)$nrPosts != $this->count() ) {
			$this->error(sprintf(_("Unvalid file content, wrong number of type '%s' posts"), "73"));
			return false;
		}
		$this->writeSection();
		return true;
	}

}

?>
