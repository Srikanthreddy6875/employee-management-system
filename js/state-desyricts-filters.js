document.addEventListener('DOMContentLoaded', function() {
    const selectedState = document.getElementById('selectedState');
    const stateDropdownContent = document.getElementById('stateDropdownContent');
    const stateList = document.getElementById('stateList');
    const stateSearch = document.getElementById('stateSearch');

    const selectedDistrict = document.getElementById('selectedDistrict');
    const districtDropdownContent = document.getElementById('districtDropdownContent');
    const districtList = document.getElementById('districtList');
    const districtSearch = document.getElementById('districtSearch');

    selectedState.addEventListener('click', function() {
        stateDropdownContent.style.display = stateDropdownContent.style.display === 'block' ? 'none' : 'block';
    });

    stateList.addEventListener('click', function(event) {
        if (event.target.tagName === 'LI') {
            selectedState.textContent = event.target.textContent;
            stateSearch.value = ''; // Clear search input
            stateDropdownContent.style.display = 'none';
            document.getElementById('state').value = event.target.getAttribute('data-value');

            // Fetch and update districts
            fetchDistricts(event.target.getAttribute('data-value'));
        }
    });

    selectedDistrict.addEventListener('click', function() {
        districtDropdownContent.style.display = districtDropdownContent.style.display === 'block' ? 'none' : 'block';
    });

    districtList.addEventListener('click', function(event) {
        if (event.target.tagName === 'LI') {
            selectedDistrict.textContent = event.target.textContent;
            districtSearch.value = ''; // Clear search input
            districtDropdownContent.style.display = 'none';
            document.getElementById('district').value = event.target.getAttribute('data-value');
        }
    });
});

function filterStates() {
    const filter = document.getElementById('stateSearch').value.toLowerCase();
    const listItems = document.querySelectorAll('#stateList li');

    listItems.forEach(function(item) {
        const text = item.textContent.toLowerCase();
        item.style.display = text.indexOf(filter) > -1 ? 'block' : 'none';
    });
}

function filterDistricts() {
    const filter = document.getElementById('districtSearch').value.toLowerCase();
    const listItems = document.querySelectorAll('#districtList li');

    listItems.forEach(function(item) {
        const text = item.textContent.toLowerCase();
        item.style.display = text.indexOf(filter) > -1 ? 'block' : 'none';
    });
}

function fetchDistricts(stateID) {
    if (stateID) {
        $.ajax({
            type: 'POST',
            url: 'get_districts.php',
            data: { state_id: stateID },
            success: function(data) {
                $('#districtList').html(data);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $('#districtList').html('<li data-value="">Error loading districts</li>');
            }
        });
    } else {
        $('#districtList').html('<li data-value="">Select State first</li>');
    }
}
