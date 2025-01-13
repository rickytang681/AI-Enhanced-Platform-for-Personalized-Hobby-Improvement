<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milestone Creation</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="text-center mb-4">Milestone Creation</h4>
                    <form>
                        <!-- Milestone Title -->
                        <div class="mb-3">
                            <label for="milestoneTitle" class="form-label">Milestone Title:</label>
                            <input type="text" id="milestoneTitle" class="form-control" placeholder="XXX XXXX XXXXX" required>
                        </div>

                        <!-- Milestone Deadline -->
                        <div class="mb-3">
                            <label for="milestoneDeadline" class="form-label">Milestone Deadline:</label>
                            <input type="text" id="milestoneDeadline" class="form-control" placeholder="XX/XX/XXXX" required>
                        </div>

                        <!-- Task List -->
                        <div class="mb-3">
                            <label for="taskList" class="form-label">Task List:</label>
                            <select id="taskList" class="form-select">
                                <option value="Task 1">Task 1</option>
                                <option value="Task 2">Task 2</option>
                                <option value="Task 3">Task 3</option>
                                <option value="Task 4">Task 4</option>
                            </select>
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
