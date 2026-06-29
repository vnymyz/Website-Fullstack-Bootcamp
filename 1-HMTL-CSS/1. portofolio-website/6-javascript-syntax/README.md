# JavaScript Syntax Basics — Level 6

Open `index.html` in a browser. Click each "Run" button to see results. Use the **Code Playground** at the bottom to write and test your own code.

No setup needed. No Node.js. Just a browser.

---

## Practice Code — Copy these into the Playground and run them

Try every single block below. Change the values. Break it. Fix it. That is how you learn.

---

### 1. Variables

```js
// let = can change later
// const = locked forever
// Always use const first, switch to let only if needed

let name = "Vanya";
let age = 20;
const country = "Indonesia";

console.log(name);
console.log(age);
console.log(country);

// Try: change "Vanya" to your own name and run again
```

```js
// Changing a let variable — this works
let score = 50;
score = 90;
console.log(score);   // 90

// Changing a const — this causes an error, try it!
const pi = 3.14;
pi = 99;   // ← ERROR: you cannot change a const
```

---

### 2. Data Types

```js
// String = text in quotes
let firstName = "Vanya";
let greeting = "Hello, world!";
console.log(typeof firstName);   // "string"

// Number = any number, no quotes needed
let age = 20;
let price = 9.99;
console.log(typeof age);         // "number"

// Boolean = only true or false
let isLoggedIn = true;
let hasAccount = false;
console.log(typeof isLoggedIn);  // "boolean"

// Null = empty on purpose
let cart = null;
console.log(cart);               // null

// Undefined = declared but no value given yet
let address;
console.log(address);            // undefined
```

```js
// Joining strings together with +
let firstName = "Vanya";
let lastName = "Doe";
let fullName = firstName + " " + lastName;
console.log(fullName);           // "Vanya Doe"

// Number + number = math
console.log(10 + 5);    // 15

// String + number = joined as text (careful!)
console.log("Age: " + 20);  // "Age: 20"
```

---

### 3. Input & Output

```js
// console.log — your best friend for checking values
let x = 100;
console.log(x);
console.log("x is:", x);
console.log("type:", typeof x);
```

```js
// alert — shows a popup (use for quick testing only)
alert("This is a popup!");
```

```js
// prompt — asks user to type something
// whatever they type comes back as a string
let userName = prompt("What is your name?");
console.log("Hello, " + userName);
```

```js
// Full input/output example
let yourName = prompt("Enter your name:");
let yourAge = prompt("Enter your age:");

// prompt always returns a string, even if you type a number
// Number() converts it to a real number
let ageAsNumber = Number(yourAge);

alert("Hi " + yourName + "! Next year you will be " + (ageAsNumber + 1));
```

---

### 4. If / Else

```js
// Basic if / else
let hour = 14;   // try changing this number

if (hour < 12) {
  console.log("Good morning!");
} else if (hour < 18) {
  console.log("Good afternoon!");
} else {
  console.log("Good evening!");
}
```

```js
// Comparison operators
// ===  exactly equal
// !==  not equal
// >    greater than
// <    less than
// >=   greater than or equal
// <=   less than or equal

let age = 17;

if (age >= 18) {
  console.log("You can vote.");
} else {
  console.log("Too young to vote. " + (18 - age) + " years to go.");
}
```

```js
// Combining conditions
// &&  means AND (both must be true)
// ||  means OR (at least one must be true)

let hasTicket = true;
let isOldEnough = true;

if (hasTicket && isOldEnough) {
  console.log("You can enter.");
} else {
  console.log("You cannot enter.");
}
```

```js
// Grade calculator — try changing the score
let score = 75;

if (score >= 90) {
  console.log("A — Excellent!");
} else if (score >= 80) {
  console.log("B — Good!");
} else if (score >= 70) {
  console.log("C — Okay.");
} else if (score >= 60) {
  console.log("D — Needs work.");
} else {
  console.log("F — Please study more.");
}
```

---

### 5. For Loop

```js
// Basic for loop — counts 1 to 5
for (let i = 1; i <= 5; i++) {
  console.log("Number: " + i);
}

// Try: change i <= 5 to i <= 10
```

```js
// Loop through an array
let fruits = ["apple", "banana", "mango", "grape"];

for (let i = 0; i < fruits.length; i++) {
  console.log(i + ": " + fruits[i]);
}
```

```js
// Multiplication table
let number = 3;   // try changing this

for (let i = 1; i <= 10; i++) {
  console.log(number + " x " + i + " = " + (number * i));
}
```

