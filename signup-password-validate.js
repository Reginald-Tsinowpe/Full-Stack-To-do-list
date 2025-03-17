document.addEventListener("submit", function (event) {
    
        
    if (event.target && event.target.id === "sign-up-form") {
        event.preventDefault(); // Prevent actual form submission for validation
        
        

        let password = document.getElementById("sign-up-password");
        let passError = document.getElementById("pass-error");
        let pass_length = password.value.length;

        if (pass_length < 8 || pass_length > 16) {
            passError.textContent = "Your password must be between 8 and 16 characters long";
            passError.style.color = "red"; // Make error visible
            passError.style.display = "block"; // Show error message
        } else {
            passError.style.display = "none"; // Hide error message on successa
            let formData = new FormData(event.target); // Collect form data

            fetch("./signup-verification/user-signup.php", {
                method: "POST",
                body: formData,
            })
            .then(response => response.json()) // Convert response to JSON
            .then(data => {
                if (data.success) {
                    let username = encodeURIComponent(data.username);

                    // Replace form with OTP verification form
                    window.location.href = `./verify-otp.html?username=${username}&vtype=signup`;
                } else {
                    alert(data.error); // Show error message
                }
            })
            .catch(error => console.error("Error:", error));
        }
            }

        
});


