// Fetch user details when the page loads
document.addEventListener("DOMContentLoaded", async () => {
    const res = await fetch("profile.php");
    const data = await res.json();

    if (data.status === "success") {
        document.getElementById("full_name").value = data.user.full_name;
        document.getElementById("email").value = data.user.email;
        document.getElementById("address").value = data.user.address;
        document.getElementById("phone").value = data.user.phone;
    } else {
        alert("Error fetching profile data.");
    }
});

// Handle profile update
document.getElementById("profile-form").addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(document.getElementById("profile-form"));

    const res = await fetch("profile.php", {
        method: "POST",
        body: formData
    });

    const data = await res.json();
    alert(data.message);
});
