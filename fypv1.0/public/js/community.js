document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Reaction functionality
    document.querySelectorAll('.reaction-btn').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.item;
            const reactionType = this.dataset.type;

            fetch(`/community/${itemId}/react`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reaction_type: reactionType })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Update the counts
                const card = this.closest('.card');
                card.querySelector('.likes-count').textContent = data.likes;
                card.querySelector('.dislikes-count').textContent = data.dislikes;

                // Toggle active state
                const isLike = reactionType === 'like';
                const otherButton = card.querySelector(`.reaction-btn[data-type="${isLike ? 'dislike' : 'like'}"]`);
                
                // Toggle current button
                this.classList.toggle('active');
                if (isLike) {
                    this.classList.toggle('btn-success');
                    this.classList.toggle('btn-outline-success');
                } else {
                    this.classList.toggle('btn-danger');
                    this.classList.toggle('btn-outline-danger');
                }

                // Remove active state from other button
                otherButton.classList.remove('active');
                if (!isLike) {
                    otherButton.classList.remove('btn-success');
                    otherButton.classList.add('btn-outline-success');
                } else {
                    otherButton.classList.remove('btn-danger');
                    otherButton.classList.add('btn-outline-danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update reaction. Please try again.');
            });
        });
    });

    // Save functionality
    document.querySelectorAll('.save-btn').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.item;

            fetch(`/community/${itemId}/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const icon = this.querySelector('i');
                if (data.saved) {
                    this.classList.replace('btn-outline-primary', 'btn-primary');
                    icon.classList.replace('bi-bookmark', 'bi-bookmark-fill');
                    this.title = 'Remove from saved';
                } else {
                    this.classList.replace('btn-primary', 'btn-outline-primary');
                    icon.classList.replace('bi-bookmark-fill', 'bi-bookmark');
                    this.title = 'Save for later';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update save status. Please try again.');
            });
        });
    });
});

// Helper function to create comment element
function createCommentElement(comment) {
    const div = document.createElement('div');
    div.className = 'comment mb-2';
    
    const profilePicture = comment.user.profile_picture 
        ? `/storage/${comment.user.profile_picture}`
        : '/images/default-profile.png';
    
    div.innerHTML = `
        <div class="d-flex align-items-center">
            <img src="${profilePicture}" 
                 class="profile-image-small me-2" 
                 alt="Profile">
            <strong>${comment.user.name}</strong>
            <small class="text-muted ms-2">${formatDate(comment.created_at)}</small>
        </div>
        <p class="mb-1 ms-4">${comment.content}</p>
    `;
    
    return div;
}

// Helper function to format content
function formatContent(content) {
    if (!content) return '';
    
    return content.split('\n').map(line => {
        line = line.trim();
        return line.length > 0 ? `<p class="mb-2">${line}</p>` : '';
    }).join('');
}

// Helper function to format dates
function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // difference in seconds

    if (diff < 60) return 'just now';
    if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
    if (diff < 604800) return `${Math.floor(diff / 86400)} days ago`;
    
    return date.toLocaleDateString();
}

// Make tags clickable
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('badge') && e.target.classList.contains('bg-secondary')) {
        const tag = e.target.textContent.trim();
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('tag', tag);
        window.location.href = currentUrl.toString();
    }
});
