<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Incentive Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
    }
    .form-card {
      background: rgba(255, 255, 255, 0.8);
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      padding: 40px;
      max-width: 800px;
      margin: auto;
      backdrop-filter: blur(10px);
    }
  .logo {
  width: 140px; /* Increased width */
  display: block;
  margin: 0 auto 20px;
  filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.2)); /* Optional: subtle shadow */
}
    .tagline {
      font-size: 1.1rem;
      text-align: center;
      color: #333;
      margin-bottom: 30px;
    }
    .section-header {
      font-size: 1.3rem;
      font-weight: bold;
      color: #fff;
      background: linear-gradient(to right, #0d6efd, #6f42c1);
      padding: 10px 20px;
      border-radius: 10px;
      margin: 30px 0 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .form-label {
      font-weight: 500;
    }
    .submit-btn {
      background: linear-gradient(to right, #0d6efd, #6f42c1);
      border: none;
      padding: 12px 30px;
      color: white;
      border-radius: 30px;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
    }
    .submit-btn:hover {
      background: linear-gradient(to right, #6f42c1, #0d6efd);
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="form-card">
      <!-- Logo -->
      <img src="{{{asset('images/1920 x 557.png')}}}" alt="Logo" class="logo">

      <!-- Tagline -->
       <h2 class="text-center">Incentive Form</h2>
      <p class="tagline">
        "Please share the incentive amount you expect this month 💰 along with your reasons 📝. Even if your incentive is zero, telling us why helps us understand 🤝 and support you better 💪. Your honesty helps us grow together! 🌱✨"
      </p>

      <form method="POST" action="{{ route('incentive.submit') }}">
        @csrf

        <!-- Team Member Section -->
        <div class="section-header"><i class="bi bi-person-circle"></i> Team Member Section</div>

        <div class="row g-3">
          <div class="col-md-6">
            <label for="employee_select" class="form-label">Select Team Member</label>
            <select class="form-select" id="employee_select" name="employee_id" required>
              <option value="">Select Employee</option>
              @foreach($employees as $employee)
                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                  {{ $employee->name }} (ID: {{ $employee->id }})
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label for="department" class="form-label">Department</label>
            <select class="form-select" id="department" name="department" required>
              <option value="">Select Department</option>
              @foreach($departments as $department)
                <option value="{{ $department->name }}" {{ old('department') == $department->name ? 'selected' : '' }}>
                  {{ $department->name }}
                </option>
              @endforeach
            </select>
          </div>

          <input type="hidden" name="team_member_id" value="{{ old('team_member_id') }}">

          <div class="col-md-6">
            <label for="incentive_month" class="form-label">Month of Claim</label>
            <input type="month" class="form-control" id="incentive_month" name="incentive_month" value="{{ old('incentive_month') }}" required>
          </div>

          <div class="col-md-6">
            <label for="requested_incentive" class="form-label">Incentive Amount Requested (₹)</label>
            <input type="number" class="form-control" id="requested_incentive" name="requested_incentive" value="{{ old('requested_incentive') }}" required>
          </div>

          <div class="col-12">
            <label for="incentive_reason" class="form-label">Reason for the Amount (Achieved / Not Achieved)</label>
            <textarea class="form-control" id="incentive_reason" name="incentive_reason" rows="3" required>{{ old('incentive_reason') }}</textarea>
          </div>

          <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <input type="text" class="form-control" id="status" name="status" value="Pending" readonly style="background-color: #fff3cd; color: #856404; font-weight: 600;">
            <small class="text-muted">Status will be automatically set to Pending when submitted</small>
          </div>
        </div>

        <!-- Approval Section (Optional - For HOD/Senior Only) -->
        <div class="section-header"><i class="bi bi-check-circle"></i> Approval Section (Optional - For HOD/Senior Only)</div>
        <p class="text-muted mb-3">
          <i class="bi bi-info-circle"></i> 
          <strong>Note:</strong> This section is optional. If left empty, the request will be submitted as "Pending" and can be approved/rejected later from the Records page.
        </p>

        <div class="row g-3">
          <div class="col-md-6">
            <label for="approved_incentive" class="form-label">Approved Incentive Amount (₹) <small class="text-muted">(Optional)</small></label>
            <input type="number" class="form-control" id="approved_incentive" name="approved_incentive" value="{{ old('approved_incentive') }}">
            <small class="text-muted">Leave empty to submit as pending</small>
          </div>

          <div class="col-6">
            <label for="approval_reason" class="form-label">Reason for Approval / Rejection <small class="text-muted">(Optional)</small></label>
            <textarea class="form-control" id="approval_reason" name="approval_reason" rows="1">{{ old('approval_reason') }}</textarea>
          </div>

          <div class="col-md-6">
            <label for="reviewed_by" class="form-label">Reviewed By <small class="text-muted">(Optional)</small></label>
              <select class="form-select" id="approved_by" name="approved_by">
            <option selected value="">Select Reviewer (Optional)</option>
            <option value="Srimanta Koley" {{ old('approved_by') == 'Srimanta Koley' ? 'selected' : '' }}>Srimanta Koley</option>
            <option value="Titas Datta" {{ old('approved_by') == 'Titas Datta' ? 'selected' : '' }}>Titas Datta</option>
            <option value="Jishan Ali" {{ old('approved_by') == 'Jishan Ali' ? 'selected' : '' }}>Jishan Ali</option>
            <option value="Nishtha" {{ old('approved_by') == 'Nishtha' ? 'selected' : '' }}>Nishtha</option>
        </select>
          </div>

          <div class="col-md-6">
            <label for="review_date" class="form-label">Date of Review <small class="text-muted">(Optional)</small></label>
            <input type="date" class="form-control" id="review_date" name="review_date" value="{{ old('review_date') }}">
          </div>
        </div>

        <!-- Display validation errors -->
        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Display success message -->
        @if (session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="text-center mt-4">
          <button type="submit" class="submit-btn">Submit</button>
        </div>

        <div class="text-center mt-3">
          <a href="{{ route('salary.incentive-records') }}" class="btn btn-outline-primary me-2">
            <i class="bi bi-list"></i> View Records
          </a>
          <a href="{{ route('salary.board') }}" class="btn btn-outline-success">
            <i class="bi bi-cash-stack"></i> Salary Board
          </a>
        </div>
      </form>
    </div>
  </div>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Department selection is now manual - no auto-populate needed
  </script>
</body>
</html>
