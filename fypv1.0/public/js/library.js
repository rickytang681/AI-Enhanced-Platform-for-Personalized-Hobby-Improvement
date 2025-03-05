document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    function updateFilters() {
        let params = new URLSearchParams(new FormData(document.getElementById('search-form')));
        document.querySelectorAll('.category-filter:checked, .subcategory-filter:checked').forEach(cb => {
            params.append(cb.classList.contains('category-filter') ? 'category' : 'subcategory', cb.value);
        });
        params.set('sort', document.getElementById('sort-select').value);
        window.location.search = params.toString();
    }

    document.querySelectorAll('.category-filter, .subcategory-filter').forEach(cb => 
        cb.addEventListener('change', updateFilters));
    document.getElementById('sort-select').addEventListener('change', updateFilters);

    // Content type toggle in upload modal
    const contentType = document.getElementById('content-type');
    const textContent = document.getElementById('text-content');
    const videoContent = document.getElementById('video-content');

    contentType?.addEventListener('change', function() {
        if (this.value === 'text') {
            textContent.style.display = 'block';
            videoContent.style.display = 'none';
        } else {
            textContent.style.display = 'none';
            videoContent.style.display = 'block';
        }
    });

    // Reaction functionality
    document.querySelectorAll('.reaction-btn').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.item;
            const reactionType = this.dataset.type;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch(`/library/${itemId}/react`, {
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

    // Add this to your existing DOMContentLoaded event listener
    const uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            const contentType = document.getElementById('content-type').value;
            const content = document.querySelector('[name="content"]').value;
            const videoUrl = document.querySelector('[name="video_url"]').value;

            if (contentType === 'text' && !content.trim()) {
                e.preventDefault();
                alert('Please enter content for text resources');
                return;
            }

            if (contentType === 'video' && !videoUrl.trim()) {
                e.preventDefault();
                alert('Please enter video URL for video resources');
                return;
            }
        });
    }

    // Add this to your existing DOMContentLoaded event listener
    const categorySelect = document.getElementById('category-select');
    const newCategoryInput = document.getElementById('new-category');

    if (categorySelect && newCategoryInput) {
        categorySelect.addEventListener('change', function() {
            if (this.value === 'new') {
                this.style.display = 'none';
                newCategoryInput.style.display = 'block';
                newCategoryInput.required = true;
                this.required = false;
            }
        });

        // Add a back button or way to return to select
        newCategoryInput.addEventListener('keyup', function(e) {
            if (e.key === 'Escape') {
                this.style.display = 'none';
                categorySelect.style.display = 'block';
                this.required = false;
                categorySelect.required = true;
                categorySelect.value = '';
            }
        });
    }

    // Rating functionality
    document.querySelectorAll('.rating-star').forEach(star => {
        star.addEventListener('mouseover', function() {
            const ratingValue = parseInt(this.dataset.rating);
            const stars = this.parentElement.querySelectorAll('.rating-star');
            
            stars.forEach((s, index) => {
                if (index < ratingValue) {
                    s.classList.remove('bi-star');
                    s.classList.add('bi-star-fill');
                } else {
                    s.classList.remove('bi-star-fill');
                    s.classList.add('bi-star');
                }
            });
        });

        star.addEventListener('mouseleave', function() {
            const container = this.closest('.stars');
            const currentRating = parseInt(container.dataset.userRating) || 0;
            const stars = container.querySelectorAll('.rating-star');
            
            stars.forEach((s, index) => {
                if (index < currentRating) {
                    s.classList.remove('bi-star');
                    s.classList.add('bi-star-fill');
                } else {
                    s.classList.remove('bi-star-fill');
                    s.classList.add('bi-star');
                }
            });
        });

        star.addEventListener('click', function() {
            const rating = this.dataset.rating;
            const itemId = this.dataset.item;
            const container = this.closest('.stars');
            const ratingText = container.querySelector('.text-muted');

            fetch(`/library/${itemId}/rate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ rating: rating })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the stars
                    container.dataset.userRating = rating;
                    const stars = container.querySelectorAll('.rating-star');
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.remove('bi-star');
                            s.classList.add('bi-star-fill');
                        } else {
                            s.classList.remove('bi-star-fill');
                            s.classList.add('bi-star');
                        }
                    });

                    // Update the rating text
                    ratingText.textContent = `(${data.average_rating} / ${data.rating_count} ${data.rating_count === 1 ? 'rating' : 'ratings'})`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update rating. Please try again.');
            });
        });
    });

    // Save functionality
    document.querySelectorAll('.save-btn').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.item;

            fetch(`/library/${itemId}/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
            });
        });
    });

    // Comment functionality
    document.querySelectorAll('.add-comment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const itemId = this.dataset.item;
            const input = this.querySelector('input');
            const content = input.value.trim();

            if (!content) return;

            fetch(`/library/${itemId}/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ content: content })
            })
            .then(response => response.json())
            .then(data => {
                // Add new comment to the list
                const commentsList = this.closest('.comments-section').querySelector('.comments-list');
                const newComment = createCommentElement(data.comment);
                commentsList.insertBefore(newComment, commentsList.firstChild);
                input.value = '';
            });
        });
    });

    // Add this to your existing DOMContentLoaded event listener
    document.querySelectorAll('.view-content').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const modal = document.getElementById('contentModal');
            const title = this.dataset.title;
            const type = this.dataset.type;
            const content = this.dataset.content;
            const videoUrl = this.dataset.video;
            const description = this.dataset.description;

            // Update modal title
            modal.querySelector('.modal-title').textContent = title;
            
            // Update description
            modal.querySelector('.description').textContent = description;

            // Update content based on type
            const contentContainer = modal.querySelector('.content-container');
            if (type === 'video') {
                contentContainer.innerHTML = `
                    <div class="ratio ratio-16x9">
                        <iframe src="${videoUrl}" 
                                allowfullscreen 
                                class="rounded"></iframe>
                    </div>`;
            } else {
                contentContainer.innerHTML = `
                    <div class="content-text p-3 border rounded">
                        ${formatContent(content)}
                    </div>`;
            }
        });
    });

    // Add this to your existing DOMContentLoaded event listener
    document.querySelectorAll('.show-more-comments').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.item;
            const showing = this.dataset.showing;
            const commentsList = this.closest('.comments-section').querySelector('.comments-list');
            
            if (showing === 'less') {
                // Show all comments
                fetch(`/library/${itemId}/comments`)
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
                fetch(`/library/${itemId}/comments`)
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

// Helper function to format content (preserve line breaks and escape HTML)
function formatContent(content) {
    return content
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/\n/g, '<br>');
}

// Add a helper function to format dates
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
