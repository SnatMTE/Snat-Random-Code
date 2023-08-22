Hangman Game: Textual Flow Explanation

Importing Modules:
The program starts by importing the necessary module, random, which is used for random word selection.

Word List Setup:
The program defines a list called word_list containing various words that the Hangman game can use as the secret word to guess.

Choosing a Random Word:
The function choose_word() randomly selects a word from the word_list.

Displaying the Word:
The function display_word() takes the chosen word and the letters that have been guessed so far, and constructs a string that displays the word with guessed letters filled in and unguessed letters as underscores.

Main Hangman Game Loop:
The main part of the game starts in the hangman() function. It displays a welcome message and initializes variables:

chosen_word: The randomly chosen word to guess.
guessed_letters: A list to store letters guessed by the player.
attempts: The number of attempts the player has to guess the word (6 in this case).
Gameplay Loop:
The program enters a loop where the player's guesses are processed:

The player is informed about the remaining attempts and their guessed letters.
The current display of the word (with underscores and guessed letters) is shown.
The player is asked to input a letter as their guess.
The program validates the guess:
If the input is not a single letter, an error message is displayed.
If the letter has already been guessed, the player is notified.
If the input is valid and new, the letter is added to guessed_letters.
Correct Guess Handling:
If the guessed letter is in the chosen word:

A "Correct guess!" message is displayed.
If the entire word has been guessed, a victory message is shown and the loop breaks.
Incorrect Guess Handling:
If the guessed letter is not in the chosen word:

An "Incorrect guess." message is displayed.
The player's attempts are decremented.
Game Over:
When the loop ends, the program checks if the player has run out of attempts:

If attempts are exhausted, a message reveals the correct word and the player's loss.
If the word is guessed, a congratulatory message displays the correct word and the player's victory.
Starting the Game:
The Hangman game is initiated by calling the hangman() function.
