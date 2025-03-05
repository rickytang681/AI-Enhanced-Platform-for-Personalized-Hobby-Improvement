document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Sort functionality
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const currentUrl = new URL(window.location.href);
            const params = new URLSearchParams(currentUrl.search);
            params.set('sort', this.value);
            window.location.search = params.toString();
        });
    }

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

    // Comment functionality
    document.querySelectorAll('.add-comment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const itemId = this.dataset.item;
            const input = this.querySelector('.comment-input'); // Change to select by class
            const content = input.value.trim();

            if (!content) return;

            // For debugging
            console.log('Attempting to send comment:', content);

            fetch(`/community/${itemId}/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    content: content
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error('Network response was not ok: ' + text);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Server response:', data); // Debug log

                if (data.success) {
                    const commentsList = this.closest('.comments-section').querySelector('.comments-list');
                    const newComment = createCommentElement(data.comment);
                    commentsList.insertBefore(newComment, commentsList.firstChild);
                    input.value = '';
                } else {
                    throw new Error('Failed to add comment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add comment. Please try again.');
            });
        });
    });

    // Show more/less comments functionality
    document.querySelectorAll('.show-more-comments').forEach(button => {
        button.addEventListener('click', function() {
            const communityId = this.dataset.community;
            const showing = this.dataset.showing;
            const commentsList = this.closest('.comments-section').querySelector('.comments-list');
            
            if (showing === 'less') {
                // Show all comments
                fetch(`/community/${communityId}/comments`)
                    .then(response => response.json())
                    .then(data => {
                        commentsList.innerHTML = ''; // Clear existing comments
                        data.comments.forEach(comment => {
                            commentsList.appendChild(createCommentElement(comment));
                        });
                        this.textContent = 'Show less comments';
                        this.dataset.showing = 'more';
                    });
            } else {
                // Show only first 3 comments
                fetch(`/community/${communityId}/comments`)
                    .then(response => response.json())
                    .then(data => {
                        commentsList.innerHTML = ''; // Clear existing comments
                        data.comments.slice(0, 3).forEach(comment => {
                            commentsList.appendChild(createCommentElement(comment));
                        });
                        this.textContent = 'Show more comments';
                        this.dataset.showing = 'less';
                    });
            }
        });
    });
});

// Helper function to create comment element
function createCommentElement(comment) {
    const div = document.createElement('div');
    div.className = 'comment border-bottom pb-2 mb-2';
    
    const profilePicture = comment.user.profile_picture 
        ? `/storage/${comment.user.profile_picture}`
        : '/images/default-profile.png';
    
    div.innerHTML = `
        <div class="d-flex align-items-center">
            <img src="${profilePicture}" 
                 class="rounded-circle" 
                 width="30" 
                 height="30" 
                 alt="Profile">
            <div class="ms-2">
                <strong>${comment.user.name}</strong>
                <small class="text-muted ms-2">${formatDate(comment.created_at)}</small>
            </div>
        </div>
        <p class="mb-0 mt-2">${comment.content}</p>
    `;
    
    return div;
}

// Helper function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
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
