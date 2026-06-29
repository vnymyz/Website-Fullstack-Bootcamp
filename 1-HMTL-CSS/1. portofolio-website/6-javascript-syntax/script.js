// ─────────────────────────────────────────
// PLAYGROUND — runs whatever code the student types
// ─────────────────────────────────────────
function runPlayground() {
  const code = document.getElementById("playground-input").value;
  const outputEl = document.getElementById("playground-output");
  const errorEl = document.getElementById("playground-error");

  outputEl.innerText = "";
  errorEl.innerText = "";

  // capture console.log calls so output appears on the page
  const logs = [];
  const originalLog = console.log;
  console.log = function (...args) {
    logs.push(args.map(a => (typeof a === "object" ? JSON.stringify(a, null, 2) : String(a))).join(" "));
    originalLog.apply(console, args); // still shows in DevTools too
  };

  try {
    // eval() runs a string as JS code — safe here since student runs their own code
    eval(code);
    outputEl.innerText = logs.length > 0 ? logs.join("\n") : "(no console.log output)";
  } catch (err) {
    errorEl.innerText = err.message;
  } finally {
    console.log = originalLog; // restore original console.log
  }
}

function clearPlayground() {
  document.getElementById("playground-output").innerText = "";
  document.getElementById("playground-error").innerText = "";
}

// HOW TO USE THIS FILE:
// Each function below runs when you click a button on the page.
// Results appear in the box below each button.
// Also open DevTools (F12 -> Console) to see console.log output.

// Helper: puts text into the output box on the page
function show(id, text) {
  document.getElementById(id).innerText = text;
}

// ─────────────────────────────────────────
// 1. VARIABLES
// let   = can be changed later
// const = locked, never changes
// ─────────────────────────────────────────
function runVariables() {
  let name = "Vanya";       // can change this later
  const age = 20;           // locked, always 20
  let isStudent = true;

  console.log(name);
  console.log(age);
  console.log(isStudent);

  show(
    "output-variables",
    "name = " + name + "\n" +
    "age = " + age + "\n" +
    "isStudent = " + isStudent
  );
}

// ─────────────────────────────────────────
// 2. DATA TYPES
// string  = text, always in quotes
// number  = any number, no quotes
// boolean = true or false only
// null    = empty on purpose
// ─────────────────────────────────────────
function runDataTypes() {
  let text    = "Hello world";
  let number  = 42;
  let decimal = 3.14;
  let isTrue  = true;
  let nothing = null;

  console.log(typeof text);    // "string"
  console.log(typeof number);  // "number"
  console.log(typeof isTrue);  // "boolean"
  console.log(typeof nothing); // "object" (quirk of JS!)

  show(
    "output-datatypes",
    '"Hello world" is type: ' + typeof text + "\n" +
    "42 is type: " + typeof number + "\n" +
    "true is type: " + typeof isTrue + "\n" +
    "null is type: " + typeof nothing + "  ← (JS quirk, not a mistake)"
  );
}

// ─────────────────────────────────────────
// 3. INPUT & OUTPUT
// prompt() = asks user to type something, returns what they typed
// alert()  = shows a popup message
// console.log() = prints to DevTools console
// ─────────────────────────────────────────
function runInputOutput() {
  let userName = prompt("Type your name:");

  if (userName) {
    alert("Hello, " + userName + "!");
    console.log("User typed:", userName);
    show("output-io", 'You typed: "' + userName + '"');
  } else {
    show("output-io", "You cancelled or typed nothing.");
  }
}

// ─────────────────────────────────────────
// 4. IF / ELSE IF / ELSE
// Comparison operators:
//   ===  equal to
//   !==  not equal to
//   >    greater than
//   <    less than
//   >=   greater than or equal to
//   <=   less than or equal to
// ─────────────────────────────────────────
function runIfElse() {
  let score = 75;
  let grade;

  if (score >= 90) {
    grade = "A — Excellent!";
  } else if (score >= 70) {
    grade = "B — Good job!";
  } else if (score >= 50) {
    grade = "C — Keep trying!";
  } else {
    grade = "D — Need more study.";
  }

  console.log("Score:", score, "→ Grade:", grade);
  show("output-ifelse", "Score: " + score + "\nGrade: " + grade);
}

// ─────────────────────────────────────────
// 5. FOR LOOP
// for (start; condition; step) { ... }
// i++ means i = i + 1
// runs until condition is false
// ─────────────────────────────────────────
function runForLoop() {
  let result = "";

  for (let i = 1; i <= 5; i++) {
    result += "Number: " + i + "\n";
    console.log("Number:", i);
  }

  show("output-for", result);
}

// ─────────────────────────────────────────
// 6. WHILE LOOP
// keeps running as long as condition is true
// WARNING: always make sure condition eventually becomes false
//          or it will loop forever and crash the browser tab
// ─────────────────────────────────────────
function runWhileLoop() {
  let count = 1;
  let result = "";

  while (count <= 3) {
    result += "Count is: " + count + "\n";
    console.log("Count is:", count);
    count++; // without this it loops forever!
  }

  show("output-while", result);
}

// ─────────────────────────────────────────
// 7. FUNCTIONS
// function name(parameter) { ... return value; }
// parameter = input going IN to the function
// return    = output coming OUT of the function
// ─────────────────────────────────────────
function greet(name) {
  return "Hello, " + name + "!";
}

function add(a, b) {
  return a + b;
}

function runFunctions() {
  let message = greet("Vanya");
  let sum = add(10, 5);

  console.log(message);
  console.log("10 + 5 =", sum);

  show(
    "output-functions",
    greet("Vanya") + "\n" +
    "10 + 5 = " + add(10, 5) + "\n" +
    "3 + 7 = " + add(3, 7)
  );
}

// ─────────────────────────────────────────
// 8. ARRAYS
// A list of items in square brackets []
// Position starts at 0, not 1
// .length  = how many items
// .push()  = add to end
// .pop()   = remove from end
// ─────────────────────────────────────────
function runArrays() {
  let fruits = ["apple", "banana", "mango"];

  console.log(fruits);
  console.log("First item:", fruits[0]);
  console.log("Total items:", fruits.length);

  fruits.push("grape"); // adds "grape" to the end
  console.log("After push:", fruits);

  let removed = fruits.pop(); // removes last item
  console.log("Removed:", removed);
  console.log("After pop:", fruits);

  show(
    "output-arrays",
    "Original: " + fruits.join(", ") + "\n" +
    "First item (position 0): " + fruits[0] + "\n" +
    "Total items: " + fruits.length + "\n\n" +
    "fruits.push('grape') → adds grape to end\n" +
    "fruits.pop()         → removes last item"
  );
}

// ─────────────────────────────────────────
// 9. OBJECTS
// Stores related info as key: value pairs
// Access with dot notation: object.key
// Like a contact card — name, age, job all together
// ─────────────────────────────────────────
function runObjects() {
  let person = {
    name: "Vanya",
    age: 20,
    isStudent: true,
    city: "Jakarta"
  };

  console.log(person);
  console.log("Name:", person.name);
  console.log("Age:", person.age);

  person.age = 21; // update a value
  console.log("Birthday! New age:", person.age);

  show(
    "output-objects",
    "name: " + person.name + "\n" +
    "age: " + person.age + "  (updated from 20 to 21)\n" +
    "isStudent: " + person.isStudent + "\n" +
    "city: " + person.city
  );
}
