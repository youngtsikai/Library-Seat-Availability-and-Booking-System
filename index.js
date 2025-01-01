function gotoStaffLogin() {
        window.location.href="stafflogin.html"
    }
    
    function gotoStudentLogin() {
        window.location.href="studentlogin.html"
    }

    function validateStudentLogIn() {
        const regnumberInput = document.getElementById("regnumber");
        const regnumber = regnumberInput.value;
        const passwordInput = document.getElementById("password")
        const password = passwordInput.value;

        if (regnumber === '' || password === '') {
          alert("Fill in Empty Spaces");
          return false;
      }

        if (regnumber.length < 8 || regnumber.length > 9) {
          regnumberInput.classList.add("invalid");
          alert("Invalid Credentials!!!");
          return false;
        } else {
          regnumberInput.classList.remove("Invalid Registration Number!");
          return true;
        }

    }

    function validateReset() {
      const passwordInput = document.getElementById("password");
      const confirmPasswordInput = document.getElementById("confirmPassword");
      const errorMessage = document.getElementById("errorMessage");
      
      // Check if passwords are empty
      if (!passwordInput.value || !confirmPasswordInput.value) {
          errorMessage.textContent = "Please enter both passwords.";
          return false;
      }
      
      // Check if passwords match
      if (passwordInput.value !== confirmPasswordInput.value) {
          errorMessage.textContent = "Passwords do not match.";
          return false;
      } else {
          errorMessage.textContent = ""; // Clear error message if valid
          return true;
      }

    }

      function validateStaffReset() {
        const passwordInput = document.getElementById("password");
        const confirmPasswordInput = document.getElementById("confirmPassword");
        const errorMessage = document.getElementById("errorMessage");
        const usernameInput = document.getElementById("username");
        const username = usernameInput.value;
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        // Check if passwords are empty
        if (!passwordInput.value || !confirmPasswordInput.value) {
            errorMessage.textContent = "Please enter both passwords.";
            return false;
        }
        
        // Check if passwords match
        if (passwordInput.value !== confirmPasswordInput.value) {
            errorMessage.textContent = "Passwords do not match.";
            return false;
        } else {
            errorMessage.textContent = ""; // Clear error message if valid
            return true;
        }
  }



  if (username === '' || password === '' || confirmPassword === '') {
    alert("Fill in Empty Spaces");
    return false;
  }