// load sidebar component
fetch("/components/admin-sidebar.html")
  .then((res) => res.text())
  .then((data) => {
    document.getElementById("adminSidebar").innerHTML = data;
  });
