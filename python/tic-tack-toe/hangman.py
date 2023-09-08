import random

# List of words for the Hangman game
word_list = ["apple", "banana", "cherry", "date", "elderberry", "fig", "grape"]

def choose_word(word_list):
    """Selects a random word from the word list."""
    return random.choice(word_list)

def display_word(word, guessed_letters):
    """
    Displays the current state of the word with guessed letters filled in.
    For letters not yet guessed, underscores are displayed.
    """
    display = ""
    for letter in word:
        if letter in guessed_letters:
            display += letter
        else:
            display += "_"
    return display

def hangman():
    print("Welcome to Hangman!")
    
    # Select a random word from the word list
    chosen_word = choose_word(word_list)
    
    # List to store guessed letters
    guessed_letters = []
    
    # Number of attempts the player has
    attempts = 6
    
    while attempts > 0:
        print("\nAttempts left:", attempts)
        print("Guessed letters:", guessed_letters)
        
        # Display the current state of the word with guessed letters
        current_display = display_word(chosen_word, guessed_letters)
        print("Current word:", current_display)
        
        # Ask the player to guess a letter
        guess = input("Guess a letter: ").lower()
        
        # Check if the guessed letter is valid (a single letter)
        if len(guess) != 1 or not guess.isalpha():
            print("Invalid input. Please enter a single letter.")
            continue
        
        # Check if the letter has already been guessed
        if guess in guessed_letters:
            print("You've already guessed that letter.")
            continue
        
        # Add the guessed letter to the list of guessed letters
        guessed_letters.append(guess)
        
        # Check if the guessed letter is in the chosen word
        if guess in chosen_word:
            print("Correct guess!")
            
            # Check if the player has guessed the whole word
            if chosen_word == display_word(chosen_word, guessed_letters):
                print("\nCongratulations! You've guessed the word:", chosen_word)
                break
        else:
            print("Incorrect guess.")
            attempts -= 1
            
    # Game over messages
    if attempts == 0:
        print("\nSorry, you're out of attempts. The word was:", chosen_word)

# Start the Hangman game
hangman()
