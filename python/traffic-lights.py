# Coded for LSIB - Principles of Computer Programming
# Matthew Ellis - 2023

import tkinter as tk
import time

class TrafficLightSimulator:
    def __init__(self, master):
        self.master = master
        self.master.title("Traffic Light Simulator")
        self.current_signal = "red"
        self.signal_duration = 5
        self.is_running = False

        self.red_light = tk.Label(self.master, width=10, height=5, bg="red", relief="groove")
        self.yellow_light = tk.Label(self.master, width=10, height=5, bg="gray", relief="groove")
        self.green_light = tk.Label(self.master, width=10, height=5, bg="gray", relief="groove")

        self.red_light.grid(row=0, column=0, padx=10, pady=5)
        self.yellow_light.grid(row=0, column=1, padx=10, pady=5)
        self.green_light.grid(row=0, column=2, padx=10, pady=5)

        self.start_button = tk.Button(self.master, text="Start", command=self.start_simulation)
        self.stop_button = tk.Button(self.master, text="Stop", command=self.stop_simulation)
        self.duration_label = tk.Label(self.master, text="Duration (s):")
        self.duration_entry = tk.Entry(self.master, width=5)
        self.duration_entry.insert(0, str(self.signal_duration))

        self.start_button.grid(row=1, column=0, padx=10, pady=5)
        self.stop_button.grid(row=1, column=1, padx=10, pady=5)
        self.duration_label.grid(row=1, column=2, padx=10, pady=5)
        self.duration_entry.grid(row=1, column=3, padx=10, pady=5)

    def start_simulation(self):
        if not self.is_running:
            self.is_running = True
            self.update_signals()

    def stop_simulation(self):
        if self.is_running:
            self.is_running = False

    def update_signals(self):
        if self.is_running:
            self.signal_duration = int(self.duration_entry.get())

            if self.current_signal == "red":
                self.red_light.config(bg="red")
                self.yellow_light.config(bg="gray")
                self.green_light.config(bg="gray")
                self.current_signal = "yellow"
            elif self.current_signal == "yellow":
                self.red_light.config(bg="gray")
                self.yellow_light.config(bg="yellow")
                self.green_light.config(bg="gray")
                self.current_signal = "green"
            elif self.current_signal == "green":
                self.red_light.config(bg="gray")
                self.yellow_light.config(bg="gray")
                self.green_light.config(bg="green")
                self.current_signal = "red"

            self.master.after(self.signal_duration * 1000, self.update_signals)

if __name__ == "__main__":
    root = tk.Tk()
    app = TrafficLightSimulator(root)
    root.mainloop()