```js
// for...of — simpler way to loop through a list
let colors = ["red", "green", "blue"];

for (let color of colors) {
  console.log("Color: " + color);
}
```

---

### 6. While Loop

```js
// Basic while loop
let count = 1;

while (count <= 5) {
  console.log("Count: " + count);
  count++;   // count++ = count + 1 — always do this or it loops forever!
}
```

```js
// Countdown using while
let countdown = 5;

while (countdown > 0) {
  console.log(countdown + "...");
  countdown--;   // countdown-- = countdown - 1
}

console.log("Go!");
```

---

### 7. Functions

```js
// Basic function — no input, no output
function sayHello() {
  console.log("Hello!");
}

sayHello();   // call it
sayHello();   // call it again — runs the same code
```

```js
// Function with input (parameter)
function greet(name) {
  console.log("Hello, " + name + "!");
}

greet("Vanya");
greet("Ahmad");
greet("Siti");
```

```js
// Function with input AND output (return)
function add(a, b) {
  return a + b;
}

let result = add(10, 5);
console.log(result);           // 15
console.log(add(3, 7));        // 10
console.log(add(100, 200));    // 300
```

```js
// Real example: function that checks age
function canVote(age) {
  if (age >= 18) {
    return "Yes, can vote.";
  } else {
    return "No, too young. " + (18 - age) + " years to go.";
  }
}

console.log(canVote(20));   // Yes
console.log(canVote(15));   // No
console.log(canVote(18));   // Yes
```

```js
// Arrow function — shorter way to write a function (same thing)
const multiply = (a, b) => a * b;

console.log(multiply(4, 5));   // 20
console.log(multiply(3, 3));   // 9
```

---

### 8. Arrays

```js
// Create an array
let fruits = ["apple", "banana", "mango"];

console.log(fruits);           // whole array
console.log(fruits[0]);        // "apple"  ← position 0 (not 1!)
console.log(fruits[1]);        // "banana"
console.log(fruits.length);    // 3
```

```js
// Adding and removing items
let fruits = ["apple", "banana"];

fruits.push("mango");         // add to END
console.log(fruits);          // ["apple", "banana", "mango"]

fruits.pop();                 // remove from END
console.log(fruits);          // ["apple", "banana"]

fruits.unshift("grape");      // add to START
console.log(fruits);          // ["grape", "apple", "banana"]

fruits.shift();               // remove from START
console.log(fruits);          // ["apple", "banana"]
```

```js
// Loop through array and do something with each item
let numbers = [10, 20, 30, 40, 50];
let total = 0;

for (let num of numbers) {
  total = total + num;
}

console.log("Total:", total);       // 150
console.log("Average:", total / numbers.length);  // 30
```

```js
// Array of objects — very common in real apps
let students = [
  { name: "Vanya", grade: 90 },
  { name: "Ahmad", grade: 75 },
  { name: "Siti",  grade: 85 }
];

for (let student of students) {
  console.log(student.name + " scored " + student.grade);
}
```

---

### 9. Objects

```js
// Create an object
let person = {
  name: "Vanya",
  age: 20,
  city: "Jakarta",
  isStudent: true
};

console.log(person.name);       // "Vanya"
console.log(person.age);        // 20
console.log(person);            // whole object
```

```js
// Update and add properties
let person = {
  name: "Vanya",
  age: 20
};

person.age = 21;            // update existing
person.job = "developer";   // add new property
console.log(person);
```

```js
// Object inside a function
function introduce(person) {
  console.log("Hi! My name is " + person.name + " and I am " + person.age + " years old.");
}

let vanya = { name: "Vanya", age: 20 };
let ahmad = { name: "Ahmad", age: 22 };

introduce(vanya);
introduce(ahmad);
```

---

### 10. Putting It All Together

```js
// Mini grade report — uses variables, array, loop, if/else, function

let students = [
  { name: "Vanya", score: 92 },
  { name: "Ahmad", score: 67 },
  { name: "Siti",  score: 80 },
  { name: "Budi",  score: 45 }
];

function getGrade(score) {
  if (score >= 90) return "A";
  if (score >= 80) return "B";
  if (score >= 70) return "C";
  if (score >= 60) return "D";
  return "F";
}

for (let student of students) {
  let grade = getGrade(student.score);
  console.log(student.name + " — Score: " + student.score + " — Grade: " + grade);
}
```

---

## What's next

`7-javascript-dom/` — connecting JavaScript to the actual HTML page: change text on screen, react to button clicks, show/hide elements. That's what makes a website actually interactive.
