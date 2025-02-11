@extends('layouts.logoutHeader')

@section('content')
<head>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h4 class="text-center mb-4">Personalized Recommendations</h4>
                
                <!-- Activity Suggestion -->
                <h5>Activity Suggestion:</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>User's Hobbies</th>
                            <th>Skill Levels</th>
                            <th>Goals</th>
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

                <!-- Resource Suggestion -->
                <h5>Resource Suggestion:</h5>
                <div class="resource-item">
                    <div>• Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
                    <div class="actions">
                        <button class="btn btn-primary btn-sm">Save</button>
                        <button class="btn btn-secondary btn-sm">Reload</button>
                        <button class="btn btn-danger btn-sm">Remove</button>
                        <button class="btn btn-info btn-sm">Copy</button>
                        <i class="thumbs">👍</i>
                        <i class="thumbs">👎</i>
                    </div>
                </div>
                <div class="resource-item">
                    <div>• Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
                    <div class="actions">
                        <button class="btn btn-primary btn-sm">Save</button>
                        <button class="btn btn-secondary btn-sm">Reload</button>
                        <button class="btn btn-danger btn-sm">Remove</button>
                        <button class="btn btn-info btn-sm">Copy</button>
                        <i class="thumbs">👍</i>
                        <i class="thumbs">👎</i>
                    </div>
                </div>
                <div class="resource-item">
                    <div>• Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
                    <div class="actions">
                        <button class="btn btn-primary btn-sm">Save</button>
                        <button class="btn btn-secondary btn-sm">Reload</button>
                        <button class="btn btn-danger btn-sm">Remove</button>
                        <button class="btn btn-info btn-sm">Copy</button>
                        <i class="thumbs">👍</i>
                        <i class="thumbs">👎</i>
                    </div>
                </div>
                <div class="resource-item">
                    <div>• Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
                    <div class="actions">
                        <button class="btn btn-primary btn-sm">Save</button>
                        <button class="btn btn-secondary btn-sm">Reload</button>
                        <button class="btn btn-danger btn-sm">Remove</button>
                        <button class="btn btn-info btn-sm">Copy</button>
                        <i class="thumbs">👍</i>
                        <i class="thumbs">👎</i>
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
@endsection