// Add this to your existing JavaScript section
document.addEventListener('DOMContentLoaded', function() {
    // Handle milestone checkbox clicks
    const milestoneCheckboxes = document.querySelectorAll('.milestone-checkbox');
    console.log('Found ' + milestoneCheckboxes.length + ' milestone checkboxes on hobby page');
    
    milestoneCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const milestoneId = this.dataset.milestoneId;
            const goalId = this.dataset.goalId;
            const completed = this.checked;
            
            console.log(`Toggling milestone ${milestoneId} for goal ${goalId} to ${completed ? 'completed' : 'not completed'}`);
            
            // Disable the checkbox during the request
            this.disabled = true;
            
            // Get the CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            
            // Make the AJAX request
            fetch(`/goals/${goalId}/milestones/${milestoneId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ completed })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server responded with status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update the milestone text decoration
                    const milestoneText = this.closest('.milestone-content').querySelector('.milestone-text');
                    if (milestoneText) {
                        if (completed) {
                            milestoneText.classList.add('text-decoration-line-through');
                        } else {
                            milestoneText.classList.remove('text-decoration-line-through');
                        }
                    }
                    
                    // Update the progress bar if it exists
                    const goalCard = document.querySelector(`.goal-card[data-goal-id="${goalId}"]`);
                    if (goalCard) {
                        const progressBar = goalCard.querySelector('.progress-bar');
                        if (progressBar) {
                            progressBar.style.width = `${data.progress}%`;
                            progressBar.setAttribute('aria-valuenow', data.progress);
                            progressBar.textContent = `${data.progress}% Complete`;
                            
                            if (data.progress == 100) {
                                progressBar.classList.add('bg-success');
                            } else {
                                progressBar.classList.remove('bg-success');
                            }
                        }
                        
                        const statusBadge = goalCard.querySelector('.badge');
                        if (statusBadge) {
                            statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                            statusBadge.className = `badge ${data.status === 'completed' ? 'bg-success' : 'bg-primary'} me-2`;
                        }
                    }
                } else {
                    // If there was an error, revert the checkbox state
                    this.checked = !completed;
                    console.error('Error from server:', data.message);
                    alert('Error updating milestone: ' + data.message);
                }
            })
            .catch(error => {
                // If there was an error, revert the checkbox state
                this.checked = !completed;
                console.error('Error toggling milestone:', error);
                alert('Error updating milestone: ' + error.message);
            })
            .finally(() => {
                // Re-enable the checkbox
                this.disabled = false;
            });
        });
    });
});