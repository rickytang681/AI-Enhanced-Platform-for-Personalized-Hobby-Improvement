<button class="save-btn btn {{ $isSaved ? 'btn-primary' : 'btn-outline-primary' }}"
        data-item="{{ $community->id }}"
        title="{{ $isSaved ? 'Remove from saved' : 'Save for later' }}">
    <i class="bi {{ $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
</button>





