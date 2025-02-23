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

            fetch(`/library/${itemId}/react`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ reaction_type: reactionType })
            })
            .then(response => response.json())
            .then(data => {
                const card = this.closest('.card');
                card.querySelector('.likes-count').textContent = data.likes;
                card.querySelector('.dislikes-count').textContent = data.dislikes;
            })
            .catch(error => console.error('Error:', error));
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
        star.addEventListener('click', function() {
            const rating = this.dataset.rating;
            const itemId = this.dataset.item;

            fetch(`/library/${itemId}/rate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ rating: rating })
            })
            .then(response => response.json())
            .then(data => {
                // Update rating display
                const ratingStars = this.closest('.rating').querySelectorAll('.bi-star, .bi-star-fill');
                ratingStars.forEach((star, index) => {
                    star.classList.remove('bi-star', 'bi-star-fill');
                    star.classList.add(index < data.rating ? 'bi-star-fill' : 'bi-star');
                });
            });
        });
    });

    // Favorite functionality
    document.querySelectorAll('.favorite-btn').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.item;

            fetch(`/library/${itemId}/favorite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                const icon = this.querySelector('i');
                if (data.favorited) {
                    this.classList.replace('btn-outline-danger', 'btn-danger');
                    icon.classList.replace('bi-heart', 'bi-heart-fill');
                } else {
                    this.classList.replace('btn-danger', 'btn-outline-danger');
                    icon.classList.replace('bi-heart-fill', 'bi-heart');
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
                    <div class="content-text">
                        ${formatContent(content)}
                    </div>`;
            }
        });
    });
});

function createCommentElement(comment) {
    // Create and return comment HTML element
    const div = document.createElement('div');
    div.className = 'comment mb-2';
    // Add comment content...
    return div;
}

// Helper function to format text content
function formatContent(content) {
    if (!content) return '';
    
    // Convert line breaks to HTML
    return content.split('\n').map(line => {
        line = line.trim();
        if (line.match(/^\d+\./)) {
            // Number lists
            return `<p class="mb-2"><strong>${line.split('.')[0]}.</strong>${line.substring(line.indexOf('.') + 1)}</p>`;
        } else if (line.length > 0) {
            // Regular paragraphs
            return `<p class="mb-2">${line}</p>`;
        }
        return '';
    }).join('');
} 