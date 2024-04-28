import random

def display_welcome_message():
    print("Welcome to Rock, Paper, Scissors!")
    print("Instructions: Enter your choice (rock, paper, or scissors) and see if you can beat the computer.\n")

def get_player_choice():
    while True:
        choice = input("Enter your choice (rock, paper, or scissors): ").lower()
        if choice in ['rock', 'paper', 'scissors']:
            return choice
        else:
            print("Invalid choice. Please enter 'rock', 'paper', or 'scissors'.")

def determine_winner(player_choice, computer_choice):
    if player_choice == computer_choice:
        return "It's a tie!"
    elif (player_choice == 'rock' and computer_choice == 'scissors') or \
         (player_choice == 'paper' and computer_choice == 'rock') or \
         (player_choice == 'scissors' and computer_choice == 'paper'):
        return "You win!"
    else:
        return "Computer wins!"

def display_choices(choice):
    if choice == 'rock':
        print("""
        _______
    ---'   ____)
          (_____)
          (_____)
          (____)
    ---.__(___)
        """)
    elif choice == 'paper':
        print("""
         _______
    ---'    ____)____
               ______)
              _______)
             _______)
    ---.__________)
        """)
    elif choice == 'scissors':
        print("""
        _______
    ---'   ____)____
              ______)
           __________)
          (____)
    ---.__(___)
        """)

def main():
    display_welcome_message()
    while True:
        player_choice = get_player_choice()
        computer_choice = random.choice(['rock', 'paper', 'scissors'])
        print("\nYou chose:")
        display_choices(player_choice)
        print("\nComputer chose:")
        display_choices(computer_choice)
        print(determine_winner(player_choice, computer_choice))
        while True:
            play_again = input("Do you want to play again? (yes/no): ").lower()
            if play_again == 'yes' or play_again == 'no':
                break
            else:
                print("Invalid input. Please enter 'yes' or 'no'.")
        if play_again == 'no':
            print("Thanks for playing!")
            break
        else:
            print("\nLet's play again!")

if __name__ == "__main__":
    main()
