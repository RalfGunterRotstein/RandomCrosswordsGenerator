<?php
/**
 * Displays the result of the inputs given to a Crossword.
 * 
 * Requires the following files;
 * 1. failure-message.html; and
 * 2. success-message.html.
 * 
 * Requires the following session variables;
 * 1. bidimensionalArrayOfCorrectCharactersForCurrentCrosswords; and
 * 2. sectionNameForDisplay.
 * 
 * Example of usage:
 * $result = new Result();
 * $result->display();
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

class Result {
	/**
	 * Displays an HTML with the result of the user's inputs for the current Crossword.
	 * @return void
	 */
	public function display() { echo $this->getResult(); }

	

	/**
	 * Evaluation of the answers given to the Crossword.
	 * @return string HTML with a success/failure message, or empty.
	 */
	private function getResult() {
		$result = "";

		if ($this->sessionVariablesAreSet()) {
			$wrong_characters_count = $this->getWrongCharactersCount();
	
			if ($wrong_characters_count > 0)
				$result .= $this->getFailureHtml($wrong_characters_count);
			else
				$result .= $this->getSuccessHtml();

			$this->unsetSessionVariables();
		}

		return $result;
	}

	/**
	 * Checks the existence of the necessary session variables.
	 * @return bool
	 * */
	private function sessionVariablesAreSet() {
		return
			isset($_SESSION["bidimensionalArrayOfCorrectCharactersForCurrentCrosswords"]) &&
			isset($_SESSION["sectionNameForDisplay"]);
	}



	/**
	 * Characters given by the user that don't match with the right ones.
	 * @return int
	 */
	private function getWrongCharactersCount() {
		$correct_characters_bidimensional_array = $this->getCorrectCharactersBidimensionalArray();
		$wrong_characters_count = 0;

		foreach ($correct_characters_bidimensional_array as $line_number => $columns_in_line)
			foreach ($columns_in_line as $column_number => $correct_character) {
				$user_character = $this->getUserCharacter($line_number, $column_number);
				
				if ($user_character != $correct_character)
					$wrong_characters_count++;
			}

		return $wrong_characters_count;
	}

	/**
	 * Expected character for each user's input.
	 * @return string[][] One multibyte character for each valid [y][x] position.
	 */
	private function getCorrectCharactersBidimensionalArray() { return $_SESSION["bidimensionalArrayOfCorrectCharactersForCurrentCrosswords"]; }

	/**
	 * Character given by the user in a specific input.
	 * @param int $line_number Y position in the bidimensional array.
	 * @param int $column_number X position in the bidimensional array.
	 * @return string One multibyte character.
	 */
	private function getUserCharacter($line_number, $column_number) { return $_POST[ "input-" . $line_number . "-" . $column_number ]; }


	
	/**
	 * Failure message's HTML with variables replaced for their values.
	 * @param int $wrong_characters_count How many characters the user got wrong.
	 * @return string HTML ready to be displayed.
	 */
	private function getFailureHtml($wrong_characters_count) {
		$html = $this->getFailureHtmlHtmlFile();

		$replaced_html = $this->replaceHtmlVariables($html, $wrong_characters_count);

		return $replaced_html;
	}

	/**
	 * Failure message's HTML whose variables must be replaced by their values.
	 * @return string Raw HTML from file.
	 */
	private function getFailureHtmlHtmlFile() { return file_get_contents(__DIR__ . "/../../../view/results/failure-message.html"); }



	/**
	 * Success message's HTML with variables replaced for their values.
	 * @return string HTML ready to be displayed.
	 */
	private function getSuccessHtml() {
		$html = $this->getSuccessHtmlHtmlFile();

		$replaced_html = $this->replaceHtmlVariables($html, 0);

		return $replaced_html;
	}

	/**
	 * Success message's HTML whose variables must be replaced by their values.
	 * @return string Raw HTML from file.
	 */
	private function getSuccessHtmlHtmlFile() { return file_get_contents(__DIR__ . "/../../../view/results/success-message.html"); }



	/**
	 * Variables to replace and their respective values.
	 *
	 * @param int $wrong_characters_count Number to replace {{wrong-characters}}.
	 * @return string[] "{{key}}" => "value"
	 */
	private function getHtmlVariables($wrong_characters_count) {
		// {{wrong-characters}} character{{plural-string}}: 0 characters, 1 character, 2 characters
		$plural_string = str_repeat("s", $wrong_characters_count!=1);
		
		$html_variables = [
			"{{wrong-characters}}" => $wrong_characters_count,
			"{{plural-string}}" => $plural_string,
			"{{section-name-for-display}}" => $this->getSectionNameForDisplay()
		];

		return $html_variables;
	}

	/**
	 * Replaces expected {{variables}} for their "values".
	 *
	 * @param string $html HTML with {{variables}} to be replaced.
	 * @param int $wrong_characters_count Number to replace {{wrong-characters}}.
	 * @return string HTML with variables replaced for their values.
	 */
	private function replaceHtmlVariables($html, $wrong_characters_count) {
		$html_variables = $this->getHtmlVariables($wrong_characters_count);

		$html_keys = array_keys($html_variables);
		$html_values = array_values($html_variables);

		$replaced_html = str_replace($html_keys, $html_values, $html);

		return $replaced_html;
	}

	/**
	 * Section's name, in case it should be referenced in the result.
	 * @return string
	 */
	private function getSectionNameForDisplay() { return $_SESSION["sectionNameForDisplay"]; }


	
	/**
	 * Clean Crossword's answers.
	 * @return void
	 */
	private function unsetSessionVariables() { unset($_SESSION["bidimensionalArrayOfCorrectCharactersForCurrentCrosswords"]); }
}
?>