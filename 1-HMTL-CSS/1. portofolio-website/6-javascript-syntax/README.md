# JavaScript Syntax Basics — Level 7

This folder is different from the others. There is no portfolio here — just JavaScript basics. Open `index.html` in a browser, click each "Run" button, and read the output.

No setup needed. No Node.js. Just a browser.

---

## What's covered

### 1. Variables

A variable is a box that holds information. You give it a name so you can use it later.

```js
let name = "Vanya"; // let = can be changed later
const age = 20; // const = locked, never changes
```

Use `const` by default. Only use `let` if you know the value will change later. Avoid `var` — it's the old way and causes confusing bugs.

---

### 2. Data Types

The kind of value stored in a variable.

| Type      | Example           | What it is               |
| --------- | ----------------- | ------------------------ |
| string    | `"Hello"`         | Text, always in quotes   |
| number    | `42` or `3.14`    | Any number, no quotes    |
| boolean   | `true` or `false` | Only two possible values |
| null      | `null`            | Empty on purpose         |
| undefined | `undefined`       | Not set yet              |

---

### 3. Input & Output

- `console.log("hello")` — prints to DevTools console (F12). Developers use this all the time to check what's happening in code.
- `alert("hello")` — shows a popup. Annoying in real apps, but useful for quick testing.
- `prompt("your name?")` — asks user to type something. Whatever they type comes back as a string.

---

### 4. If / Else

Make the code decide what to do based on a condition.

```js
if (score >= 90) {
  // runs if score is 90 or more
} else if (score >= 70) {
  // runs if score is 70–89
} else {
  // runs if nothing above matched
}
```

Comparison operators: `===` (equal), `!==` (not equal), `>`, `<`, `>=`, `<=`

---

### 5. For Loop

Repeat something a set number of times.

```js
for (let i = 1; i <= 5; i++) {
  // runs 5 times: i goes 1, 2, 3, 4, 5
}
```

Three parts: `start (let i = 1)` → `condition (i <= 5)` → `step (i++)`.

---

### 6. While Loop

Keep repeating as long as something is true. You don't need to know how many times upfront.

```js
while (count <= 3) {
  count++; // always change something or it loops forever!
}
```

---

### 7. Functions

A function is a reusable block of code. Write it once, call it as many times as needed.

```js
function greet(name) {
  // name = input going IN
  return "Hello " + name; // return = output coming OUT
}

greet("Vanya"); // → "Hello Vanya"
greet("Ahmad"); // → "Hello Ahmad"
```

---

### 8. Arrays

A list of items. Position numbers start at **0**, not 1.

```js
let fruits = ["apple", "banana", "mango"];
fruits[0]; // "apple"
fruits.length; // 3
fruits.push("grape"); // adds to end
fruits.pop(); // removes last item
```

---

### 9. Objects

Groups related information together. Access values using a dot.

```js
let person = {
  name: "Vanya",
  age: 20,
};

person.name; // "Vanya"
person.age; // 20
```

---

## Try this

1. In `script.js`, change the `score` in `runIfElse()` to different numbers (40, 70, 95) and click Run again to see different grades.
2. In `runForLoop()`, change `i <= 5` to `i <= 10` — see it count to 10.
3. In `runObjects()`, add a new key like `job: "developer"` to the object and `show()` it.

## What's next

`8-javascript-dom/` — connecting JavaScript to the actual HTML page. This is where it gets exciting: change text, react to clicks, show/hide elements. That's what makes websites interactive.
