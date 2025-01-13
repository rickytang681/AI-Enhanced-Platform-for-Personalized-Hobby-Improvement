<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resource Library</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .filter-section, .resource-item {
            margin-bottom: 20px;
        }
        .category-list, .subcategories {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .category-list label, .subcategories label {
            margin-right: 10px;
        }
        .thumbs {
            cursor: pointer;
            font-size: 1.2rem;
            margin-right: 10px;
        }
        .resource-image {
            width: 100%;
            height: 150px;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #aaa;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card p-4">
                    <h4 class="text-center mb-4">Resource Library</h4>

                    <!-- Search Bar -->
                    <div class="filter-section">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Advanced Filters</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Filter 1</a></li>
                                <li><a class="dropdown-item" href="#">Filter 2</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Category List -->
                    <div class="filter-section">
                        <h5>Category List:</h5>
                        <div class="category-list">
                            <label><input type="checkbox"> Photography</label>
                            <label><input type="checkbox"> Coding</label>
                            <label><input type="checkbox"> Reading</label>
                            <label><input type="checkbox"> Video games</label>
                            <label><input type="checkbox"> Writing</label>
                            <label><input type="checkbox"> Music</label>
                            <label><input type="checkbox"> Sports</label>
                            <label><input type="checkbox"> Cooking</label>
                            <label><input type="checkbox"> Gardening</label>
                            <label><input type="checkbox"> Arts</label>
                            <label><input type="checkbox"> Crafts</label>
                            <label><input type="checkbox"> Running</label>
                        </div>
                    </div>

                    <!-- Subcategories -->
                    <div class="filter-section">
                        <h5>Subcategories:</h5>
                        <div class="subcategories">
                            <label><input type="checkbox"> Beginner</label>
                            <label><input type="checkbox"> Intermediate</label>
                            <label><input type="checkbox"> Advanced</label>
                        </div>
                        <select class="form-select mt-2" style="max-width: 200px;">
                            <option selected>Popular</option>
                            <option>Newest</option>
                            <option>Highly Rated</option>
                        </select>
                    </div>

                    <!-- Resource Item -->
                    <div class="resource-item">
                        <div class="resource-image">
                            <span>Image Placeholder</span>
                        </div>
                        <form class="mt-3">
                            <div class="mb-2">
                                <label>Author:</label>
                                <input type="text" class="form-control" readonly>
                            </div>
                            <div class="mb-2">
                                <label>Title:</label>
                                <input type="text" class="form-control" readonly>
                            </div>
                            <div class="mb-2">
                                <label>Description:</label>
                                <textarea class="form-control" rows="3" readonly></textarea>
                            </div>
                        </form>
                        <div class="actions mt-2">
                            <a href="#" class="btn btn-link">Share</a>
                            <i class="thumbs">👍</i>
                            <i class="thumbs">👎</i>
                            <a href="#" class="btn btn-link">Bookmark</a>
                        </div>
                        <div class="mt-3">
                            <label>Comment:</label>
                            <textarea class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Footer Links -->
                    <div class="text-center mt-4">
                        <a href="#">Terms of Service</a> |
                        <a href="#">Privacy Policy</a> |
                        <a href="#">Help</a> |
                        <a href="#">Contact Us</a> |
                        <a href="#">About Us</a> |
                        <a href="#">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
