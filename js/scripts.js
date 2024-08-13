/*!
    * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2023 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
    // 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

});


// document.addEventListener('DOMContentLoaded', function() {
//     const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {
//         searchable: true,
//         fixedHeight: true,
//         perPage: 5
//     });

//     document.querySelectorAll('.delete-button').forEach(function(button) {
//         button.addEventListener('click', function(event) {
//             if (!confirm('Are you sure you want to delete this admin?')) {
//                 event.preventDefault();
//             }
//         });
//     });
// });

// function confirmDelete(districtId) {
//     if (confirm("Are you sure you want to delete this district?")) {
//         window.location.href = `./delete-destricts.php?id=${districtId}`;
//     }
// }