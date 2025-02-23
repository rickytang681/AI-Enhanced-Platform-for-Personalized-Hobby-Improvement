@extends('layouts.logoutHeader')

@section('content')
<div class="container mt-4">
    <!-- Dashboard Sections -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">Recent Activities</h5>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">Progress Snapshot</h5>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">Recommendations</h5>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">Community Highlights</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection