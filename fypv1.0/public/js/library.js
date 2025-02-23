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
}); 