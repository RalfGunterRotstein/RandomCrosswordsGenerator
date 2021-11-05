<?php
/**
 * Represents a position with no character in the Crossword, not surrounded by answers.
 * 
 * Requires the following files:
 * 1. outer-square.html; and
 * 2. Square.php.
 * 
 * Example of usage:
 * $square = new OuterSquare(0, 0);
 * $crossword->insertSquare($square);
 * $square->SpreadOuterSquares($crossword);
 * 
 * $content = $square->getHtml();
 * echo $content;
 *
 * @author Ralf Gunter Rotstein <ralfrotstein@gmail.com>
 * @copyright Copyright (c) 2021, Ralf Gunter Rotstein
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 * 
 * @category RandomCrosswordsGenerator
 * @package RandomCrosswordsGenerator_Squares
 * @version 1.0.0
 */

namespace random_crosswords_generator;

require_once('Square.php');

class OuterSquare extends Square {
    /**
     * True means the Square under is a FilledSquare.
     * @var boolean
     */
    private $upFromFilledSquareClass = false;
    
    /**
     * True means the left Square under is a FilledSquare.
     * @var boolean
     */
    private $rightFromFilledSquareClass = false;
    
    /**
     * True means the Square above is a FilledSquare.
     * @var boolean
     */
    private $downFromFilledSquareClass = false;
    
    /**
     * True means the right Square under is a FilledSquare.
     * @var boolean
     */
    private $leftFromFilledSquareClass = false;

	/**
	 * Calls the parent's constructor.
	 *
	 * @param integer $vertical_position Y position in the Crossword.
	 * @param integer $horizontal_position X position in the Crossword.
	 * @return self
	 */
	public function __construct($vertical_position, $horizontal_position) {
		parent::__construct($vertical_position, $horizontal_position);
	}


	
    /**
     * Creates OuterSquares in all adjacent positions where there is no Square.
     * 
     * If there is an adjacent FilledSquare or SpaceMarkerSquare, registers this Square's relation with it.
     *
     * @param Crossword $crossword Board to which this Square belongs.
     * @return void
     */
    public function SpreadOuterSquares($crossword) {
        $neighbour_directions = $this->neighbourDirections(); // ex. up
        $vertical_positions = $this->neighbourVerticalPositions(); // ex. -1
        $horizontal_positions = $this->neighbourHorizontalPositions(); // ex. 0

        for ($i=0; $i < count($neighbour_directions); $i++)
            if ($crossword->positionIsInsideLimits($vertical_positions[$i], $horizontal_positions[$i]))
                $this->createOuterSquareAtPosition($vertical_positions[$i], $horizontal_positions[$i], $crossword) ||
                $this->addRelationToFilledSquare($crossword->getSquare($vertical_positions[$i], $horizontal_positions[$i]), $neighbour_directions[$i]);
    }



    /**
     * Name of each neighbour direction.
     * @return string[]
     */
    private function neighbourDirections() {
        return [
            "up",
            "right",
            "down",
            "left"
        ];
    }

    /**
     * Vertical position of each neighbour direction.
     * @return string[]
     */
    private function neighbourVerticalPositions() {
        $vertical_position = $this->getVerticalPosition();

        return [
            $vertical_position - 1,
            $vertical_position,
            $vertical_position + 1,
            $vertical_position
        ];
    }

    /**
     * Horizontal position of each neighbour direction.
     * @return string[]
     */
    private function neighbourHorizontalPositions() {
        $horizontal_position = $this->getHorizontalPosition();

        return [
            $horizontal_position,
            $horizontal_position + 1,
            $horizontal_position,
            $horizontal_position - 1
        ];
    }
    


    /**
     * Creates an OuterSquare and spreads it.
	 *
	 * @param integer $vertical_position Y position in the Crossword.
	 * @param integer $horizontal_position X position in the Crossword.
     * @param [type] $crossword Board to which this Square belongs.
     * @return OuterSquare|null May be used as a boolean.
     */
    private function createOuterSquareAtPosition($vertical_position, $horizontal_position, $crossword) {
        if (!$crossword->squareExists($vertical_position, $horizontal_position)) {
            $outerSquare = new OuterSquare($vertical_position, $horizontal_position);

            $crossword->insertSquare($outerSquare);
            $outerSquare->SpreadOuterSquares($crossword);

            return $outerSquare;
        }
        else
            return null;
    }
    
    /**
     * Register if this OuterSquare is up/right/down/left of an answer's character.
     *
     * @param Square $existing_square Square with which there is a relation.
     * @param string $filled_square_relative_position Name of each neighbour direction.
     * @return void
     */
    private function addRelationToFilledSquare($existing_square, $filled_square_relative_position) {
        if ($existing_square instanceof FilledSquare ||
            $existing_square instanceof SpaceMarkerSquare)
            switch ($filled_square_relative_position) {
                case 'up':
                    $this->downFromFilledSquareClass = true;
                    break;
                case 'right':
                    $this->leftFromFilledSquareClass = true;
                    break;
                case 'down':
                    $this->upFromFilledSquareClass = true;
                    break;
                case 'left':
                    $this->rightFromFilledSquareClass = true;
                    break;
            }
    }



	/**
	 * Square's HTML whose variables must be replaced by their values.
	 * @return string Raw HTML from file.
	 */
    protected function getHtmlFile() { return file_get_contents(__DIR__ . "/../../../view/squares/outer-square.html"); }

	/**
	 * Variables to replace and their respective values.
	 * @return string[] "{{key}}" => "value"
	 */
	protected function getHtmlVariables() {
		$html_variables = parent::getHtmlVariables();

		$specific_html_variables = [
				"{{up-from-filled-square}}" => $this->getUpFromFilledSquareHtmlClass(),
				"{{right-from-filled-square}}" => $this->getRightFromFilledSquareHtmlClass(),
				"{{down-from-filled-square}}" => $this->getDownFromFilledSquareHtmlClass(),
				"{{left-from-filled-square}}" => $this->getLeftFromFilledSquareHtmlClass()
		];

        $all_html_variables = array_merge($html_variables, $specific_html_variables);
		
		return $all_html_variables;
	}


    
    /**
     * Identifies the Square's HTML as beying the neighbour above a FilledSquare.
     * @return string A class to add to the HTML object, or empty.
     */
    private function getUpFromFilledSquareHtmlClass() {
        return $this->upFromFilledSquareClass ?
            "up-from-filled-square" :
            "";
    }

    /**
     * Identifies the Square's HTML as beying the right neighbour of a FilledSquare.
     * @return string A class to add to the HTML object, or empty.
     */
    private function getRightFromFilledSquareHtmlClass() {
        return $this->rightFromFilledSquareClass ?
            "right-from-filled-square" :
            "";
    }

    /**
     * Identifies the Square's HTML as beying the neighbour under a FilledSquare.
     * @return string A class to add to the HTML object, or empty.
     */
    private function getDownFromFilledSquareHtmlClass() {
        return $this->downFromFilledSquareClass ?
            "down-from-filled-square" :
            "";
    }

    /**
     * Identifies the Square's HTML as beying the left neighbour of a FilledSquare.
     * @return string A class to add to the HTML object, or empty.
     */
    private function getLeftFromFilledSquareHtmlClass() {
        return $this->leftFromFilledSquareClass ?
            "left-from-filled-square" :
            "";
    }
}
?>