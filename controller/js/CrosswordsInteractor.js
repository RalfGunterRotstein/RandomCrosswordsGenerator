/**
 * Lets the user fill the Crossword as well as request and receive a result.
 * @author Ralf Gunter Rotstein <ralf.rotstein@gmail.com>
 * @copyright Copyright (c) 2021, Ralf Gunter Rotstein
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 * 
 * @category RandomCrosswordsGenerator
 * @package RandomCrosswordsGenerator
 * @version 1.0.0
 */
class CrosswordsInteractor {
	/**
	 * Initializes variables and adds event listeners.
	 * @constructor
	 */
    constructor() {
		/**
		 * @property {HTMLElement[]} firstSquaresInOrder All the first squares (divs).
		 */
		this.firstSquaresInOrder = this.getFirstSquaresInOrderForTheFirstTime();



		/**
		 * @property {HTMLElement} numberedQuestionsContainer Container of the Questions.
		 */
		this.numberedQuestionsContainer = document.querySelector("#numbered-questions__container");

		/**
		 * @property {HTMLElement[]} numberedQuestions Questions of the crossword.
		 */
		this.numberedQuestions = this.numberedQuestionsContainer.querySelectorAll(".numbered-questions__question");



		/**
		 * @property {HTMLElement} numberedQuestionsContainer Container of the dir. selectors.
		 */
		this.directionSelectors = document.querySelector("#crossword__direction-selectors");

		/**
		 * @property {HTMLElement} directionSelectorHorizontal Clickable direction selector.
		 */
		this.directionSelectorHorizontal = document.querySelector("#direction-selectors__horizontal");
		
		/**
		 * @property {HTMLElement} directionSelectorVertical Clickable direction selector.
		 */
		this.directionSelectorVertical = document.querySelector("#direction-selectors__vertical");
		
		/**
		 * @property {HTMLElement} directionSelectorToggle Clickable direction selector.
		 */
		this.directionSelectorToggle = document.querySelector("#direction-selectors__toggle");

		/**
		 * @property {boolean} directionChangeIsBlocked True means direction can't be changed.
		 */
		this.directionChangeIsBlocked = false;
		


		/**
		 * @property {HTMLElement} crossword Crossword made of Squares.
		 */
		this.crossword = document.querySelector("#crossword");

		/**
		 * @property {HTMLElement[]} inputs Inputs of the Squares that should be filled.
		 */
		this.inputs = this.crossword.querySelectorAll(".square input");

		/**
		 * @property {HTMLElement} form Contains the Crossword and its submit buttom.
		 */
		this.form = document.querySelector("#crossword__answers__form");



		/**
		 * @property {HTMLElement} title Title (h2) of the crossword.
		 */
		this.title = document.querySelector("#crossword__title-container");
		
		/**
		 * @property {HTMLElement} submitButton Will send answers to analysis page.
		 */
		this.submitButton = document.querySelector("#crossword__answers__form__submit-button");

		/**
		 * @property {HTMLElement} evaluationFeedback Will display result from analysis page.
		 */
		this.evaluationFeedback = document.querySelector("#crosswords__evaluation-feedback");



		this.setDirection("vertical");
		this.lastFocusedInput = null;
		this.addEventListeners();
	}

	/**
	 * Distance from element's top to the top of the whole page.
	 * @param {HTMLElement} element Element whose top to consider.
	 * @returns {number}
	 */
    elementDistanceFromPageTop(element) { return window.pageYOffset + element.getBoundingClientRect().top; }

	/**
	 * Square from the given position of the crossword.
	 * @param {number} verticalPosition Y position of the square.
	 * @param {number} horizontalPosition X position of the square.
	 * @returns {HTMLElement}
	 */
    getHtmlSquare(verticalPosition, horizontalPosition) { return document.querySelector("#square-" + verticalPosition + "-" + horizontalPosition); }



