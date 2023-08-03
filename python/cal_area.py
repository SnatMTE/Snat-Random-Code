class Rectangle:
    def __init__(self, length, width):
        self.length = length
        self.width = width

    def calculate_area(self):
        return self.length * self.width


class Triangle:
    def __init__(self, base, height):
        self.base = base
        self.height = height

    def calculate_area(self):
        return 0.5 * self.base * self.height
trian

def get_user_input(message):
    try:
        value = float(input(message))
        if value <= 0:
            raise ValueError("Please enter a positive number.")
        return value
    except ValueError as e:
        print(str(e))
        return get_user_input(message)


def main():
    print("Welcome to the Shape Area Calculator!")

    shape_type = input("Please enter the shape (rectangle or triangle): ").lower()

    if shape_type == "rectangle":
        length = get_user_input("Please enter the length of the rectangle: ")
        width = get_user_input("Please enter the width of the rectangle: ")

        shape = Rectangle(length, width)
    elif shape_type == "triangle":
        base = get_user_input("Please enter the base of the triangle: ")
        height = get_user_input("Please enter the height of the triangle: ")

        shape = Triangle(base, height)
    else:
        print("Invalid shape. Please enter either 'rectangle' or 'triangle'.")
        return

    area = shape.calculate_area()

    print(f"The area of the {shape_type} is: {area}")


if __name__ == "__main__":
    main()
