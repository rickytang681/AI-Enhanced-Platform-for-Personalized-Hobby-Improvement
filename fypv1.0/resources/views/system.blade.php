@extends('layouts.logoutHeader')

@section('content')
<head>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<div class="container">
    <h1>System Administration</h1>

    <!-- User Overview Section -->
    <div class="section">
      <h2>User Overview</h2>
      <div class="card">
        <div class="stat-box">
          <p><strong>Total Users:</strong></p>
          <p>XXXXX</p>
        </div>
        <div class="chart" aria-label="User Growth Chart">
          User Growth Chart
        </div>
      </div>
    </div>

    <!-- User Activity Summary -->
    <div class="section">
      <h2>User Activity Summary</h2>
      <div class="card">
        <table>
          <thead>
            <tr>
              <th>User</th>
              <th>Average Time Spent</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>aaa</td>
              <td>xxx</td>
            </tr>
            <tr>
              <td>bbb</td>
              <td>yyy</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- User Search -->
    <div class="section">
      <h2>User Search</h2>
      <input type="text" placeholder="Search by name or email" aria-label="Search Users" />
    </div>

    <!-- User List -->
    <div class="section">
      <h2>User List</h2>
      <div class="card">
        <table>
          <thead>
            <tr>
              <th>User ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Status</th>
              <th>Last Login</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>John Doe</td>
              <td>john.doe@example.com</td>
              <td>Active</td>
              <td>2024-01-01</td>
              <td>
                <select aria-label="User Action">
                  <option>Activate</option>
                  <option>Deactivate</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Jane Smith</td>
              <td>jane.smith@example.com</td>
              <td>Inactive</td>
              <td>2023-12-15</td>
              <td>
                <select aria-label="User Action">
                  <option>Activate</option>
                  <option>Deactivate</option>
                </select>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Top-Engaged Content -->
    <div class="section">
      <h2>Top-Engaged Content</h2>
      <div class="chart" aria-label="Top-Engaged Content Chart">
        Top-Engaged Content Chart
      </div>
    </div>

    <!-- Content Search and Content List -->
    <div class="section">
      <h2>Content Search</h2>
      <input type="text" placeholder="Search content by title or author" aria-label="Search Content" />
      <h2>Content List</h2>
      <div class="card">
        <table>
          <thead>
            <tr>
              <th>Content ID</th>
              <th>Title</th>
              <th>Description</th>
              <th>Type</th>
              <th>Author</th>
              <th>Category</th>
              <th>Views</th>
              <th>Likes</th>
              <th>Shares</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>101</td>
              <td>Content Title 1</td>
              <td>Brief description...</td>
              <td>Video</td>
              <td>Author 1</td>
              <td>Education</td>
              <td>1000</td>
              <td>500</td>
              <td>200</td>
              <td>
                <select aria-label="Content Action">
                  <option>Approve</option>
                  <option>Reject</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>102</td>
              <td>Content Title 2</td>
              <td>Brief description...</td>
              <td>Article</td>
              <td>Author 2</td>
              <td>Entertainment</td>
              <td>1500</td>
              <td>800</td>
              <td>300</td>
              <td>
                <select aria-label="Content Action">
                  <option>Approve</option>
                  <option>Reject</option>
                </select>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
</div>
@endsection