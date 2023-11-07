# Coded by M.Terra Ellis
# Example Hangman Game
import tkinter as tk
import random

class HangmanGame:
    def __init__(self, master):
        self.master = master
        self.master.title("Hangman Game")
        self.wordlist = ["python", "hangman", "programming", "developer", "interface", "example"]
        self.word = random.choice(self.wordlist)
        self.guesses = 0
        self.max_guesses = 5
        self.hidden_word = ["_" if char.isalpha() else char for char in self.word]

        self.word_label = tk.Label(master, text=" ".join(self.hidden_word), font=("Helvetica", 20))
        self.word_label.pack()

        self.input_label = tk.Label(master, text="Guess a letter: ", font=("Helvetica", 12))
        self.input_label.pack()

        self.entry = tk.Entry(master, font=("Helvetica", 12))
        self.entry.pack()

        self.guess_button = tk.Button(master, text="Guess", command=self.make_guess, font=("Helvetica", 12))
        self.guess_button.pack()

        self.message_label = tk.Label(master, text="", font=("Helvetica", 12))
        self.message_label.pack()

    def make_guess(self):
        guess = self.entry.get()
        if len(guess) != 1 or not guess.isalpha():
            self.message_label.config(text="Please enter a valid letter.")
            return

        if guess in self.word:
            for i in range(len(self.word)):
                if self.word[i] == guess:
                    self.hidden_word[i] = guess
            self.word_label.config(text=" ".join(self.hidden_word))
            if "".join(self.hidden_word) == self.word:
                self.message_label.config(text="You won! The word was: " + self.word)
                self.entry.config(state="disabled")
            self.entry.delete(0, "end")
        else:
            self.guesses += 1
            if self.guesses >= self.max_guesses:
                self.message_label.config(text="You lost! The word was: " + self.word)
                self.entry.config(state="disabled")
            else:
                self.message_label.config(text=f"Wrong guess! {self.max_guesses - self.guesses} guesses left.")
            self.entry.delete(0, "end")

def main():
    root = tk.Tk()
    hangman = HangmanGame(root)
    root.mainloop()

if __name__ == "__main__":
    main()
