# Coded by M.Terra Ellis
# Example Hangman Game

import tkinter as tk
from tkinter import messagebox
import random

class HangmanGame:
    def __init__(self, master):
        self.master = master
        self.master.title("Hangman Game")
        self.wordlist = [
            {"word": "variable", "meaning": "A storage location identified by a memory address and an associated symbolic name (an identifier)"},
            {"word": "array", "meaning": "A collection of items stored at contiguous memory locations and identified by an index or key"},
            {"word": "object", "meaning": "An instance of a class in object-oriented programming, consisting of data and methods"},
            {"word": "function", "meaning": "A block of code designed to perform a specific task, often reusable"},
            {"word": "algorithm", "meaning": "A step-by-step procedure or formula for solving a problem or accomplishing a task"},
            {"word": "loop", "meaning": "A control flow statement that allows code to be executed repeatedly"},
            {"word": "class", "meaning": "A blueprint for creating objects in object-oriented programming"},
            {"word": "method", "meaning": "A function that is associated with an object in object-oriented programming"},
            {"word": "interface", "meaning": "A set of functions or methods that allow interaction with an object or system"},
            {"word": "inheritance", "meaning": "A mechanism in object-oriented programming that allows one class to inherit properties and methods from another"}
        ]
        self.word_info = {}
        self.word = ""
        self.hidden_word = []
        self.guesses = 0
        self.max_guesses = 7
        self.used_letters = set()

        self.initialize_game()

        # Word label
        self.word_label = tk.Label(master, text=" ".join(self.hidden_word), font=("Helvetica", 20))
        self.word_label.pack(pady=20)

        # Meaning button
        meaning_button = tk.Button(master, text="Show Meaning", font=("Helvetica", 12), command=self.show_meaning)
        meaning_button.config(width=15, height=2)
        meaning_button.pack(pady=10)

        # Keyboard in Query centered in the screen
        keyboard_frame = tk.Frame(master)
        keyboard_frame.pack()
        self.letter_buttons = []
        for letter in "abcdefghijklmnopqrstuvwxyz":
            button = tk.Button(keyboard_frame, text=letter, font=("Helvetica", 12), command=lambda l=letter: self.make_guess(l))
            button.config(width=4, height=2)
            button.grid(row=(ord(letter) - ord('a')) // 7, column=(ord(letter) - ord('a')) % 7, padx=5, pady=5)
            self.letter_buttons.append(button)

        # Picture of the hangman progress (ASCII art)
        self.hangman_image = tk.Label(master, text="")
        self.hangman_image.pack(pady=20)

        # Button to restart and quit
        restart_button = tk.Button(master, text="Restart", font=("Helvetica", 12), command=self.restart_game)
        restart_button.config(width=8, height=2)
        restart_button.pack(side=tk.LEFT, padx=10)
        quit_button = tk.Button(master, text="Quit", font=("Helvetica", 12), command=self.quit_game)
        quit_button.config(width=8, height=2)
        quit_button.pack(side=tk.RIGHT, padx=10)

    def initialize_game(self):
        self.word_info = random.choice(self.wordlist)
        self.word = self.word_info["word"]
        self.hidden_word = ["_" if char.isalpha() else char for char in self.word]
        self.guesses = 0
        self.used_letters = set()

    def make_guess(self, guess):
        if guess in self.used_letters:
            return  # Do nothing if the letter has already been used

        self.used_letters.add(guess)

        if guess in self.word:
            for i in range(len(self.word)):
                if self.word[i] == guess:
                    self.hidden_word[i] = guess
            self.word_label.config(text=" ".join(self.hidden_word))
            if "".join(self.hidden_word) == self.word:
                messagebox.showinfo("Congratulations!", f"You won! The word was: {self.word}")
                self.restart_game()
        else:
            self.guesses += 1
            if self.guesses >= self.max_guesses:
                messagebox.showinfo("Game Over", f"You lost! The word was: {self.word}")
                self.restart_game()
            else:
                self.update_hangman_image()

        # Disable the used letter button
        for button in self.letter_buttons:
            if button["text"] == guess:
                button.config(state="disabled")

    def update_hangman_image(self):
        hangman_images = [
            "  +---+\n  |   |\n      |\n      |\n      |\n      |",
            "  +---+\n  |   |\n  O   |\n      |\n      |\n      |",
            "  +---+\n  |   |\n  O   |\n  |   |\n      |\n      |",
            "  +---+\n  |   |\n  O   |\n /|   |\n      |\n      |",
            "  +---+\n  |   |\n  O   |\n /|\\  |\n      |\n      |",
            "  +---+\n  |   |\n  O   |\n /|\\  |\n /    |\n      |",
            "  +---+\n  |   |\n  O   |\n /|\\  |\n / \\  |\n      |"
        ]
        self.hangman_image.config(text=hangman_images[self.guesses])

    def restart_game(self):
        self.initialize_game()
        self.word_label.config(text=" ".join(self.hidden_word))
        self.hangman_image.config(text="")
        for button in self.letter_buttons:
            button.config(state="normal")

    def quit_game(self):
        self.master.destroy()

    def show_meaning(self):
        meaning = self.word_info.get("meaning", "Meaning not available")
        messagebox.showinfo("Word Meaning", meaning)

def main():
    root = tk.Tk()
    root.attributes('-fullscreen', True)  # Start in full-screen mode

    # Calculate the position to center the window
    screen_width = root.winfo_screenwidth()
    screen_height = root.winfo_screenheight()
    window_width = 800  # Adjust to your desired window size
    window_height = 600  # Adjust to your desired window size
    x = (screen_width - window_width) // 2
    y = (screen_height - window_height) // 2

    root.geometry(f"{window_width}x{window_height}+{x}+{y}")
    hangman = HangmanGame(root)
    root.mainloop()

if __name__ == "__main__":
    main()
