// Shared milestone toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing milestone toggle functionality');
    
    // Handle milestone checkbox clicks
    const milestoneCheckboxes = document.querySelectorAll('.milestone-checkbox');
    console.log('Found ' + milestoneCheckboxes.length + ' milestone checkboxes');
    
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
            console.log('CSRF Token:', csrfToken ? 'Found' : 'Not found');
            
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
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Server responded with status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Server response:', data);
                
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
                    
                    // Update the goal icon if it exists (for dashboard)
                    if (data.progress === 100) {
                        const goalIcon = document.querySelector(`[data-goal-id="${goalId}"] .bi-circle`);
                        if (goalIcon) {
                            goalIcon.classList.remove('bi-circle');
                            goalIcon.classList.add('bi-check-circle-fill', 'text-success');
                        }
                    } else {
                        const goalIcon = document.querySelector(`[data-goal-id="${goalId}"] .bi-check-circle-fill`);
                        if (goalIcon) {
                            goalIcon.classList.remove('bi-check-circle-fill', 'text-success');
                            goalIcon.classList.add('bi-circle');
                        }
                    }
                    
                    // Update progress bar if it exists (for goal and hobby pages)
                    const progressBar = document.querySelector(`[data-goal-id="${goalId}"] .progress-bar`);
                    if (progressBar) {
                        progressBar.style.width = `${data.progress}%`;
                        progressBar.setAttribute('aria-valuenow', data.progress);
                        progressBar.textContent = `${data.progress}%`;
                        
                        if (data.progress === 100) {
                            progressBar.classList.add('bg-success');
                        } else {
                            progressBar.classList.remove('bg-success');
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

