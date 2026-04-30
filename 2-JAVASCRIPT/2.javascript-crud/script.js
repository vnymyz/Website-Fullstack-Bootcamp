// Disini ngasih tau kalau ada data di localstorage diambil.
// semisal enggak ada berarti di set kosong dulu atau defaultnya
let data = JSON.parse(localStorage.getItem("data")) || [];
let comments = JSON.parse(localStorage.getItem("comments")) || {};

// GET PARAM (for edit)
const params = new URLSearchParams(window.location.search);
const editIndex = params.get("edit");

// TOAST
function showToast(message) {
  const container = document.getElementById("toastContainer");
  if (!container) return;

  const toast = document.createElement("div");
  toast.className = "toast";
  toast.innerText = message;

  container.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 3000);
}

// ================= READ =================
function render() {
  // disini bakal ada
  const list = document.getElementById("list");
  //   kalau misal enggak ada berarti kosong
  if (!list) return;

  list.innerHTML = "";

  //   kalau ada data dia bakal looping 1 1 data nya
  data.forEach((item, index) => {
    list.innerHTML += `
  <div>
    <h3>${item.title}</h3>

    ${item.image ? `<img src="${item.image}" width="200">` : ""}

    <p>${item.content}</p>

    <button onclick="view(${index})">View</button>
    <button onclick="edit(${index})">Edit</button>
    <button onclick="remove(${index})">Delete</button>
  </div>
  <hr>
`;
  });
}

// ================= CREATE + UPDATE by ID =================
function save() {
  const title = document.getElementById("title").value;
  const content = document.getElementById("content").value;
  const fileInput = document.getElementById("imageFile");
  const imageUrl = document.getElementById("imageUrl").value;

  if (!title || !content) {
    alert("Isi semua field!");
    return;
  }

  // FUNCTION to save after image ready
  function saveData(finalImage) {
    if (editIndex !== null) {
      data[editIndex] = { title, content, image: finalImage };
    } else {
      data.push({ title, content, image: finalImage });
    }

    localStorage.setItem("data", JSON.stringify(data));

    // send message before redirect
    localStorage.setItem(
      "toastMessage",
      editIndex !== null
        ? "Article updated successfully!"
        : "Article added successfully!",
    );

    window.location.href = "index.html";
  }

  // ================= PRIORITY =================
  // 1. If file selected → convert to base64
  if (fileInput.files.length > 0) {
    const reader = new FileReader();

    reader.onload = function (e) {
      saveData(e.target.result); // base64 image
    };

    reader.readAsDataURL(fileInput.files[0]);

    // 2. If URL filled → use URL
  } else if (imageUrl) {
    saveData(imageUrl);

    // 3. No image
  } else {
    saveData("");
  }
}

// ================= DELETE BY ID =================
function remove(index) {
  data.splice(index, 1);
  localStorage.setItem("data", JSON.stringify(data));

  showToast("Article deleted successfully!");

  render();
}

// ================= EDIT =================
function edit(index) {
  window.location.href = "form.html?edit=" + index;
}

// ================= LOAD EDIT DATA =================
if (editIndex !== null) {
  const item = data[editIndex];

  if (item) {
    document.getElementById("title").value = item.title;
    document.getElementById("content").value = item.content;
    document.getElementById("imageUrl").value = item.image || "";
    document.getElementById("titleForm").innerText = "Edit Article";
  }
}

// Add Comment
function addComment() {
  const input = document.getElementById("commentInput");
  const text = input.value;

  if (!text) return;

  // RANDOM NAME
  const names = ["Anon", "Guest123", "UserX", "Mystery", "Unknown"];
  const randomName = names[Math.floor(Math.random() * names.length)];

  const newComment = {
    name: randomName,
    text: text,
  };

  if (!comments[postId]) {
    comments[postId] = [];
  }

  comments[postId].push(newComment);

  localStorage.setItem("comments", JSON.stringify(comments));

  input.value = "";
  renderComments();
  showToast("Comment added successfully!");
}

// render comments
function renderComments() {
  const list = document.getElementById("commentList");
  if (!list) return;

  list.innerHTML = "";

  const postComments = comments[postId] || [];

  postComments.forEach((c) => {
    list.innerHTML += `
      <div>
        <strong>${c.name}</strong>
        <p>${c.text}</p>
        <hr>
      </div>
    `;
  });
}

// VIEW FUNCTION
function view(index) {
  window.location.href = "post.html?id=" + index;
}

const postId = new URLSearchParams(window.location.search).get("id");

if (postId !== null) {
  const item = data[postId];

  if (item) {
    document.getElementById("title").innerText = item.title;
    document.getElementById("content").innerText = item.content;

    if (item.image) {
      document.getElementById("image").src = item.image;
    }
  }

  renderComments();
}

// RUN
render();

const toastMessage = localStorage.getItem("toastMessage");

if (toastMessage) {
  showToast(toastMessage);
  localStorage.removeItem("toastMessage");
}