	/**
	 * First Square of each word sorted by their number.
	 * @returns {HTMLElement[]} All the first squares (divs).
	 */
	getFirstSquaresInOrderForTheFirstTime() {
		const firstSquareNumbers = Array.from(document.querySelectorAll(".question-number"));

		const firstSquareNumbersInOrder = firstSquareNumbers.sort((a, b) => this.firstSquareNumberIdNumber(a) - this.firstSquareNumberIdNumber(b));

		const firstSquaresInOrder = firstSquareNumbersInOrder.map(firstSquareNumber => firstSquareNumber.parentElement);

		return firstSquaresInOrder;
	}

	/**
	 * Number of the element firstSquareQuestionNumber.
	 * @param {HTMLElement} firstSquareNumber A span with the number of the question.
	 * @returns {number} The number of the question.
	 */
	firstSquareNumberIdNumber(firstSquareNumber) {
		// Ex. [first, square, question, number, 7]
		const idPartsSeparetedByHyphen = firstSquareNumber.id.split("-");

		// Ex. 7
		const lastIdPart = idPartsSeparetedByHyphen[idPartsSeparetedByHyphen.length-1];

		return lastIdPart;
	}

	/**
	 * Avoids certain bugs.
	 */
	blockDirectionChange() { this.directionChangeIsBlocked = true; }

	/**
	 * Allows the user to toggle between vertical and horizontal.
	 */
	unblockDirectionChange() { this.directionChangeIsBlocked = false; }



	/**
	 * Information contained in the element's ID.
	 * @param {HTMLElement} element A square (div) or its input.
	 * @returns {HTMLElement[]} [square, VNUM, HNUM]
	 */
    boardElementBasicInfo(element) {
		// Exs. [square, 3, 5], [input, 3, 5]
        const [elementType, elementVerticalPosition, elementHorizontalPosition] = element.id.split("-");

		// Ex. 3
        const elementVerticalPositionInt = parseInt(elementVerticalPosition);

		// Ex. 5
        const elementHorizontalPositionInt = parseInt(elementHorizontalPosition);

		// Ex. ["square", 3, 5]
        return [elementType, elementVerticalPositionInt, elementHorizontalPositionInt];
    }

	/**
	 * Runs recursively until it reaches the first square.
	 * @param {HTMLElement} selectedSquare Current square to check if it is the first.
	 * @param {string} direction "vertical" or "horizontal".
	 * @returns {HTMLElement} The first square (div).
	 */
    getFirstSquareOfWord(selectedSquare, direction) {
		if (this.isFirstSquareOfDirection(selectedSquare, direction))
        	return selectedSquare;
		else {
			const previousSquare = this.getPreviousSquare(selectedSquare, direction);

			return this.getFirstSquareOfWord(previousSquare, direction);
		}
	}

	/**
	 * False means the square is not the first of a word in the given direction.
	 * @param {HTMLElement} square A div.
	 * @param {string} direction "vertical" or "horizontal".
	 * @returns {boolean}
	 */
	isFirstSquareOfDirection(square, direction) { return this.getFirstSquareQuestionNumber(square, direction) != null; }

	/**
	 * The previous square in the given direction.
	 * @param {HTMLElement} square A div.
	 * @param {string} direction "vertical" or "horizontal".
	 * @returns {HTMLElement} div
	 */
	getPreviousSquare(square, direction) {
		const [type, verticalPosition, horizontalPosition] = this.boardElementBasicInfo(square);

		const previousSquare = this.getHtmlSquare(verticalPosition - (direction=="vertical"), horizontalPosition - (direction=="horizontal"));

		return previousSquare;
	}



	/**
	 * Removes all classes that indicate any element is selected.
	 * @returns {void}
	 */
    unselectWords() {
		const selectedNumbers = Array.from(document.querySelectorAll(".question-number--selected"));
		const selectedSquares = Array.from(document.querySelectorAll(".square--selected"));
		const selectedQuestions = Array.from(document.querySelectorAll(".question--selected"));

		selectedNumbers.forEach(number => number.classList.remove("question-number--selected"));
		selectedSquares.forEach(square => square.classList.remove("square--selected"));
		selectedQuestions.forEach(question => question.classList.remove("question--selected"));
    }

