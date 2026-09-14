// script.js -- vanilla JS, TANPA React
let todos = [
  { id: 1, text: "Belajar HTML CSS JS", done: true },
  { id: 2, text: "Belajar React", done: false },
];

const listEl = document.getElementById("list-todo");

// Fungsi ini "manual" render ulang SELURUH list ke DOM.
// Tiap ada perubahan data, kita panggil ini lagi dari nol.
function render() {
  listEl.innerHTML = ""; // kosongin dulu, baru bikin ulang semua <li>
  todos.forEach((todo) => {
    const li = document.createElement("li");
    if (todo.done) li.classList.add("done");
    const span = document.createElement("span");
    span.textContent = todo.text;
    span.addEventListener("click", () => toggleTodo(todo.id));
    li.appendChild(span);
    listEl.appendChild(li);
  });
}

function toggleTodo(id) {
  todos = todos.map((t) => (t.id === id ? { ...t, done: !t.done } : t));
  render(); // manual: render ulang semua abis data berubah
}

render(); // render pertama kali
