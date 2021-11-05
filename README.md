# RandomCrosswordsGenerator
A **PHP** Model-View-Crontroller random crosswords generator, which creates a different crossword every time the page is loaded. Includes complete interface, with **HTML**, **CSS** and **JS**.

How to use it:
1. In model/**AnswersAndQuestions.php**, create a private method for each theme you want to have.
2. Inside each of these methods, return the array **[$answers, $questions]**. $answers should be an array with all the **possible answers** about the theme ([a1, a2, ...]); $questions should be an array with all the **possible questions for each answer** ([[a1_q1, a1_q2, ...], [a2_q1, a2_q2, ...]], ...). Unless two answers have a **different number of letters** or **no letters in common**, they mustn’t share a question (I’ve avoided the question "Mythological animal" in my example because both gryphoN and unicorN were possible answers, but I’ve used "Is always drinking tea" for both the Hatter and the March Hare).
3. In the **switch** of the method **getAnswersAndPossibleQuestionsForEachAnswerByTheme**, create a case for each **isolated theme**, returning its method.
4. In **Page.php**, instead of just "new CrosswordCreator()", you may instantiate the CrosswordCreator with the parameters **$min_entries**, **$initial_max_vertical_squares** and **$max_horizontal_squares**. (Changing the last one would require some CSS work, though.)
5. In the **root**, create a different **PHP page** for each set of themes you want to have (theme-1.php, theme-1-2-and-3.php, ...).
6. In **each page**’s code, instantiate the **class Page** with "$page = new Page("Set of themes’ name", ['theme_1', 'theme_2', '...']);".
7. **Insert the crossword**’s interface where you want it to show with "$page->display();".
8. In **images/**, you may exchange the files **direction-selector.svg** and **space-marker.svg** for your own SVGs with the same name.
9. Change the file view/styles/**crosswords.css** according to your design.

You can see it working at:
1. [Wonderland Random Crosswords – Ralf’s Portfolio](http://ralf.infinityfreeapp.com/random_crosswords_generator/alices-adventures-in-wonderland.php);
2. [Looking-Glass Random Crosswords – Ralf’s Portfolio](http://ralf.infinityfreeapp.com/random_crosswords_generator/through-the-looking-glass.php); and
3. [Wonderland and Looking-Glass Random Crosswords – Ralf’s Portfolio](http://ralf.infinityfreeapp.com/random_crosswords_generator/alices-adventures-in-wonderland-and-through-the-looking-glass.php).