	/**
	 * Selects all elements of an answer clicked by the user.
	 * @param {HTMLElement} selectedSquare A square (div) selected by the user.
	 * @returns {void}
	 */
    selectWord(selectedSquare) {
		this.unselectWords();

		const wordDirection = this.getDirectionBasedOnSelectedSquare(selectedSquare);

        let firstSquare = this.getFirstSquareOfWord(selectedSquare, wordDirection);

        const firstSquareQuestionNumber = this.getFirstSquareQuestionNumber(firstSquare, wordDirection);
		this.selectFirstSquareQuestionNumber(firstSquareQuestionNumber);

		this.selectQuestion(firstSquareQuestionNumber);

		this.selectAllWordSquaresStartingFromFirst(firstSquare, wordDirection);
	}

	/**
	 * The direction of the answer selected by the user.
	 * @param {HTMLElement} selectedSquare A square (div) selected by the user.
	 * @returns {string} "vertical" or "horizontal".
	 */
	getDirectionBasedOnSelectedSquare(selectedSquare) {
		let wordDirection;
        if (this.elementContainsDirection(selectedSquare, this.currentDirection))
            wordDirection = this.currentDirection;
		else
			wordDirection = (this.currentDirection == "horizontal") ? "vertical" : "horizontal";
		
		return wordDirection;
	}

	/**
	 * Checks if the square is part of a word in the given direction.
	 * @param {HTMLElement} element A square (div).
	 * @param {string} direction "vertical" or "horizontal".
	 * @returns {boolean}
	 */
	elementContainsDirection(element, direction) { return element.classList.contains(direction); }

	/**
	 * The number of the first square.
	 * @param {HTMLElement} firstSquare A div.
	 * @param {string} wordDirection "vertical" or "horizontal".
	 * @returns {HTMLElement} A span.
	 */
	getFirstSquareQuestionNumber(firstSquare, wordDirection) { return firstSquare.querySelector(".question-number." + wordDirection); }

	/**
	 * Adds class that indicates the number is selected.
	 * @returns {void}
	 */
	selectFirstSquareQuestionNumber(firstSquareQuestionNumber) { firstSquareQuestionNumber.classList.add("question-number--selected"); }

	/**
	 * Adds class that indicates the question is selected.
	 * @returns {void}
	 */
	selectQuestion(firstSquareQuestionNumber) {
		const questionNumberInt = firstSquareQuestionNumber.innerHTML;
		const question = document.querySelector("#go-to-answer-" + questionNumberInt);
		question.classList.add("question--selected");
	}

	/**
	 * Adds class that indicates all squares of an answer are selected.
	 * @param {HTMLElement} squareToSelect First square, then the others recursively.
	 * @param {string} wordDirection "vertical" or "horizontal".
	 * @returns {void}
	 */
	selectAllWordSquaresStartingFromFirst(squareToSelect, wordDirection) {
		this.selectSquare(squareToSelect);

        const [type, verticalPosition, horizontalPosition] = this.boardElementBasicInfo(squareToSelect);

		const nextVerticalPosition = verticalPosition + (wordDirection == "vertical");
		const nextHorizontalPosition = horizontalPosition + (wordDirection == "horizontal");
		const nextSquareToSelect = this.getHtmlSquare(nextVerticalPosition, nextHorizontalPosition);

		if (this.squareIsPartOfAWord(nextSquareToSelect))
			this.selectAllWordSquaresStartingFromFirst(nextSquareToSelect, wordDirection);
    }

	/**
	 * False means the square doesn't have an input.
	 * @param {HTMLElement} square A div.
	 * @returns {boolean}
	 */
	squareIsSelectable(square) { return this.getSquareInput(square) != null; }

	/**
	 * The input of the square, if it has one.
	 * @param {HTMLElement} square A div.
	 * @returns {HTMLElement|null} An input.
	 */
	getSquareInput(square) { return square.querySelector("input"); }

