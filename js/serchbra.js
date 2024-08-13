function filterStates() {  
    const input = document.getElementById('state-search');  
    const filter = input.value.toLowerCase();  
    const stateList = document.getElementById('stateList');  
    const options = stateList.getElementsByClassName('state-option');  

    let hasMatch = false;  
    
    for (let i = 0; i < options.length; i++) {  
        const txtValue = options[i].innerText.toLowerCase();  
        if (txtValue.includes(filter)) {  
            options[i].style.display = "";  
            hasMatch = true;  
        } else {  
            options[i].style.display = "none";  
        }  
    }  

    stateList.style.display = hasMatch ? "block" : "none";  
}  

function selectState(element) {  
    const stateInput = document.getElementById('state-search');  
    const stateId = element.getAttribute('data-id');  

    stateInput.value = element.innerText;  
    document.getElementById('state').value = stateId; // Set the hidden select value  
    document.getElementById('stateList').style.display = 'none'; // Hide the dropdown  
    validatestate('state', 'stateErr'); // Call validation  
}  

// Close the dropdown if clicked outside  
window.onclick = function(event) {  
    if (!event.target.matches('#state-search')) {  
        document.getElementById('stateList').style.display = 'none';  
    }  
};  