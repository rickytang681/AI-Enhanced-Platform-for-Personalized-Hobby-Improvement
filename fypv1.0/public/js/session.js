// Add this to your JavaScript
function checkSession() {
    fetch('/check-session')
        .then(response => response.json())
        .then(data => {
            if (!data.authenticated) {
                window.location.href = '/login';
            }
        });
}

// Check every 5 minutes
setInterval(checkSession, 300000); 