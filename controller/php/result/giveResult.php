<?php
/**
 * Displays the Result of the inputs given to the current Crossword.
 * 
 * Requires the following files:
 * 1. Result.php;
 *
 * @author Ralf Gunter Rotstein <ralfrotstein@gmail.com>
 * @copyright Copyright (c) 2021, Ralf Gunter Rotstein
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 * 
 * @category RandomCrosswordsGenerator
 * @package RandomCrosswordsGenerator_Result
 * @version 1.0.0
 */

namespace random_crosswords_generator;
require_once("Result.php");

session_start();

$result = new Result();
$result->display();