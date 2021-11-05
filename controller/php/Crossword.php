<?php
/**
 * Fits words into an interactive board and displays this board.
 * 
 * Requires the following files:
 * 1. crossword.html;
 * 2. Entry.php;
 * 3. OuterSquare.php; and
 * 4. InnerSquare.php.
 * 
 * Example of usage:
 * do {
 * $crossword = new Crossword($max_hor_squares, $initial_max_ver_squares, $answers, $questions);
 * } while ($crossword->getNumberOfEntries() < $min_entries);
 * $crossword->setReadyToUse();
 * 
 * $content = $crossword->getHtml();
 * echo $content;
 *
 * @author Ralf Gunter Rotstein <ralfrotstein@gmail.com>
 * @copyright Copyright (c) 2021, Ralf Gunter Rotstein
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 * 
 * @category RandomCrosswordsGenerator
 * @package RandomCrosswordsGenerator
 * @version 1.0.0
 */

namespace random_crosswords_generator;

require_once('Entry.php');
require_once('squares/OuterSquare.php');
require_once('squares/InnerSquare.php');

class Crossword {
    /**
     * Total columns of Squares.
     * @var integer
     */
    private $max_horizontal_squares = 10;

    /**
     * Rows of Squares (can be increased by really big words).
     * @var integer
     */
    private $current_max_vertical_squares = 10;

    /**
     * Container for an answer and its Question.
     * @var Entry[]
     */
    private $entries = [];

    /**
     * The empty and filled positions that for the Crossword's board.
     * @var Square[][]
     */
    private $squares = [[]];

    /**
     * Y position of the highest Square created.
     * @var integer
     */
    private $min_vertical_position = INF;

    /**
     * Y position of the lowest Square created.
     * @var integer
     */
    private $max_vertical_position = -INF;

    /**
     * X position of the leftmost Square created.
     * @var integer
     */
    private $min_horizontal_position = INF;

    /**
     * X position of the rightmost Square created.
     * @var integer
     */
    private $max_horizontal_position = -INF;

    /**
     * Use it to avoid rearranging twice a Square that belongs to two answers.
     * @var integer
     */
    private $times_all_squares_will_have_been_rearranged_after_this_one = 0;

	/**
	 * Fits the answers given into a board in order, ignoring the incompatible ones.
     *
     * @param integer $max_horizontal_squares Total columns of Squares.
     * @param integer $initial_max_vertical_squares Rows of Squares (can be increased by really big words).
     * @param string[] $answers_text Words to create a board with
     * @param string[] $questions_text A question for each answer.
	 * @return self
     */
    public function __construct($max_horizontal_squares, $initial_max_vertical_squares, $answers_text, $questions_text) {
        $this->max_horizontal_squares = $max_horizontal_squares;
        $this->current_max_vertical_squares = $initial_max_vertical_squares;
        
        $first_answer_array = $this->stringToMultibyteCharArray($answers_text[0]);
        $first_question_text = $questions_text[0];
        $first_entry = new Entry($first_answer_array, $first_question_text, false, 0, 0);
        // The first one is the only Entry that will never be rejected
        $this->insertEntry($first_entry);

        for ($i = 1; $i < count($answers_text); $i++)
            $this->tryToInsertEntry($answers_text[$i], $questions_text[$i]);
    }



