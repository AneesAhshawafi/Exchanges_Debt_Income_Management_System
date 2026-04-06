
document.addEventListener('DOMContentLoaded', function () {
    const addClientForm = document.getElementById('add-client-form');
    const clientsListDiv = document.getElementById("clients-list");
    const overlay = document.getElementById('add-client-overlay');

    addClientForm.addEventListener('submit', function (event) {
        // Prevent the default form submission which causes a page reload
        event.preventDefault();

        // Get the form data
        const formData = new FormData(addClientForm);

        // Send the data to your new PHP handler using fetch (AJAX)
        fetch('add_client_handler.php', {
            method: 'POST',
            body: formData
        })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        overlay.classList.add('hidden');
                        addClientForm.reset(); // Clear the form input
                        alert(data.message);
                        location.href = location.pathname + '?reload=' + Date.now();
                      
                    } else {
                        // Handle errors, e.g., show an alert
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Submission failed:', error);
                    alert('An error occurred while adding the client.');
                });
    });
});