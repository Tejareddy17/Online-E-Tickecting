document.getElementById("forgot-password-form").addEventListener("submit", async (e) => {
    e.preventDefault();
    
    const formData = new FormData(document.getElementById("forgot-password-form"));

    const res = await fetch("forgot_password.php", {
        method: "POST",
        body: formData
    });

    const data = await res.json();
    alert(data.message);
});