	/**
	 * Adds class that indicates the square is selected.
	 * @param {HTMLElement|null} squareToSelect A div.
	 * @returns {void}
	 */
	selectSquare(squareToSelect) {
		if (this.squareIsSelectable(squareToSelect))
			squareToSelect.classList.add("square--selected");
	}

	/**
	 * False means the square is an outer-square or an inner-square.
	 * @param {HTMLElement} square A div.
	 * @returns {boolean}
	 */
	squareIsPartOfAWord(square) {
		return square != null &&
			(square.classList.contains("filled-square") ||
			square.classList.contains("space-marker"));
	}



	/**
	 * Defines the direction of the word the user will fill.
	 * @param {string} newDirection "vertical" or "horizontal".
	 * @returns {void}
	 */
	setDirection(newDirection) {
		if (!this.directionChangeIsBlocked) {
			this.currentDirection = newDirection;
			this.animateDirectionSelectors();
		}
	}

	/**
	 * Inverts the direction of the word the user will fill (vertical/horizontal).
	 * @returns {void}
	 */
	toggleDirection() {
		if (!this.directionChangeIsBlocked) {
			this.currentDirection =
				(this.currentDirection == "horizontal") ?
					"vertical" :
					"horizontal";

			this.animateDirectionSelectors();
		}
	}

	/**
	 * Makes the interface indicate the direction of the selected word.
	 * @returns {void}
	 */
    animateDirectionSelectors() {
        if (this.currentDirection == "horizontal") {
            this.directionSelectorHorizontal.classList.add("active");
            this.directionSelectorVertical.classList.remove("active");
            this.directionSelectorToggle.classList.add("horizontal");
            this.directionSelectorToggle.classList.remove("vertical");
        }
        else {
            this.directionSelectorHorizontal.classList.remove("active");
            this.directionSelectorVertical.classList.add("active");
            this.directionSelectorToggle.classList.remove("horizontal");
            this.directionSelectorToggle.classList.add("vertical");
        }
    }

	/**
	 * Move one square in the given direction, looping through the end of the selected word.
	 * @param {HTMLElement} currentSquare Square whose input the cursor is currently on.
	 * @param {number} verticalMovement Ex. Up -> -1.
	 * @param {number} horizontalMovement Ex. Up -> 0.
	 * @param {boolean} looping Cursor passed last square, so it's looping to the first.
	 * @returns {void}
	 */
	moveCursor(currentSquare, verticalMovement, horizontalMovement, looping = false) {
		const [squareType, currentVerticalPosition, currentHorizontalPosition] = this.boardElementBasicInfo(currentSquare);

		const nextVerticalPosition = currentVerticalPosition + verticalMovement;
		const nextHorizontalPosition = currentHorizontalPosition + horizontalMovement;
		
		const nextSquareToFocus = this.getHtmlSquare(nextVerticalPosition, nextHorizontalPosition);

		if (this.squareIsPartOfAWord(nextSquareToFocus)) {
			if (this.squareIsSelectable(nextSquareToFocus)) {
				if (looping == false) {
					// selectable square + not looping = we've found the square to focus.
					const movementDirection = (verticalMovement != 0) ? "vertical" : "horizontal";
					this.setDirection(movementDirection);

					this.focusInputBySquare(nextSquareToFocus);
				}
				else
					// selectable square + looping = follow backwards until the first square.
					return this.moveCursor(nextSquareToFocus, verticalMovement, horizontalMovement, true);
			}
			else
				// Part of word + not selectable = space-marker. Skip this one.
				return this.moveCursor(nextSquareToFocus, verticalMovement, horizontalMovement, looping);
		}
		else
			if (looping == false)
				// Not part of a word + not looping = we've passed the end. Loop to beginning.
				return this.moveCursor(currentSquare, -verticalMovement, -horizontalMovement, true);
			else {
				// Not part of a word + looping = current square is the first. Select it.
				const movementDirection = (verticalMovement != 0) ? "vertical" : "horizontal";
				this.setDirection(movementDirection);

				this.focusInputBySquare(currentSquare);
			}
	}

