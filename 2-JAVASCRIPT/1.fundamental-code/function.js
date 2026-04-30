function add(a, b) {
  return a + b;
}

function subtract(a, b) {
  return a - b;
}

function multiply(a, b) {
  return a * b;
}

function divide(a, b) {
  if (b === 0) {
    throw new Error("Cannot divide by zero");
  }
  return a / b;
}

function modulus(a, b) {
  if (b === 0) {
    throw new Error("Cannot perform modulus by zero");
  }
  return a % b;
}

const result = add(2, 3);
console.log(result); // Output: 5

const result2 = subtract(5, 2);
console.log(result2); // Output: 3

const result3 = multiply(4, 6);
console.log(result3); // Output: 24

const result4 = divide(10, 2);
console.log(result4); // Output: 5

const result5 = modulus(10, 3);
console.log(result5); // Output: 1
