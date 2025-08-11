document.getElementById("login-form").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent default form submission

    let formData = new FormData(this);
    formData.append("ajax", "1"); // ✅ Add AJAX flag

    let loginButton = document.getElementById("login-btn");
    let messageBox = document.getElementById("message-box");

    // Clear previous messages
    messageBox.innerHTML = "";
    messageBox.style.display = "none";

    // Disable button & show loading state
    loginButton.disabled = true;
    loginButton.innerHTML = "Logging in... ⏳";

    fetch("login.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json()) // Convert response to JSON
    .then(data => {
        // Restore button state
        loginButton.disabled = false;
        loginButton.innerHTML = "Login";

        // Display message inside the page
        messageBox.style.display = "block";
        messageBox.textContent = data.message;
        messageBox.style.color = data.status === "success" ? "green" : "red";

        if (data.status === "success") {
            setTimeout(() => {
                window.location.href = data.redirect; // ✅ Redirect to dashboard.php
            }, 1500); // Delay for better UX
        }
    })
    .catch(error => {
        console.error("Error:", error);
        messageBox.style.display = "block";
        messageBox.textContent = "An error occurred. Please try again.";
        messageBox.style.color = "red";
        
        loginButton.disabled = false;
        loginButton.innerHTML = "Login";
    });
});