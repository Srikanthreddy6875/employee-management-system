function validateForm() {
    let isValid = true;

    // Name Validation
    const name = document.getElementById('name').value.trim();
    const nameErr = document.getElementById('nameErr');
    if (!name) {
        nameErr.textContent = "* Name is required";
        isValid = false;
    } else if (!/^[a-zA-Z\s]+$/.test(name)) {
        nameErr.textContent = "* Name should contain only letters";
        isValid = false;
    } else {
        nameErr.textContent = "";
    }

    // Email Validation
    const email = document.getElementById('email').value.trim();
    const emailErr = document.getElementById('emailErr');
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!email) {
        emailErr.textContent = "* Email is required";
        isValid = false;
    } else if (!emailRegex.test(email)) {
        emailErr.textContent = "* Invalid email format";
        isValid = false;
    } else {
        emailErr.textContent = "";
    }

    // Password Validation
    const pass = document.getElementById('pass').value;
    const passErr = document.getElementById('passErr');
    const passRegex = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}/;
    if (!pass) {
        passErr.textContent = "* Password is required";
        isValid = false;
    } else if (!passRegex.test(pass)) {
        passErr.textContent = "* Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character";
        isValid = false;
    } else {
        passErr.textContent = "";
    }

    // Confirm Password Validation
    const passConfirm = document.getElementById('pass_confirm').value;
    const passConfirmErr = document.getElementById('passConfirmErr');
    if (!passConfirm) {
        passConfirmErr.textContent = "* Password confirmation is required";
        isValid = false;
    } else if (passConfirm !== pass) {
        passConfirmErr.textContent = "* Passwords do not match";
        isValid = false;
    } else {
        passConfirmErr.textContent = "";
    }

    // Designation Validation
    const designation = document.getElementById('designation').value;
    const designationErr = document.getElementById('designationErr');
    if (!designation) {
        designationErr.textContent = "* Please select a Designation";
        isValid = false;
    } else {
        designationErr.textContent = "";
    }

    // State Validation
    const state = document.getElementById('state').value;
    const stateErr = document.getElementById('stateErr');
    if (!state) {
        stateErr.textContent = "* Please select a State";
        isValid = false;
    } else {
        stateErr.textContent = "";
    }

    // District Validation
    const district = document.getElementById('district').value;
    const districtErr = document.getElementById('districtErr');
    if (!district) {
        districtErr.textContent = "* Please select a District";
        isValid = false;
    } else {
        districtErr.textContent = "";
    }

    // Phone Number Validation
    const phoneNumber = document.getElementById('phone_number').value.trim();
    const phoneNumberErr = document.getElementById('phoneNumberErr');
    const phoneRegex = /^[0-9]{10,15}$/;
    if (!phoneNumber) {
        phoneNumberErr.textContent = "* Phone number is required";
        isValid = false;
    } else if (!phoneRegex.test(phoneNumber)) {
        phoneNumberErr.textContent = "* Phone number must be between 10 and 15 digits";
        isValid = false;
    } else {
        phoneNumberErr.textContent = "";
    }

    return isValid;
}

function previewImage() {
    const fileInput = document.getElementById('image');
    const preview = document.getElementById('profile_picture_preview');

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(fileInput.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

// Update Districts based on State Selection
document.getElementById('state').addEventListener('change', function() {
    const stateId = this.value;
    const districtSelect = document.getElementById('district');

    // Clear existing options
    districtSelect.innerHTML = "<option value=''>Select District</option>";

    if (stateId) {
        fetch(`get_districts.php?state_id=${stateId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.district_id;
                    option.textContent = district.district_name;
                    districtSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching districts:', error));
    }
});
