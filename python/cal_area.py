def calculate_area_using_divide_and_conquer(length, width):
    # Base case: Check if either length or width is less than or equal to zero
    if length <= 0 or width <= 0:
        return 0

    # Base case: Check if either length or width is 1
    if length == 1 or width == 1:
        return max(length, width)

    # Divide: Split the rectangle into two smaller rectangles
    half_width = width // 2

    # Conquer: Recursively calculate the areas of the two smaller rectangles
    area1 = calculate_area_using_divide_and_conquer(length, half_width)
    area2 = calculate_area_using_divide_and_conquer(length, half_width + (width % 2))

    # Combine: Add the areas of the two smaller rectangles
    total_area = area1 + area2

    return total_area

def main():
    length = float(input("Please enter the length of the rectangle: "))
    width = float(input("Please enter the width of the rectangle: "))

    area = calculate_area_using_divide_and_conquer(length, width)
    print("The area of the rectangle is:", area)

if __name__ == "__main__":
    main()