	/**
	 * Puts the cursor on the given square's input.
	 * @param {HTMLElement} square A div.
	 * @returns {void}
	 */
	focusInputBySquare(square) {
		const input = this.getSquareInput(square);
		this.focusInput(input);
	}



	/**
	 * Sets all the ways in which the user can interact with the crossword.
	 * @returns {void}
	 */
	addEventListeners() {
		this.crossword.addEventListener("keydown", this.keyDownEvent.bind(this));
	
		this.directionSelectors.addEventListener("mousedown", function(event) { event.preventDefault(); });
		this.directionSelectors.addEventListener("click", this.directionSelectorsClickEvent.bind(this));
		
		this.inputs.forEach(input => {
			input.addEventListener("mousedown", this.squareInputMouseDownEvent.bind(this, input));

			input.addEventListener("focus", this.squareInputFocusEvent.bind(this, input));
			input.addEventListener("blur", this.squareInputBlurEvent.bind(this));

			input.addEventListener("input", this.squareInputInputEvent.bind(this, input));
		});
		
		this.numberedQuestions.forEach(numberedQuestion => {
			numberedQuestion.addEventListener("click", this.numberedQuestionClickEvent.bind(this, numberedQuestion));

			numberedQuestion.addEventListener("touchstart", this.numberedQuestionTouchStartEvent.bind(this, numberedQuestion), {passive: true});
			numberedQuestion.addEventListener("touchmove", this.numberedQuestionTouchMoveEvent.bind(this, numberedQuestion), {passive: true});
			numberedQuestion.addEventListener("touchend", this.numberedQuestionTouchEndEvent.bind(this, numberedQuestion), {passive: true});
		});
		
		window.addEventListener("scroll", this.scrollEvent.bind(this), {passive: true});
		
		this.form.addEventListener("submit", this.submitEvent.bind(this));
	}

	/**
	 * Calls a method according to the pressed key.
	 * @param {HTMLElement} event The keyDown event.
	 * @returns {void}
	 */
	keyDownEvent(event) {
		const input = document.activeElement;
	
		if (this.elementIsInput(input)) { // has to be capital
			switch (event.key) {
				case "Backspace":
					this.backspaceKeyEvent(input, event);
					break;

				case "Control":
					this.controlKeyEvent();
					break;
		
				case "ArrowUp":
					if (this.elementContainsDirection(input, "vertical"))
						this.moveCursorUp(input);
					break;
				case "ArrowRight":
					if (this.elementContainsDirection(input, "horizontal"))
						this.moveCursorRight(input);
					break;
				case "ArrowDown":
					if (this.elementContainsDirection(input, "vertical"))
						this.moveCursorDown(input);
					break;
				case "ArrowLeft":
					if (this.elementContainsDirection(input, "horizontal"))
						this.moveCursorLeft(input);
					break;
			}
		}
	}

	/**
	 * False means the element is not an input.
	 * @param {HTMLElement} possibleInput Element to check.
	 * @returns {boolean}
	 */
	elementIsInput(possibleInput) { return possibleInput.tagName == "INPUT"; }

	/**
	 * If current square already empty, moves to the previous one. Erases square.
	 * @param {HTMLElement} input A square's input.
	 * @param {HTMLElement} event The keyDown event.
	 * @returns {void}
	 */
	backspaceKeyEvent(input, event) {
		event.preventDefault();

		const verticalMovement = (this.currentDirection == "vertical") ? -1 : 0;
		const horizontalMovement = (this.currentDirection == "horizontal") ? -1 : 0;

		if (input.value == "")
			this.moveCursor(input.parentElement, verticalMovement, horizontalMovement);

		document.activeElement.value = "";
	}
	
	/**
	 * Toggles direction.
	 * @returns {void}
	 */
	controlKeyEvent() {
		this.toggleDirection();
		this.selectWord(this.lastFocusedInput.parentElement);
	}

	/**
	 * Moves cursor to the upward square.
	 * @returns {void}
	 */
	moveCursorUp(input) { this.moveCursor(input.parentElement, -1, 0); }

	/**
	 * Moves cursor to the rightward square.
	 * @returns {void}
	 */
	moveCursorRight(input) { this.moveCursor(input.parentElement, 0, 1); }

