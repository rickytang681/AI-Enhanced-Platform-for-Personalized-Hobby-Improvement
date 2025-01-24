<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goal Setting Section</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="text-center mb-4">Goal Setting Section</h4>
                    <form>
                        <!-- Goal Title -->
                        <div class="mb-3">
                            <label for="goalTitle" class="form-label">Goal Title:</label>
                            <input type="text" id="goalTitle" class="form-control" placeholder="XXX XXXX XXXXX" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description:</label>
                            <textarea id="description" class="form-control" rows="4" placeholder="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet..." required></textarea>
                        </div>

                        <!-- Target Date -->
                        <div class="mb-3">
                            <label for="targetDate" class="form-label">Target Date:</label>
                            <input type="text" id="targetDate" class="form-control" placeholder="XX/XX/XXXX" required>
                        </div>

                        <!-- Priority Level -->
                        <div class="mb-3">
                            <label for="priorityLevel" class="form-label">Priority Level:</label>
                            <select id="priorityLevel" class="form-select">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>

                        <!-- Add New Goal Button -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-secondary w-100">Add New Goal</button>
                        </div>

                        <!-- Save and Change Button -->
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary w-100">Save and Change</button>
                        </div>
                    </form>

                    <!-- Footer Links -->
                    <div class="text-center mt-3">
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