    /**
     * Inserts an answer if it is compatible with the already inserted ones.
     *
     * @param string $new_answer_text Answer to be inserted.
     * @param string $new_question_text Answer's question.
     * @return void
     */
    private function tryToInsertEntry($new_answer_text, $new_question_text) {
        $new_answer_array = $this->stringToMultibyteCharArray($new_answer_text);
        $answer_length = count($new_answer_array);

        // Tries to insert the answer wherever it can cross a character already inserted.
        foreach ($this->entries as $old_entry) {
            foreach ($old_entry->getAnswerSquares() as $old_square) {
                for ($i = 0; $i < count($new_answer_array); $i++) {
                    if ($new_answer_array[$i] == $old_square->getCharacter()) {
                        // Sets a direction different than the answer to be crossed.
                        $direction_is_horizontal = !$old_entry->getDirectionIsHorizontal();

                        // If !$direction_is_horizontal, puts the word $i squares above.
                        $first_square_vertical_position = $old_square->getVerticalPosition() - !$direction_is_horizontal * $i;

                        // If $direction_is_horizontal, puts the word $i squares to the left.
                        $first_square_horizontal_position = $old_square->getHorizontalPosition() - $direction_is_horizontal * $i;



                        // Checks if the answer fits the crosswords in the chosen direction.
                        $answer_fits_the_crossword = $direction_is_horizontal ?
                            $this->answerFitsTheCrosswordHorizontal($answer_length, $first_square_horizontal_position) :
                            $this->answerFitsTheCrosswordVertical($answer_length, $first_square_vertical_position);

                        
                        
                        // If the answer fits the board and all old answers, inserts it.
                        if ($answer_fits_the_crossword) {
                            $new_entry = new Entry($new_answer_array, $new_question_text, $direction_is_horizontal, $first_square_vertical_position, $first_square_horizontal_position);

                            if ($this->newEntryFitsTheOldEntries($new_entry)) {
                                $this->insertEntry($new_entry);
                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Turns a string into a char array of special characters.
     *
     * @param string $string A regular string.
     * @return string[] A multibyte char array
     */
    private function stringToMultibyteCharArray($string) {
        $multibyte_char_array = [];

        for ($i = 0; $i < strlen($string); $i++) {
            $multibyte_char = mb_substr($string, $i, 1);

            // A [special char] is read as a pair of "" + [special char]
            if ($multibyte_char != "")
                array_push($multibyte_char_array, $multibyte_char);
        }
            
        return $multibyte_char_array;
    }



    /**
     * False means the word would make the Crossword wider than its limits.
     *
     * @param int $answer_length length of the answer.
     * @param int $first_square_horizontal_position X position of the first character.
     * @return boolean
     */
    private function answerFitsTheCrosswordHorizontal($answer_length, $first_square_horizontal_position) {
        // -1 because the first character is in the position 0, not 1.
        $last_square_horizontal_position = $first_square_horizontal_position + $answer_length - 1;
        
        $fits_max_horizontal_size = $answer_length <= $this->max_horizontal_squares;

        $fits_board_left = $this->max_horizontal_position - $first_square_horizontal_position < $this->max_horizontal_squares;

        $fits_board_right = $last_square_horizontal_position - $this->min_horizontal_position < $this->max_horizontal_squares;

        return $fits_max_horizontal_size && $fits_board_left && $fits_board_right;
    }

    /**
     * False means the word would make the Crossword higher than its limits.
     * 
     * True includes the possibility of stretching the Crossword specifically to allow words that are longer that its original limits.
     *
     * @param int $answer_length Length of the answer.
     * @param int $first_square_horizontal_position X position of the first character.
     * @return boolean
     */
    private function answerFitsTheCrosswordVertical($answer_length, $first_square_vertical_position) {
        // -1 because the first character is in the position 0, not 1.
        $last_square_vertical_position = $first_square_vertical_position + $answer_length - 1;

        $fits_max_vertical_size = $answer_length <= $this->current_max_vertical_squares;

        $fits_board_up = $this->max_vertical_position - $first_square_vertical_position < $this->current_max_vertical_squares;
        
        $fits_board_down = $last_square_vertical_position - $this->min_vertical_position < $this->current_max_vertical_squares;
        
        if ($fits_max_vertical_size)
            return $fits_board_up && $fits_board_down;
        else {
            $there_is_empty_space_above = $first_square_vertical_position > $this->min_vertical_position;

            $there_is_empty_space_under = $last_square_vertical_position < $this->max_vertical_position;

            // Words higher than the Crossword can stretch it vertically to one side, as long as they don't let any empty Squares to the opposite direction.
            return !$fits_board_down && !$there_is_empty_space_above ||
                !$fits_board_up && !$there_is_empty_space_under;
        }
    }

    /**
     * False means the new answer is not compatible with the old ones.
     *
     * @param Entry $new_entry The new answer's Entry.
     * @return boolean
     */
    private function newEntryFitsTheOldEntries($new_entry) {
        // The extremities of a word mustn't touch another word.
        if (!$this->thereIsSpaceBeforeAndAfterEntry($new_entry))
            return false;

        // Tests each character of the answer to insert.
        foreach ($new_entry->getAnswerSquares() as $new_square) {
            $vertical_position = $new_square->getVerticalPosition();
            $horizontal_position = $new_square->getHorizontalPosition();

            // If there is a crossable character in the same position, it' ok.
            if ($this->squareExists($vertical_position, $horizontal_position)) {
                $old_square = $this->squares[$vertical_position][$horizontal_position];
    
                if (!$this->squaresAreCrossable($new_square, $old_square))
                    return false;
            }
            // To fill an empty position, the neighbour positions must be empty as well.
            else {
                $direction_is_horizontal = $new_entry->getDirectionIsHorizontal();

                if ($this->neighbourSquareIsFilled($new_square, $direction_is_horizontal))
                    return false;
            }
        }

        return true;
    }

    /**
     * False means an old character would touch the new answer, which is not acceptable.
     *
     * @param Entry $new_entry The new answer's Entry.
     * @return boolean
     */
    private function thereIsSpaceBeforeAndAfterEntry($new_entry) {
        $direction_is_horizontal = $new_entry->getDirectionIsHorizontal();
        $first_square = $new_entry->getAnswerFirstSquare();
        $last_square = $new_entry->getAnswerLastSquare();

        $precedent_square_is_filled = $direction_is_horizontal ?
            $this->leftSquareIsFilled($first_square) :
            $this->upSquareIsFilled($first_square);

        $subsequent_square_is_filled = $direction_is_horizontal ?
            $this->rightSquareIsFilled($last_square) :
            $this->downSquareIsFilled($last_square);

        return !$precedent_square_is_filled &&
            !$subsequent_square_is_filled;
    }

    /**
     * False means the characters are different or their answers are in the same direction.
     *
     * @param Square $square_1 Square of an answer's character.
     * @param Square $square_2 Square of an answer's character.
     * @return boolean
     */
    private function squaresAreCrossable($square_1, $square_2) {
        return $this->squaresHaveSameCharacter($square_1, $square_2) &&
            !$this->squaresHaveSameDirection($square_1, $square_2);
    }

    /**
     * False means the characters are different.
     *
     * @param Square $square_1 Square of an answer's character.
     * @param Square $square_2 Square of an answer's character.
     * @return boolean
     */
    private function squaresHaveSameCharacter($square_1, $square_2) { return $square_1->getCharacter() == $square_2->getCharacter(); }

    /**
     * False means the Squares' answers are in different directions.
     *
     * @param Square $square_1 Square of an answer's character.
     * @param Square $square_2 Square of an answer's character.
     * @return boolean
     */
    private function squaresHaveSameDirection($square_1, $square_2) {
        return
            $square_1->containsVerticalDirection() && $square_2->containsVerticalDirection() ||
            $square_1->containsHorizontalDirection() && $square_2->containsHorizontalDirection();
    }

    /**
     * True means an old character would touch the new answer, which is not acceptable.
     *
     * @param Square $square Square of the Entry to insert.
     * @param Square $direction_is_horizontal False means the direction is vertical.
     * @return boolean
     */
    private function neighbourSquareIsFilled($square, $direction_is_horizontal) {
        $first_neighbour_is_filled = $direction_is_horizontal ?
            $this->upSquareIsFilled($square) :
            $this->leftSquareIsFilled($square);

        $second_neighbour_is_filled = $direction_is_horizontal ?
            $this->downSquareIsFilled($square) :
            $this->rightSquareIsFilled($square);

        return $first_neighbour_is_filled || $second_neighbour_is_filled;
    }

    /**
     * False means there is an empty space over the square.
     *
     * @param Square $square Square of the Entry to insert.
     * @return boolean
     */
    private function upSquareIsFilled($square) { return isset($this->squares[$square->getVerticalPosition() - 1][$square->getHorizontalPosition()]); }

    /**
     * False means there is an empty space on the right of the square.
     *
     * @param Square $square Square of the Entry to insert.
     * @return boolean
     */
    private function rightSquareIsFilled($square) { return isset($this->squares[$square->getVerticalPosition()][$square->getHorizontalPosition() + 1]); }

    /**
     * False means there is an empty space under the square.
     *
     * @param Square $square Square of the Entry to insert.
     * @return boolean
     */
    private function downSquareIsFilled($square) { return isset($this->squares[$square->getVerticalPosition() + 1][$square->getHorizontalPosition()]); }

    /**
     * False means there is an empty space on the left of the square.
     *
     * @param Square $square Square of the Entry to insert.
     * @return boolean
     */
    private function leftSquareIsFilled($square) { return isset($this->squares[$square->getVerticalPosition()][$square->getHorizontalPosition() - 1]); }

    /**
     * Inserts an answer without doing any tests.
     *
     * @param string $new_entry Entry of the answer to be inserted.
     * @return void
     */
    private function insertEntry($new_entry) {
        array_push($this->entries, $new_entry);

        $new_entry->storeSquaresOnCrossword($this);

        $this->updateLimits($new_entry);
    }



    /**
     * Prepares the Crossword for being used after all Entries have been inserted.
     * @return void
     */
    public function setReadyToUse() {
        $this->setMinPositionsToZero();
        $this->organizeEntries();
        $this->setFirstSquaresAndQuestionNumbers();
        $this->createOuterSquaresFromBorder();
        $this->setTopAndLeftBorderSquares();
        $this->createInnerSquares();
    }



    /**
     * Adds a Square to the Crossword's array of Squares.
     *
     * @param Square $square Square to insert.
     * @return void
     */
    public function insertSquare($square) { $this->squares[$square->getVerticalPosition()][$square->getHorizontalPosition()] = $square; }

    /**
     * False means there is no Square in the given position.
     *
     * @param string $vertical_position Y position of the Square.
     * @param string $horizontal_position X position of the Square.
     * @return boolean
     */
    public function squareExists($vertical_position, $horizontal_position) { return isset($this->squares[$vertical_position][$horizontal_position]); }

    /**
     * The Square in the given position of the array of Squares.
     *
     * @param string $vertical_position Y position of the Square.
     * @param string $horizontal_position X position of the Square.
     * @return Square
     */
    public function getSquare($vertical_position, $horizontal_position) { return $this->squares[$vertical_position][$horizontal_position]; }



    /**
     * False means the position is beyond the existent Squares.
     *
     * @param string $vertical_position Y position of the Square.
     * @param string $horizontal_position X position of the Square.
     * @return boolean
     */
    public function positionIsInsideLimits($vertical_position, $horizontal_position) {
        return
            $vertical_position <= $this->max_vertical_position &&
            $horizontal_position <= $this->max_horizontal_position &&
            $vertical_position >= $this->min_vertical_position &&
            $horizontal_position >= $this->min_horizontal_position;
    }



    /**
     * Number of already inserted Entries.
     * @return integer
     */
    public function getNumberOfEntries() { return count($this->entries); }



    /**
     * Moves all Squares together, making the top-left position [0][0].
     * @return void
     */
    private function setMinPositionsToZero() {
        $this->squares = [[]];

        // Prepares to subtract the min. v. pos. of all Squares, making the min. = 0.
        $vertical_position_to_add = -$this->min_vertical_position;

        // Prepares to subtract the min. h. pos. of all Squares, making the min. = 0.
        $horizontal_position_to_add = -$this->min_horizontal_position;

        $this->resetLimits();
        $this->times_all_squares_will_have_been_rearranged_after_this_one++;

        foreach ($this->entries as $entry) {
            $entry->addToPosition($vertical_position_to_add, $horizontal_position_to_add, $this->times_all_squares_will_have_been_rearranged_after_this_one);

            $entry->storeSquaresOnCrossword($this);
            $this->updateLimits($entry);
        }
    }

    /**
     * Sets the max. and min. positions to indicate the Crossword is empty.
     * @return void
     */
    private function resetLimits() {
        $this->min_vertical_position = INF;
        $this->max_vertical_position = -INF;
        $this->min_horizontal_position = INF;
        $this->max_horizontal_position = -INF;
    }

    /**
     * Reorders the Entries according to their position in the Crossword.
     * @return void
     */
    private function organizeEntries() {
        $organized_entries = [];
        
        foreach ($this->entries as $next_entry) {
            $i = 0;

            for (; $i < count($organized_entries) ; $i++)
                if ($next_entry->IShouldComeBeforeTheGivenEntry($organized_entries[$i]))
                    // Defines $i as the the Entry's position.
                    break;
            
            array_splice($organized_entries, $i, 0, [$next_entry]);
        }
        
        $this->entries = $organized_entries;
    }

    /**
     * Gives a number to each Entry and sets the properties of each answer's first Square.
     * @return void
     */
    private function setFirstSquaresAndQuestionNumbers() {
        for ($i = 0; $i < count($this->entries); $i++)
            $this->entries[$i]->setFirstSquareAndQuestionNumber($i + 1);
    }

    /**
     * Fills empty spaces around the inserted answers (FilledSquares) with OuterSquares.
     * @return void
     */
    private function createOuterSquaresFromBorder() {
        for ($i = $this->min_vertical_position; $i <= $this->max_vertical_position; $i++)
            for ($j = $this->min_horizontal_position; $j <= $this->max_horizontal_position; $j++)
                if ($this->positionIsAtBorder($i, $j) &&
                    !$this->squareExists($i, $j)) {
                    $this->squares[$i][$j] = new OuterSquare($i, $j);
                    $this->squares[$i][$j]->SpreadOuterSquares($this);
                }
    }

    /**
     * Position has minimum or maximum  Y or X.
     * @return boolean
     */
    private function positionIsAtBorder($vertical_position, $horizontal_position) {
        return
            $vertical_position == $this->min_vertical_position ||
            $horizontal_position == $this->min_horizontal_position ||
            $vertical_position == $this->max_vertical_position ||
            $horizontal_position == $this->max_horizontal_position;
    }

    /**
     * Identifies Squares on the left or top border after the Crossword is ready.
     * @return void
     */
    private function setTopAndLeftBorderSquares() {
        for ($i = $this->min_vertical_position; $i <= $this->max_vertical_position; $i++)
            $this->squares[$i][0]->setAsLeftBorderSquare();

        for ($j = $this->min_horizontal_position; $j <= $this->max_horizontal_position; $j++)
            $this->squares[0][$j]->setAsTopBorderSquare();
    }

    /**
     * Fills empty spaces surrounded by the inserted answers (FilledSquares) with InnerSquares.
     * @return void
     */
    private function createInnerSquares() {
        for ($i = $this->min_vertical_position; $i <= $this->max_vertical_position; $i++)
            for ($j = $this->min_horizontal_position; $j <= $this->max_horizontal_position; $j++)
                // All Squares that are not InnerSquares were already created. 
                if (!$this->squareExists($i, $j))
                    $this->squares[$i][$j] = new InnerSquare($i, $j);
    }



	/**
	 * Expected character for each input the user will give.
	 * @return string[][] One multibyte character for each valid [y][x] position.
	 */
    public function getMapOfCharacters() {
        $bidimensional_array_of_correct_letters = [];

        for ($i = $this->min_vertical_position; $i <= $this->max_vertical_position; $i++)
            for ($j = $this->min_horizontal_position; $j <= $this->max_horizontal_position; $j++)
                // Only FilledSquares have characters to guess.
                if ($this->squares[$i][$j] instanceof FilledSquare)
                    $bidimensional_array_of_correct_letters[$i][$j] = $this->squares[$i][$j]->getCharacter();
        
        return $bidimensional_array_of_correct_letters;
    }

    /**
     * Expand limits to contain the Entry's answer.
     *
     * @param Entry $new_entry Entry being inserted.
     * @return void
     */
    private function updateLimits($new_entry) {
        if ($new_entry->getMinVerticalPosition() < $this->min_vertical_position)
            $this->min_vertical_position = $new_entry->getMinVerticalPosition();

        if ($new_entry->getMaxVerticalPosition() > $this->max_vertical_position)
            $this->max_vertical_position = $new_entry->getMaxVerticalPosition();

        if ($new_entry->getMinHorizontalPosition() < $this->min_horizontal_position)
            $this->min_horizontal_position = $new_entry->getMinHorizontalPosition();

        if ($new_entry->getMaxHorizontalPosition() > $this->max_horizontal_position)
            $this->max_horizontal_position = $new_entry->getMaxHorizontalPosition();

        // Stretches the board verticaly.
        if (!$new_entry->getDirectionIsHorizontal() &&
            $new_entry->getAnswerLength() > $this->current_max_vertical_squares)
            $this->current_max_vertical_squares = $new_entry->getAnswerLength();
    }



	/**
	 * Div with a board of Squares.
	 * @return string HTML ready to be displayed.
	 */
    public function getHtml() {
        $html = $this->getHtmlFile();
		$html_variables = $this->getHtmlVariables();

        $html = $this->replaceHtmlVariables($html, $html_variables);

        return $html;
    }

	/**
	 * Crossword's HTML whose variables must be replaced by their values.
	 * @return string Raw HTML from file.
	 */
    private function getHtmlFile() { return file_get_contents(__DIR__ . "/../../view/crossword.html"); }

    /**
	 * Variables to replace and their respective values.
	 * @return string[] "{{key}}" => "value"
	 */
    private function getHtmlVariables() {
        $html_variables = [
            "{{squares}}" => $this->getSquaresHtml()
        ];
        
        return $html_variables;
    }
    
	/**
	 * A concatanation of the display of each Square.
	 * @return string Squares' HTML ready to be displayed.
	 */
    private function getSquaresHtml() {
        $squares_html = "";

        for ($i = $this->min_vertical_position; $i <= $this->max_vertical_position; $i++) {
            if ($i > 0)
                $squares_html .= "<br>";
                
            for ($j=$this->min_horizontal_position; $j <= $this->max_horizontal_position ; $j++)
                $squares_html .= $this->squares[$i][$j]->getHtml();
        }

        return $squares_html;
    }

	/**
	 * A concatanation of the display of each Question.
	 * @return string Questions' HTML ready to be displayed.
	 */
    public function getQuestionsHtml() {
        $questions_html = "";
        
        foreach ($this->entries as $entry)
            $questions_html .= $entry->getQuestionHtml();
        
        return $questions_html;
    }

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
}
?>