	/**
	 * Moves cursor to the downward square.
	 * @returns {void}
	 */
	moveCursorDown(input) { this.moveCursor(input.parentElement, 1, 0); }

	/**
	 * Moves cursor to the leftward square.
	 * @returns {void}
	 */
	moveCursorLeft(input) { this.moveCursor(input.parentElement, 0, -1); }



	/**
	 * Sets the direction according to the clicked direction selector.
	 * @param {HTMLElement} event The click event.
	 * @returns {void}
	 */
	directionSelectorsClickEvent(event) {
		const clickedElement = event.target;

		if (clickedElement === this.directionSelectorHorizontal)
			this.setDirection("horizontal");
		else if (clickedElement === this.directionSelectorVertical)
			this.setDirection("vertical");
		else if (clickedElement === this.directionSelectorToggle || this.directionSelectorToggle.contains(clickedElement))
			this.toggleDirection();

		this.focusLastFocusedInput();
	}

	/**
	 * Gives the focus back to the last input focused, if there is any.
	 * @returns {void}
	 */
	focusLastFocusedInput() {
		if (this.lastFocusedInput) {
			// Avoids toggling direction when reselecting the last selected input.
			this.blockDirectionChange();

			this.focusInput(this.lastFocusedInput);
			this.selectWord(this.lastFocusedInput.parentElement);
			
			this.unblockDirectionChange();
		}
	}

	/**
	 * If the clicked square is focused, toggles direction; selects word.
	 * @param {HTMLElement} input The mouseDown event's target.
	 * @returns {void}
	 */
	squareInputMouseDownEvent(input) {
		if (input === document.activeElement &&
			this.elementContainsDirection(input, "horizontal") &&
			this.elementContainsDirection(input, "vertical"))
			this.toggleDirection();
		
		this.updateLastFocusedInput();
		this.selectWord(input.parentElement);
	}

	/**
	 * Toggles direction if necessary; selects word.
	 * @param {HTMLElement} input The focus event's target.
	 * @returns {void}
	 */
	squareInputFocusEvent(input) {
		if (input.classList.contains(this.currentDirection))
			this.setDirection(this.currentDirection);
		else
			this.toggleDirection();
		
		this.updateLastFocusedInput();
		this.selectWord(input.parentElement);
	}

	/**
	 * Unselects words.
	 * @returns {void}
	 */
	squareInputBlurEvent() { this.unselectWords(); }

	/**
	 * Toggles direction if necessary; write character; go to next square of the answer.
	 * @param {HTMLElement} input A square's input.
	 * @param {HTMLElement} event The keyDown event.
	 * @returns {void}
	 */
	squareInputInputEvent(input, event) {
		if (!input.classList.contains(this.currentDirection))
			this.toggleDirection();
	
		// Avoids string with more than 1 character using only the last one.
		const inputCharacter = event.data ? event.data[event.data.length-1] : "";

		// Avoids spaces. They are already marked by the .space-marker class.
		input.value = (inputCharacter != " ") ? inputCharacter : "";
		
		// Moves focus to next square only if the input was valid.
		if (input.value != "") {
			const verticalMovement = 1 * (this.currentDirection == "vertical");
			const horizontalMovement = 1 * (this.currentDirection == "horizontal");
	
			this.moveCursor(input.parentElement, verticalMovement, horizontalMovement);
		}
	}

	/**
	 * Sets answer's direction, focuses its first square and scrolls window to its position.
	 * @param {HTMLElement} numberedQuestion A dl's item.
	 * @returns {void}
	 */
	numberedQuestionClickEvent(numberedQuestion) {
		const questionNumberInt = this.getQuestionNumberIntByNumberedQuestion(numberedQuestion);
		const input = this.getInputByQuestionNumberInt(questionNumberInt);
		const firstSquareQuestionNumber = this.getFirstSquareQuestionNumberByQuestionNumberInt(questionNumberInt);
		
		this.setDirection(this.elementContainsDirection(firstSquareQuestionNumber, "horizontal") ?
			"horizontal" :
			"vertical");
	
		// The window doesn't scroll automatically if the input was already selected.
		this.scrollToReselectedInput(input);
		
		this.focusInput(input);
	}

