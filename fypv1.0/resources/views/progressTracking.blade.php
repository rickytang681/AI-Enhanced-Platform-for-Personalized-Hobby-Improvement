@extends('layouts.logoutHeader')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Tracking</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .progress-graph {
            border: 2px solid #ccc;
            background-color: #e9ecef;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card p-4">
                    <h4 class="text-center mb-4">Progress Tracking</h4>
                    
                    <!-- Goals Overview -->
                    <h5>Goals Overview:</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Goal</th>
                                <th>Status</th>
                                <th>Overdue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Goal 1</td>
                                <td>In progress</td>
                                <td>XX/XX/XXXX</td>
                            </tr>
                            <tr>
                                <td>Goal 2</td>
                                <td>Completed</td>
                                <td>XX/XX/XXXX</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Milestones Overview -->
                    <h5>Milestones Overview:</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Milestones</th>
                                <th>Status</th>
                                <th>Overdue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Milestones 1</td>
                                <td>In progress</td>
                                <td>XX/XX/XXXX</td>
                            </tr>
                            <tr>
                                <td>Milestones 2</td>
                                <td>Completed</td>
                                <td>XX/XX/XXXX</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Progress Graph -->
                    <h5>Progress Graph:</h5>
                    <div class="progress-graph">
                        <p class="text-muted">Graph Placeholder</p>
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
@endsection