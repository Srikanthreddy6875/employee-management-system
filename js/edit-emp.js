$(document).ready(function() {
    $('#state').change(function() {
        var stateID = $(this).val();
        if (stateID) {
            $.ajax({
                type: 'POST',
                url: 'get_districts.php',
                data: {
                    state_id: stateID
                },
                success: function(data) {
                    $('#district').html(data);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('#district').html('<option value="">Error loading districts</option>');
                }
            });
        } else {
            $('#district').html('<option value="">Select State first</option>');
        }
    });
});


// image preview
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('profile_preview');
        output.src = reader.result;
        output.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}

document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone_number');
    const designationInput = document.getElementById('designation');
    const stateInput = document.getElementById('state');
    const districtInput = document.getElementById('district');

    nameInput.addEventListener('input', function() {
        if (nameInput.value.trim() === '') {
            document.getElementById('nameErr').textContent = 'Name is required.';
        } else {
            document.getElementById('nameErr').textContent = '';
        }
    });

    emailInput.addEventListener('input', function() {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailInput.value.trim())) {
            document.getElementById('emailErr').textContent = 'Invalid email address.';
        } else {
            document.getElementById('emailErr').textContent = '';
        }
    });

    phoneInput.addEventListener('input', function() {
        const phonePattern = /^[0-9]{9,12}$/;
        if (!phonePattern.test(phoneInput.value.trim())) {
            document.getElementById('phoneErr').textContent = 'Invalid phone number.';
        } else {
            document.getElementById('phoneErr').textContent = '';
        }
    });

    designationInput.addEventListener('change', function() {
        if (designationInput.value === '') {
            document.getElementById('designationErr').textContent = 'Please select a designation.';
        } else {
            document.getElementById('designationErr').textContent = '';
        }
    });

    stateInput.addEventListener('change', function() {
        if (stateInput.value === '') {
            document.getElementById('stateErr').textContent = 'Please select a state.';
        } else {
            document.getElementById('stateErr').textContent = '';
        }
    });

    districtInput.addEventListener('change', function() {
        if (districtInput.value === '') {
            document.getElementById('districtErr').textContent = 'Please select a district.';
        } else {
            document.getElementById('districtErr').textContent = '';
        }
    });
});