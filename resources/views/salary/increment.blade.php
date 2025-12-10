<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Salary Increment Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tabler-icons@latest/tabler-sprite.svg">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .card { border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.1); padding: 40px; max-width: 800px; margin: auto; background: linear-gradient(to right, #6f42c1 0px, #007bff 500px); }
        .logo { width: 140px; }
        form-card { background: rgba(255, 255, 255, 0.8); border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.2); }
        .tagline { font-size: 1.1rem; text-align: center; color: #333; margin-bottom: 30px; }
        .section-header { font-size: 1.3rem; font-weight: bold; color: #fff; background: linear-gradient(to right, #6f42c1, #007bff); border-radius: 10px; margin: 30px 0 20px; display: flex; align-items: center; gap: 10px; }
        .form-label { font-weight: 500; }
        .submit-hover { background: linear-gradient(to right, #6f42c1, #007bff); border: none; padding: 12px 30px; color: white; border-radius: 5px; font-weight: 600; font-size: 1.1rem; transition: 0.3s ease; }
        .submit-hover:hover { background: linear-gradient(to right, #5a36a6, #0056b3); color: white; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="text-center mb-4">
                <h2>🏢 5 CORE TASK MANAGER</h2>
                <h3>Salary Increment Form</h3>
                <div class="tagline">
                    "Please share the increment amount you expect this month 😊 along with your reasons 📝. Even if your increment is zero, telling us why helps us understand 😔 and support you better 💪. Your honesty helps us grow together ✨🤝"
                </div>
            </div>

            <form method="POST" action="{{ route('salary-proposal.store') }}">
                @csrf

                <!-- Employee Information -->
                <div class="section-header">👥 Team Member Section</div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="employee_select" class="form-label">Select Team Member</label>
                        <select class="form-control" id="employee_select" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @if(isset($employees) && count($employees) > 0)
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            @else
                                <option value="">No employees found</option>
                            @endif
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
                </div>

                <!-- Proposal Details -->
                <div class="section-header">📈 Proposal Details</div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="review_month" class="form-label">Review Month</label>
                        <input type="month" class="form-control" id="review_month" name="review_month">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Proposal Type</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="proposal_type" id="increase" value="increase">
                                <label class="form-check-label" for="increase">Increase</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="proposal_type" id="no_increase" value="no_increase">
                                <label class="form-check-label" for="no_increase">No Increase</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="proposed_amount" class="form-label">Proposed Increase Amount (₹)</label>
                        <input type="number" class="form-control" id="proposed_amount" name="proposed_amount" min="0">
                    </div>
                    <div class="col-12">
                        <label for="comments" class="form-label">Justification / Comments</label>
                        <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Provide detailed reasons for your proposal..."></textarea>
                    </div>
                </div>

                <!-- Approval Section -->
                <div class="section-header">✅ Approval Section</div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="approved_by" class="form-label">Approved By</label>
                        <select class="form-control" id="approved_by" name="approved_by">
                            <option value="">Select Approver</option>
                            <option value="Srimanta Koley">Srimanta Koley</option>
                            <option value="Titas Datta">Titas Datta</option>
                            <option value="Jishan Ali">Jishan Ali</option>
                            <option value="Nishtha">Nishtha</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="approval_status" class="form-label">Approval Status</label>
                        <select class="form-control" id="approval_status" name="approval_status">
                            <option value="">Choose Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="submit-hover">
                        ✅ Submit Proposal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Department selection is now manual - no auto-populate needed
    </script>
</body>
</html>