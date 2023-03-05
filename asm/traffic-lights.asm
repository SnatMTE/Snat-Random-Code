ORG 0

MOV P1, #0x01 ; Start with green light on

LOOP_GREEN:
MOV R2, #30 ; Delay for 30 seconds
CALL DELAY
MOV P1, #0x02 ; Turn on amber light
CALL RANDOM ; Generate a random delay for amber light
MOV P1, #0x04 ; Turn on red light
MOV R2, #20 ; Delay for 20 seconds
CALL DELAY
JMP LOOP_GREEN

; Delay subroutine using Timer0
DELAY:
MOV TH0, #0x4C ; Load Timer0 with 1 MHz count for 1ms delay
MOV TL0, #0x00
SETB TR0 ; Start Timer0
WAIT_DELAY:
JNB TF0, WAIT_DELAY ; Wait until Timer0 overflows
CLR TF0 ; Clear Timer0 overflow flag
CLR TR0 ; Stop Timer0
RET

; Random subroutine to generate delay for amber light
RANDOM:
MOV R5, #0x0A ; Set upper bound for random number
MOV R4, #0x05 ; Set lower bound for random number
MOV A, R5 ; A = upper bound
SUBB A, R4 ; A = upper bound - lower bound
INC A ; A = number of possible values
MOV R6, #0x01 ; Set LSB of random number
CLR A ; Clear accumulator
MOV R7, #0x00 ; Clear carry flag
RANDOM_LOOP:
ADD A, R6 ; Add 1 to accumulator
RLC A ; Rotate accumulator left through carry
DJNZ R0, RANDOM_LOOP ; Decrement R0 until 0
DIV AB, R6 ; Divide accumulator by number of possible values
ADD A, R4 ; Add lower bound to random number
MOV R3, A ; Store random number in R3
MOV R2, #0x0A ; Delay for 10ms before returning
CALL DELAY
RET

END
