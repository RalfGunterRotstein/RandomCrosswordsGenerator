<?php
/**
 * Receives the user's input.
 * 
 * Requires the following files:
 * 1. input.html.
 * 
 * Example of usage:
 * $input = new Input($square);
 * $html = $input->getHtml();
 * echo $html;
 *
 * @author Ralf Gunter Rotstein <ralfrotstein@gmail.com>
 * @copyright Copyright (c) 2021, Ralf Gunter Rotstein
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 * 
 * @category RandomCrosswordsGenerator
 * @package RandomCrosswordsGenerator_Squares_InternalElements
 * @version 1.0.0
 */

namespace random_crosswords_generator;

class Input {
	/**
	 * A Square corresponding to a character of the Crossword.
	 * @var FilledSquare
	 */
	private $filled_square = null;

	/**
	 * Sets the properties.
	 * 
	 * @param string $direction "vertical" or "horizontal"
	 * @return self
	 */
	public function __construct($filled_square) { $this->filled_square = $filled_square; }
	
	/**
	 * A HTML's input.
	 * @return string HTML ready to be displayed.
	 */
	public function getHtml() {
		$html = $this->getHtmlFile();
		$html_variables = $this->getHtmlVariables();

		$replaced_html = $this->replaceHtmlVariables($html, $html_variables);

        return $replaced_html;
	}


	
	/**
	 * Input's HTML whose variables must be replaced by their values.
	 * @return string Raw HTML from file.
	 */
	private function getHtmlFile() { return file_get_contents(__DIR__ . "/../../../../view/squares/internal-elements/input.html"); }

	/**
	 * Replaces expected "{{variables}}" for their "values".
	 *
	 * @param string $html HTML with {{variables}} to be replaced.
	 * @param string[] $html_variables Dictionary: {{variable}} => value.
	 * @return string HTML with variables replaced for their values.
	 */
    private function replaceHtmlVariables($html, $html_variables) {
        $html_keys = array_keys($html_variables);
        $html_values = array_values($html_variables);

        $replaced_html = str_replace($html_keys, $html_values, $html);

		return $replaced_html;
	}

	/**
	 * Variables to replace and their respective values.
	 * @return string[] "{{key}}" => "value"
	 */
	private function getHtmlVariables() {
		$variables = [
			"{{vertical-position}}" => $this->getVerticalPosition(),
			"{{horizontal-position}}" => $this->getHorizontalPosition(),
			"{{vertical-class}}" => $this->getVerticalHtmlClass(),
			"{{horizontal-class}}" => $this->getHorizontalHtmlClass()
		];
		
		return $variables;
	}



	/**
	 * The Y position of the FilledSquare to which the Input belongs.
	 * @return integer
	 */
	private function getVerticalPosition() { return $this->filled_square->getVerticalPosition(); }

	/**
	 * The X position of the FilledSquare to which the Input belongs.
	 * @return integer
	 */
	private function getHorizontalPosition() { return $this->filled_square->getHorizontalPosition(); }

    /**
     * Identifies the Input's HTML as beying part of a vertical answer.
     * @return string A class to add to the HTML object, or empty.
     */
	private function getVerticalHtmlClass() {
		return $this->filled_square->containsVerticalDirection() ?
			"vertical" :
			"";
	}

    /**
     * Identifies the Input's HTML as beying part of a horizontal answer.
     * @return string A class to add to the HTML object, or empty.
     */
	private function getHorizontalHtmlClass() {
		return $this->filled_square->containsHorizontalDirection() ?
			"horizontal" :
			"";
	}
}
?>