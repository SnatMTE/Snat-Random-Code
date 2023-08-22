using System;

namespace TicTacToe
{
    class Program
    {
        static char[] board = { '1', '2', '3', '4', '5', '6', '7', '8', '9' };
        static char currentPlayer = 'X';

        static void Main(string[] args)
        {
            bool gameIsOver = false;

            while (!gameIsOver)
            {
                DisplayBoard();

                if (currentPlayer == 'X')
                {
                    int move = GetMove();
                    if (IsValidMove(move))
                    {
                        MakeMove(move);
                        gameIsOver = CheckForWin() || CheckForTie();
                        currentPlayer = 'O';
                    }
                    else
                    {
                        Console.WriteLine("Invalid move. Try again.");
                    }
                }
                else
                {
                    int aiMove = GetAIMove();
                    MakeMove(aiMove);
                    gameIsOver = CheckForWin() || CheckForTie();
                    currentPlayer = 'X';
                }
            }

            DisplayBoard();
            if (CheckForWin())
            {
                Console.WriteLine($"Player {(currentPlayer == 'X' ? 'O' : 'X')} wins!");
            }
            else
            {
                Console.WriteLine("It's a tie!");
            }
        }

        static void DisplayBoard()
        {
            Console.Clear();
            Console.WriteLine($" {board[0]} | {board[1]} | {board[2]} ");
            Console.WriteLine("---+---+---");
            Console.WriteLine($" {board[3]} | {board[4]} | {board[5]} ");
            Console.WriteLine("---+---+---");
            Console.WriteLine($" {board[6]} | {board[7]} | {board[8]} ");
        }

        static int GetMove()
        {
            Console.Write($"Player {currentPlayer}, enter your move (1-9): ");
            int move;
            while (!int.TryParse(Console.ReadLine(), out move) || move < 1 || move > 9)
            {
                Console.Write("Invalid input. Enter a valid move (1-9): ");
            }
            return move - 1; // Adjust to 0-based index
        }

        static int GetAIMove()
        {
            Random random = new Random();
            int aiMove;
            do
            {
                aiMove = random.Next(0, 9);
            } while (!IsValidMove(aiMove));
            return aiMove;
        }

        static bool IsValidMove(int move)
        {
            return board[move] != 'X' && board[move] != 'O';
        }

        static void MakeMove(int move)
        {
            board[move] = currentPlayer;
        }

        static bool CheckForWin()
        {
            // Check rows, columns, and diagonals for a win
            return (board[0] == board[1] && board[1] == board[2]) ||
                   (board[3] == board[4] && board[4] == board[5]) ||
                   (board[6] == board[7] && board[7] == board[8]) ||
                   (board[0] == board[3] && board[3] == board[6]) ||
                   (board[1] == board[4] && board[4] == board[7]) ||
                   (board[2] == board[5] && board[5] == board[8]) ||
                   (board[0] == board[4] && board[4] == board[8]) ||
                   (board[2] == board[4] && board[4] == board[6]);
        }

        static bool CheckForTie()
        {
            // Check if the board is full (no more valid moves)
            foreach (char cell in board)
            {
                if (cell != 'X' && cell != 'O')
                {
                    return false;
                }
            }
            return true;
        }
    }
}
