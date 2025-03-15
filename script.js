document.addEventListener("DOMContentLoaded", function () {
    // Attach event listeners after DOM has fully loaded
    document.getElementById("sign-up-instead").addEventListener("click", switchToSignUp);
    attachLoginEvent();
});

// Function to attach login form event listener
function attachLoginEvent() {
    let loginForm = document.getElementById("login-form");
    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            event.preventDefault();
            let loadingText = document.querySelector("#loading-text");
            if (loadingText) {
                loadingText.style.display = "block"; // Show Loading...
            }


            event.target.submit(); // Submit the form after 3 seconds

        });
    }
}



// Function to switch to the signup form
function switchToSignUp() {
    document.getElementById("form-div").innerHTML = `
        <form action="./signup-verification/user-signup.php" method="POST" id="sign-up-form">
            <p>Sign Up</p>

            <label>First Name:
                <input type="text" name="first-name" required>
            </label>

            <label>Last Name:
                <input type="text" name="last-name" required>
            </label>

            <label>Username:
                <input type="text" name="username" id="username" required>
            </label>

            <label>Email:
                <input type="email" name="email" required>
            </label>

            <label id="password-label">Password:
                <input type="password" name="password" id="sign-up-password" title="between 8 and 16 characters " required>
                <p id="pass-error"></p>
            </label>

            <input type="submit" value="Sign Up">
            <p id="loading-text" style="display:none;">Loading...</p>
            
            <p>Already have an account? <a href="#" id="login-instead">Login</a></p>
        </form>       
    `;

    // Reattach event listener for switching back to login
    document.getElementById("login-instead").addEventListener("click", switchToLogin);

    document.getElementById("sign-up-form").addEventListener("submit", function (event) {

        let loadingText = document.querySelector("#loading-text"); // Ensure it exists
        if (loadingText) {
            loadingText.style.display = "block"; // Show Loading...
        }


 // Submit the form after 3 seconds

    });
}

// Function to switch back to the login form
function switchToLogin() {
    document.getElementById("form-div").innerHTML = `
            <form action="./signup-verification/user-login.php" method="POST" id="login-form">
                <p>Login</p>

                <label>Username:
                    <input type="text" id="login-uname" name="uname" required>
                    <p id="uname-error" style="display:none;"></p>
                </label>
                
                <label>Password: 
                    <input type="password" id="login-password" name="upassword" required>
                    <p id="pass-error" style="display:none;"></p>
                </label>
                

                <input type="submit" value="Login">
                <p id="loading-text" style="display:none;">Loading...</p>
                
                <p>Don't have an account? <a href="#" id="sign-up-instead">Sign up</a></p>
            </form>
    `;

    // Reattach event listener for switching to signup
    document.getElementById("sign-up-instead").addEventListener("click", switchToSignUp);

    attachLoginEvent();
}








