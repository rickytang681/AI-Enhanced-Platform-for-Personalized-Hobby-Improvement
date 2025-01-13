<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Administration</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #f4f4f9;
    }
    .container {
      max-width: 1200px;
      margin: auto;
      background: #fff;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    h1, h2, h3 {
      text-align: center;
      margin-bottom: 20px;
    }
    .section {
      margin-bottom: 30px;
    }
    .section h2 {
      border-bottom: 2px solid #ddd;
      padding-bottom: 5px;
      margin-bottom: 15px;
    }
    .card {
      background: #f9f9f9;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    .stat-box {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    .chart {
      border: 1px solid #ddd;
      height: 200px;
      background: #e0e0e0;
      text-align: center;
      line-height: 200px;
      color: #777;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    table th, table td {
      text-align: left;
      border: 1px solid #ddd;
      padding: 10px;
    }
    table th {
      background-color: #f4f4f9;
    }
    input[type="text"] {
      padding: 8px;
      width: calc(100% - 20px);
      margin-bottom: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    select {
      padding: 5px;
    }
    .btn {
      padding: 10px 15px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .btn:hover {
      background: #0056b3;
    }
    footer {
      text-align: center;
      margin-top: 30px;
      font-size: 0.9rem;
    }
    footer a {
      color: #007bff;
      text-decoration: none;
      margin: 0 10px;
    }
    footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
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

    <!-- Footer -->
    <footer>
      <p>
        <a href="#">Terms of Service</a> |
        <a href="#">Privacy Policy</a> |
        <a href="#">Help</a> |
        <a href="#">Contact Us</a> |
        <a href="#">About Us</a> |
        <a href="#">Logout</a>
      </p>
    </footer>
  </div>
</body>
</html>
