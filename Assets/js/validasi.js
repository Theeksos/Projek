document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formLogin");
    const username = document.getElementById("username");
    const password = document.getElementById("password");

    form.addEventListener("submit", function (e) {
        username.value = username.value.trim();
        password.value = password.value.trim();

        if (username.value === "" || password.value === "") {
            e.preventDefault(); // batalkan submit ke server
            alert("Username dan Password wajib diisi!");
            return;
        }

        if (password.value.length < 6) {
            e.preventDefault();
            alert("Password minimal 6 karakter.");
            return;
        }
    });
});
