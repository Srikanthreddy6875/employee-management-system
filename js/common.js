function validateName(nameId, errorId) {
    let name = document.getElementById(nameId).value.trim();
    let nameErr = "";

    if (name === "") {
        nameErr = "* Name is required";
    } else if (!/^[a-zA-Z\s]+$/.test(name)) {
        nameErr = "* Only letters and white space allowed";
    }

    document.getElementById(errorId).textContent = nameErr;
}

function validateEmail(emailId, errorId) {
    let email = document.getElementById(emailId).value.trim();
    let emailErr = "";

    if (email === "") {
        emailErr = "* Email is required";
    } else if (!/^\S+@\S+\.\S+$/.test(email)) {
        emailErr = "* Invalid email format";
    }

    document.getElementById(errorId).textContent = emailErr;
}

function validateDesignation(desigID, errorId) {
    let designation = document.getElementById(desigID).value.trim();
    let designationErr = "";

    if (designation === "Select Designation") {
        designationErr = "* Please select a Designation";
    }

    document.getElementById(errorId).textContent = designationErr;
}

// Function to validate the main password
function validatePassword(passID, errorID) {
    let pass = document.getElementById(passID).value.trim();
    let passErr = "";

    if (pass === "") {
        passErr = "* Password is required";
    } else if (!/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}/.test(pass)) {
        passErr = "* Password must be at least 8 characters long and include one uppercase letter, one special character, and one number";
    }

    document.getElementById(errorID).textContent = passErr;

    // Also validate confirm password if the main password is valid
    if (passErr === "") {
        validateConfirmPassword('confirmPass', 'confirmPassError');
    }
}

// Function to validate the confirm password
function validateConfirmPassword(confpassID, errorID) {
    let pass = document.getElementById('pass').value.trim();
    let pass_confirm = document.getElementById(confpassID).value.trim();
    let passConfirmErr = "";

    if (pass_confirm === "") {
        passConfirmErr = "* Confirm Password is required";
    } else if (pass !== pass_confirm) {
        passConfirmErr = "* Passwords do not match";
    }

    document.getElementById(errorID).textContent = passConfirmErr;
}

// Event listeners for real-time validation
document.getElementById('pass').addEventListener('input', function() {
    validatePassword('pass', 'passError');
});

document.getElementById('confirmPass').addEventListener('input', function() {
    validateConfirmPassword('confirmPass', 'confirmPassError');
});

function validateSalary(salaryID, errorID) {
    let salary = document.getElementById(salaryID).value.trim();
    let salaryErr = "";

    if (salary === "") {
        salaryErr = "* Salary is required";
    } else if (!/^\d+$/.test(salary)) {
        salaryErr = "* Salary should contain only numbers";
    }

    document.getElementById(errorID).textContent = salaryErr;
}

function validateDOB(dobID, errorID) {
    let dob = document.getElementById(dobID).value;
    let dobErr = "";

    if (dob === "") {
        dobErr = "* Date of Birth is required";
    } else {
        let today = new Date();
        let selectedDate = new Date(dob);
        if (selectedDate >= today) {
            dobErr = "* Date of Birth cannot be today or in the future";
        }
    }

    document.getElementById(errorID).textContent = dobErr;
}

function validateGender(errorID) {
    let gender = document.querySelector('input[name="gender"]:checked');
    let genderErr = "";

    if (!gender) {
        genderErr = "* Gender is required";
    }

    document.getElementById(errorID).textContent = genderErr;
}

function validatestate(stateID, errorID) {
    let district = document.getElementById(stateID).value.trim();
    let districtErr = "";

    if (district === "" || district === "Select District") {
        districtErr = "* Please select a District";
    }

    document.getElementById(errorID).textContent = districtErr;
}
function validateDistrict(districtID, errorID) {
    let district = document.getElementById(districtID).value.trim();
    let districtErr = "";

    if (district === "" || district === "Select District") {
        districtErr = "* Please select a District";
    }

    document.getElementById(errorID).textContent = districtErr;
}

function validateUserType(userID, errorID) {
    let district = document.getElementById(userID).value.trim();
    let districtErr = "";

    if (district === "" || district === "Select District") {
        districtErr = "* Please select a User Type";
    }

    document.getElementById(errorID).textContent = districtErr;
}


function setupValidation(inputId, errorId, errorMessage) {
    const input = document.getElementById(inputId);
    const errorElement = document.getElementById(errorId);

    input.addEventListener('keyup', function () {
        const value = input.value.trim();
        if (value === '') {
            errorElement.textContent = errorMessage;
        } else {
            errorElement.textContent = '';
        }
    });
}

// function validatePhoneNumber(phoneNumberId, phoneNumberErrId) {
//     var phoneNumber = document.getElementById(phoneNumberId).value;
//     var phoneNumberErr = document.getElementById(phoneNumberErrId);
//     var phoneNumberPattern = /^[0-9]{10,13}$/;

//     if (phoneNumber === "") {
//         phoneNumberErr.innerHTML = "* Phone number is required";
//     } else if (!phoneNumberPattern.test(phoneNumber)) {
//         phoneNumberErr.innerHTML = "* Phone number must be 10 digits";
//     } else {
//         phoneNumberErr.innerHTML = "";
//     }
// }