	/**
	 * Number of the question selected by the user.
	 * @param {HTMLElement} numberedQuestion A dl's item.
	 * @returns {number}
	 */
	getQuestionNumberIntByNumberedQuestion(numberedQuestion) {
		// Ex. go-to-answer-3
		const questionId = numberedQuestion.id;
		
		// Ex. [go, to, answer, 3]
		const questionIdParts = questionId.split("-");

		// Ex. 3
		const questionNumberInt = questionIdParts[questionIdParts.length - 1];

		return questionNumberInt;
	}

	/**
	 * Input of the first square of the selected question.
	 * @param {number} questionNumberInt Number of the selected question.
	 * @returns {HTMLElement} An input.
	 */
	getInputByQuestionNumberInt(questionNumberInt) {
		const firstSquare = this.getFirstSquareByQuestionNumberInt(questionNumberInt);
		const input = this.getSquareInput(firstSquare);

		return input;
	}

	/**
	 * Scroll to already focused input, since focusing it again doesn't scroll automatically.
	 * @param {HTMLElement} input An input.
	 * @returns {void}
	 */
	scrollToReselectedInput(input) {
		if (input === this.lastFocusedInput)
			this.scrollToElementTop(input);
	}

	/**
	 * Focuses input.
	 * @param {HTMLElement} input An input.
	 * @returns {void}
	 */
	focusInput(input) {
		input.focus();
		this.updateLastFocusedInput();
	}

	/**
	 * Blurs input.
	 * @param {HTMLElement} input An input.
	 * @returns {void}
	 */
	blurInput(input) { input.blur(); }

	/**
	 * Remembers currently focused input.
	 * @returns {void}
	 */
	updateLastFocusedInput() { this.lastFocusedInput = document.activeElement; }

	/**
	 * Scrolls window to the element's Y position.
	 * @param {HTMLElement} element Any visible element.
	 * @returns {void}
	 */
	scrollToElementTop(element) { window.scrollTo(0, this.elementDistanceFromPageTop(element)); }



	/**
	 * Span inside the first square of an answer with the number of its question.
	 * @param {number} questionNumberInt The number of the question.
	 * @returns {HTMLElement}
	 */
	getFirstSquareQuestionNumberByQuestionNumberInt(questionNumberInt) { return this.crossword.querySelector("#first-square-question-number-" + questionNumberInt); }

	/**
	 * First square of the answer of a question with this number.
	 * @param {number} questionNumberInt The number of the question.
	 * @returns {HTMLElement}
	 */
	getFirstSquareByQuestionNumberInt(questionNumberInt) { return this.firstSquaresInOrder[questionNumberInt-1]; }



	/**
	 * Adds class that indicates the question can be selected.
	 * @param {HTMLElement} question The touched question.
	 * @returns {void}
	 */
	numberedQuestionTouchStartEvent(question) { question.classList.add("touch-started"); }

	/**
	 * Removes class that indicates the question can be selected.
	 * @param {HTMLElement} question The touched question.
	 * @returns {void}
	 */
	numberedQuestionTouchMoveEvent(question) { question.classList.remove("touch-started"); }

	/**
	 * Adds class that indicates the question is selected.
	 * @param {HTMLElement} question The touched question.
	 * @returns {void}
	 */
	numberedQuestionTouchEndEvent(question) {
		if (question.classList.contains("touch-started"))
			question.classList.add("question--selected");
		question.classList.remove("touch-started");
	}

	/**
	 * Blurs input if window is too far.
	 * @returns {void}
	 */
	scrollEvent() {
		const input = document.activeElement;
	
		if (this.elementIsInput(input) &&
			(this.elementIsAboveScreen(this.numberedQuestionsContainer) ||
			this.elementIsUnderScreen(this.form)))
			this.blurInput(input);
	}

	/**
	 * False means the element's bottom is not over the screen's top.
	 * @param {HTMLElement} element The element to check.
	 * @returns {boolean}
	 */
	elementIsAboveScreen(element) {
		const elementBottom = this.elementDistanceFromPageTop(element) + element.getBoundingClientRect().height;
		const screenTop = window.scrollY;

		return elementBottom < screenTop;
	}

	/**
	 * False means the element's top is not under the screen's bottom.
	 * @param {HTMLElement} element The element to check.
	 * @returns {boolean}
	 */
	elementIsUnderScreen(element) {
		const screenBottom = window.scrollY + window.innerHeight;
		const elementTop = this.elementDistanceFromPageTop(element);

		return elementTop > screenBottom;
	}

	/**
	 * Submits user's answers, displays result and scrolls to result.
	 * @param {HTMLElement} event The submit event.
	 * @returns {void}
	 */
	submitEvent(event) {
		event.preventDefault();
		this.scrollToElementTop(this.title);
		this.submitAnswers();
	}

	

	/**
	 * Submits user's answers, removes submit buttom, gives loading warning and displays result.
	 * @returns {void}
	 */
	submitAnswers() {
		this.removeSubmitButton();
		this.giveEvaluatingWarning();
	
		// Prepares user's answers to be sent.
		const url = this.getEvaluationUrl();
		const formData = this.getFormDataWithUserAnswers();
	
		// Prepares Request to send user's answers.
		const request = this.getNewRequestReadyToSend(url);
		this.setWhatToDoWhenRequestIsAnswered(request);

		// Uses Request to send user's answers.
		this.sendRequest(request, formData);
	}

	/**
	 * Removes submit buttom.
	 * @returns {void}
	 */
	removeSubmitButton() {
		this.submitButton.remove();
		this.submitButton = null;
	}

	/**
	 * Displays a loading message.
	 * @returns {void}
	 */
	giveEvaluatingWarning() { this.evaluationFeedback.innerHTML = "Avaliando..."; }
	
	/**
	 * Page responsable for receiving the user's answers and returning the result.
	 * @returns {string}
	 */
	getEvaluationUrl() { return "controller/php/result/giveResult.php"; }

	/**
	 * User's inputs' information to analyze.
	 * @returns {FormData} [#input-vPos-hPos => char]
	 */
	getFormDataWithUserAnswers() {
		const formData = new FormData();
		for (let i = 0; i < this.inputs.length; i++) {
			// Ex. input-5-2
			const answerKey = this.inputs[i].id;

			// Ex. c
			const answerValue = this.inputs[i].value.toLowerCase();
			
			// Ex. [input-5-2 => c]
			formData.append(answerKey, answerValue);
		}

		return formData;
	}

	/**
	 * Request that will be used to send the FormData.
	 * @param {string} url Page to receive the user's answers and return the result.
	 * @returns {Request}
	 */
	getNewRequestReadyToSend(url) {
		const request = new XMLHttpRequest();

		// Prepares Request to be sent.
		request.open("POST", url, true);

		return request;
	}

	/**
	 * Displays result when it's ready.
	 * @param {Request} request Will be used to send the FormData.
	 * @returns {void}
	 */
	setWhatToDoWhenRequestIsAnswered(request) {
		// This function will be executed when the Request is answered.
		function displayResultWhenRequestIsReady() {
			if (request.readyState == XMLHttpRequest.DONE) {
				this.displayResult(request.responseText);
			}
		}

		request.onreadystatechange = displayResultWhenRequestIsReady.bind(this);
	}

	/**
	 * Uses the Request to send the FormData to the proper page.
	 * @param {Request} request Will be used to send the FormData.
	 * @param {FormData} formData Contains the user's answers.
	 * @returns {void}
	 */
	sendRequest(request, formData) { request.send(formData); }


	
	/**
	 * Displays result as HTML.
	 * @param {Request} result Received after analyzing the user's answers.
	 * @returns {void}
	 */
	displayResult(result) { this.evaluationFeedback.innerHTML = result; }
}



const crosswordsInteractor = new CrosswordsInteractor();