function validatePhoneNumber(inputId, errorId) {
    const inputElement = document.getElementById(inputId);
    const errorElement = document.getElementById(errorId);
    const maxLength = 10;

    let value = inputElement.value;

    value = value.replace(/\D/g, '').slice(0, maxLength);

    inputElement.value = value;

    if (value.length === maxLength) {
        errorElement.textContent = '';
    } else {
        errorElement.textContent = 'Please enter a valid phone number.';
    }
}

function validateForm() {
    validateName('name', 'nameErr');
    validateEmail('email', 'emailErr');
    validateDesignation('designation', 'designationErr');
    validatePassword('pass', 'passErr');
    validateConfirmPassword('pass_confirm', 'passConfirmErr');
    validateSalary('salary', 'salaryErr');
    validateDOB('dob', 'dobErr');
    validateGender('genderErr');
    validatestate('state', 'stateErr');
    validateDistrict('district', 'districtErr');
    validateUserType('user_type','userTypeErr');
    setupValidation('designation_name', 'designation_error', 'Please enter a designation name.');
    // setupValidation('state_name', 'state_error', 'Please enter a state name.');
    validatePhoneNumber('phone_number', 'phoneNumberErr');

    let nameErr = document.getElementById('nameErr').textContent;
    let emailErr = document.getElementById('emailErr').textContent;
    let designationErr = document.getElementById('designationErr').textContent;
    let passErr = document.getElementById('passErr').textContent;
    let passConfirmErr = document.getElementById('passConfirmErr').textContent;
    let salaryErr = document.getElementById('salaryErr').textContent;
    let dobErr = document.getElementById('dobErr').textContent;
    let genderErr = document.getElementById('genderErr').textContent;
    let stateErr = document.getElementById('stateErr').textContent;
    let districtErr= document.getElementById('districtErr').textContent;
    let userTypeErr=document.getElementById('userTypeErr').textContent;
    let designation_error =document.getElementById('designation_name').textContent;
    // let state_error=document.getElementById("state_name").textContent;

    return !(
        nameErr || emailErr || designationErr || passErr || passConfirmErr ||
        salaryErr || dobErr || genderErr || stateErr || districtErr ||userTypeErr ||designation_error
    );
}

// Add event listeners to validate fields on keyup
document.getElementById('name').addEventListener('keyup', function() {
    validateName('name', 'nameErr');
});

document.getElementById('email').addEventListener('keyup', function() {
    validateEmail('email', 'emailErr');
});

document.getElementById('designation').addEventListener('change', function() {
    validateDesignation('designation', 'designationErr');
});

document.getElementById('pass').addEventListener('keyup', function() {
    validatePassword('pass', 'passErr');
});

document.getElementById('pass_confirm').addEventListener('keyup', function() {
    validateConfirmPassword('pass_confirm', 'passConfirmErr');
});

document.getElementById('salary').addEventListener('keyup', function() {
    validateSalary('salary', 'salaryErr');
});

document.getElementById('dob').addEventListener('change', function() {
    validateDOB('dob', 'dobErr');
});



// add steate feild validation 
function setupValidation(inputId, errorId, errorMessage) {
    const input = document.getElementById(inputId);
    const errorElement = document.getElementById(errorId);

    input.addEventListener('input', function () {
        const value = input.value.trim();
        if (value === '') {
            errorElement.textContent = errorMessage;
        } else {
            errorElement.textContent = '';
        }
    });
}

function validateFormstate() {
    let isValid = true;
    
    const stateInput = document.getElementById('state_name');
    const stateError = document.getElementById('state_error');
    
    if (stateInput.value.trim() === '') {
        stateError.textContent = 'Please enter a state name.';
        isValid = false;
    } else {
        stateError.textContent = '';
    }

    return isValid;
}

document.addEventListener('DOMContentLoaded', function() {
    setupValidation('state_name', 'state_error', 'Please enter a state name.');
});

// manage destricts functino
function setupValidation(inputId, errorId, errorMessage) {
    var input = document.getElementById(inputId);
    var errorSpan = document.getElementById(errorId);

    input.addEventListener('input', function() {
        if (input.value.trim() === '') {
            errorSpan.textContent = errorMessage;
        } else {
            errorSpan.textContent = '';
        }
    });
}

function validateState() {
    var stateSelect = document.getElementById('state_id');
    var stateError = document.getElementById('state_error');

    if (stateSelect.value === '') {
        stateError.textContent = 'Please select a state.';
        return false;
    } else {
        stateError.textContent = '';
        return true;
    }
}

function validateForm() {
    var isValid = true;

    var districtInput = document.getElementById('district_name');
    var districtError = document.getElementById('district_error');

    if (districtInput.value.trim() === '') {
        districtError.textContent = 'Please enter a district name.';
        isValid = false;
    } else {
        districtError.textContent = '';
    }

    if (!validateState()) {
        isValid = false;
    }

    return isValid;
}

// Set up validation for district name
setupValidation('district_name', 'district_error', 'Please enter a district name.');

// // password max length function
// $(document).on('keypress', '#pass', function(e) {
//     if ($(e.target).val().length >= 10) {
//         if (e.keyCode != 32) {
//             return false;
//         }
//     }
// });