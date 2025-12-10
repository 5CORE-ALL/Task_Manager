<!-- DO's Modal (Beautiful Design) -->
<div class="modal fade" id="dosModal" tabindex="-1" aria-labelledby="dosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-top-left-radius:16px; border-top-right-radius:16px; padding: 25px;">
                <div style="display:flex; align-items:center; gap:15px;">
                    <span style="font-size:2.5rem;">✅</span>
                    <div>
                        <h4 class="modal-title" id="dosModalLabel" style="margin-bottom:5px; font-weight:600;">Add New DO</h4>
                        <div style="font-size:1rem; color:#e8eaf6; opacity:0.9;">Capture actions that boost your productivity</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1); font-size:1.2rem;"></button>
            </div>
            <div class="modal-body" style="padding:30px; background:#f8f9ff;">
                <form id="dosForm">
                    @csrf
                    <!-- What Field -->
                    <div class="mb-4">
                        <label for="dosWhat" class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:8px;">
                            <i class="ti ti-target" style="color:#28a745; margin-right:8px;"></i>What DO you want to track? 
                            <span style="color:#7f8c8d;font-size:0.9rem;">(Action to perform)</span>
                        </label>
                        <input type="text" class="form-control" id="dosWhat" name="dosWhat" 
                               placeholder="e.g., Review daily goals every morning" 
                               maxlength="100" required
                               style="border:2px solid #e1e8ed; border-radius:10px; padding:15px; font-size:1rem; transition:all 0.3s;">
                        <small class="text-muted" style="font-size:0.85rem;">
                            <i class="ti ti-info-circle"></i> Describe the productive action you want to track (max 100 chars)
                        </small>
                    </div>

                    <!-- Why and How Fields Side by Side -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="dosWhy" class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:8px;">
                                <i class="ti ti-lightbulb" style="color:#ffc107; margin-right:8px;"></i>Why is this important?
                            </label>
                            <textarea class="form-control" id="dosWhy" name="dosWhy" rows="4" 
                                      placeholder="e.g., Helps me stay focused, prioritize tasks, and achieve daily objectives efficiently" 
                                      required
                                      style="border:2px solid #e1e8ed; border-radius:10px; padding:15px; font-size:0.95rem; resize:vertical; transition:all 0.3s;"></textarea>
                            <small class="text-muted" style="font-size:0.85rem;">
                                <i class="ti ti-arrow-right"></i> Explain the motivation behind this action
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label for="dosImpact" class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:8px;">
                                <i class="ti ti-trending-up" style="color:#28a745; margin-right:8px;"></i>Expected Impact
                            </label>
                            <textarea class="form-control" id="dosImpact" name="dosImpact" rows="4" 
                                      placeholder="e.g., Increases productivity by 30%, saves 1 hour daily, improves work quality" 
                                      required
                                      style="border:2px solid #e1e8ed; border-radius:10px; padding:15px; font-size:0.95rem; resize:vertical; transition:all 0.3s;"></textarea>
                            <small class="text-muted" style="font-size:0.85rem;">
                                <i class="ti ti-chart-line"></i> How will this improve your productivity?
                            </small>
                        </div>
                    </div>

                    <!-- Priority Level -->
                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:15px;">
                            <i class="ti ti-star" style="color:#ffc107; margin-right:8px;"></i>Priority Level
                        </label>
                        <div class="d-flex gap-4 flex-wrap">
                            <div class="form-check" style="padding:15px; background:#fff; border:2px solid #e1e8ed; border-radius:10px; min-width:120px; text-align:center; transition:all 0.3s;">
                                <input class="form-check-input" type="radio" name="dosPriority" id="dosHigh" value="High" required style="transform:scale(1.3);">
                                <label class="form-check-label fw-bold" for="dosHigh" style="color:#dc2626; font-size:1rem; margin-left:8px;">🔥 High</label>
                            </div>
                            <div class="form-check" style="padding:15px; background:#fff; border:2px solid #e1e8ed; border-radius:10px; min-width:120px; text-align:center; transition:all 0.3s;">
                                <input class="form-check-input" type="radio" name="dosPriority" id="dosMedium" value="Medium" required style="transform:scale(1.3);">
                                <label class="form-check-label fw-bold" for="dosMedium" style="color:#ea580c; font-size:1rem; margin-left:8px;">⚡ Medium</label>
                            </div>
                            <div class="form-check" style="padding:15px; background:#fff; border:2px solid #e1e8ed; border-radius:10px; min-width:120px; text-align:center; transition:all 0.3s;">
                                <input class="form-check-input" type="radio" name="dosPriority" id="dosLow" value="Low" required style="transform:scale(1.3);">
                                <label class="form-check-label fw-bold" for="dosLow" style="color:#16a34a; font-size:1rem; margin-left:8px;">🟢 Low</label>
                            </div>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <div id="dosMessage" class="alert d-none" style="border-radius:10px; margin-top:20px;"></div>
                </form>
            </div>
            <div class="modal-footer" style="border-bottom-left-radius:16px; border-bottom-right-radius:16px; padding:25px; background:#f8f9ff; border-top:1px solid #e1e8ed;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="padding:12px 30px; border-radius:8px; font-weight:500; border:2px solid #6c757d;">
                    <i class="ti ti-x"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="submitDos" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); border:none; padding:12px 30px; border-radius:8px; font-weight:600; box-shadow:0 4px 15px rgba(102, 126, 234, 0.4);">
                    <i class="ti ti-check"></i> Save DO
                </button>
            </div>
        </div>
    </div>
</div>

<!-- DON'T Modal (Beautiful Design) -->
<div class="modal fade" id="dontModal" tabindex="-1" aria-labelledby="dontModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #ff512f 0%, #dd2476 100%); color: #fff; border-top-left-radius:16px; border-top-right-radius:16px; padding: 25px;">
                <div style="display:flex; align-items:center; gap:15px;">
                    <span style="font-size:2.5rem;">🚫</span>
                    <div>
                        <h4 class="modal-title" id="dontModalLabel" style="margin-bottom:5px; font-weight:600;">Add New DON'T</h4>
                        <div style="font-size:1rem; color:#ffe0e6; opacity:0.9;">Capture actions to avoid for better productivity</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1); font-size:1.2rem;"></button>
            </div>
            <div class="modal-body" style="padding:30px; background:#fff5f5;">
                <form id="dontForm">
                    @csrf
                    <!-- What Field -->
                    <div class="mb-4">
                        <label for="dontWhat" class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:8px;">
                            <i class="ti ti-ban" style="color:#ff512f; margin-right:8px;"></i>What NOT to DO? 
                            <span style="color:#7f8c8d;font-size:0.9rem;">(Action to avoid)</span>
                        </label>
                        <input type="text" class="form-control" id="dontWhat" name="dontWhat" 
                               placeholder="e.g., Check social media during work hours" 
                               maxlength="100" required
                               style="border:2px solid #fecaca; border-radius:10px; padding:15px; font-size:1rem; transition:all 0.3s;">
                        <small class="text-muted" style="font-size:0.85rem;">
                            <i class="ti ti-info-circle"></i> Describe the action you should avoid (max 100 chars)
                        </small>
                    </div>

                    <!-- Why and How Fields Side by Side -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="dontWhy" class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:8px;">
                                <i class="ti ti-alert-triangle" style="color:#f59e0b; margin-right:8px;"></i>Why avoid this?
                            </label>
                            <textarea class="form-control" id="dontWhy" name="dontWhy" rows="4" 
                                      placeholder="e.g., Breaks concentration, leads to time waste, causes distraction from important tasks" 
                                      required
                                      style="border:2px solid #fecaca; border-radius:10px; padding:15px; font-size:0.95rem; resize:vertical; transition:all 0.3s;"></textarea>
                            <small class="text-muted" style="font-size:0.85rem;">
                                <i class="ti ti-arrow-right"></i> Explain why this action hurts productivity
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label for="dontImpact" class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:8px;">
                                <i class="ti ti-trending-down" style="color:#ef4444; margin-right:8px;"></i>Negative Impact
                            </label>
                            <textarea class="form-control" id="dontImpact" name="dontImpact" rows="4" 
                                      placeholder="e.g., Wastes 2+ hours daily, reduces focus by 60%, delays project completion" 
                                      required
                                      style="border:2px solid #fecaca; border-radius:10px; padding:15px; font-size:0.95rem; resize:vertical; transition:all 0.3s;"></textarea>
                            <small class="text-muted" style="font-size:0.85rem;">
                                <i class="ti ti-chart-line-down"></i> How does this harm your work efficiency?
                            </small>
                        </div>
                    </div>

                    <!-- Severity Level -->
                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:15px;">
                            <i class="ti ti-flame" style="color:#ef4444; margin-right:8px;"></i>Severity Level
                        </label>
                        <div class="d-flex gap-4 flex-wrap">
                            <div class="form-check" style="padding:15px; background:#fff; border:2px solid #fecaca; border-radius:10px; min-width:120px; text-align:center; transition:all 0.3s;">
                                <input class="form-check-input" type="radio" name="dontSeverity" id="dontCritical" value="Critical" required style="transform:scale(1.3);">
                                <label class="form-check-label fw-bold" for="dontCritical" style="color:#dc2626; font-size:1rem; margin-left:8px;">🚨 Critical</label>
                            </div>
                            <div class="form-check" style="padding:15px; background:#fff; border:2px solid #fecaca; border-radius:10px; min-width:120px; text-align:center; transition:all 0.3s;">
                                <input class="form-check-input" type="radio" name="dontSeverity" id="dontHigh" value="High" required style="transform:scale(1.3);">
                                <label class="form-check-label fw-bold" for="dontHigh" style="color:#ea580c; font-size:1rem; margin-left:8px;">⚠️ High</label>
                            </div>
                            <div class="form-check" style="padding:15px; background:#fff; border:2px solid #fecaca; border-radius:10px; min-width:120px; text-align:center; transition:all 0.3s;">
                                <input class="form-check-input" type="radio" name="dontSeverity" id="dontMedium" value="Medium" required style="transform:scale(1.3);">
                                <label class="form-check-label fw-bold" for="dontMedium" style="color:#f59e0b; font-size:1rem; margin-left:8px;">🔸 Medium</label>
                            </div>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <div id="dontMessage" class="alert d-none" style="border-radius:10px; margin-top:20px;"></div>
                </form>
            </div>
            <div class="modal-footer" style="border-bottom-left-radius:16px; border-bottom-right-radius:16px; padding:25px; background:#fff5f5; border-top:1px solid #fecaca;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="padding:12px 30px; border-radius:8px; font-weight:500; border:2px solid #6c757d;">
                    <i class="ti ti-x"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="submitDont" style="background:linear-gradient(135deg, #ff512f 0%, #dd2476 100%); border:none; padding:12px 30px; border-radius:8px; font-weight:600; box-shadow:0 4px 15px rgba(255, 81, 47, 0.4);">
                    <i class="ti ti-ban"></i> Save DON'T
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to form elements
    const formControls = document.querySelectorAll('#dosModal .form-control, #dontModal .form-control');
    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            if (this.closest('#dosModal')) {
                this.style.borderColor = '#667eea';
                this.style.boxShadow = '0 0 0 0.2rem rgba(102, 126, 234, 0.25)';
            } else {
                this.style.borderColor = '#ff512f';
                this.style.boxShadow = '0 0 0 0.2rem rgba(255, 81, 47, 0.25)';
            }
        });
        control.addEventListener('blur', function() {
            if (this.closest('#dosModal')) {
                this.style.borderColor = '#e1e8ed';
            } else {
                this.style.borderColor = '#fecaca';
            }
            this.style.boxShadow = 'none';
        });
    });

    // Add hover effects to radio buttons
    const radioContainers = document.querySelectorAll('#dosModal .form-check, #dontModal .form-check');
    radioContainers.forEach(container => {
        container.addEventListener('mouseenter', function() {
            if (this.closest('#dosModal')) {
                this.style.borderColor = '#667eea';
                this.style.backgroundColor = '#f8f9ff';
            } else {
                this.style.borderColor = '#ff512f';
                this.style.backgroundColor = '#fff5f5';
            }
        });
        container.addEventListener('mouseleave', function() {
            if (!this.querySelector('input').checked) {
                if (this.closest('#dosModal')) {
                    this.style.borderColor = '#e1e8ed';
                } else {
                    this.style.borderColor = '#fecaca';
                }
                this.style.backgroundColor = '#fff';
            }
        });
    });

    // Handle DO's form submission
    const submitDosBtn = document.getElementById('submitDos');
    if (submitDosBtn) {
        submitDosBtn.addEventListener('click', function() {
            const form = document.getElementById('dosForm');
            const messageDiv = document.getElementById('dosMessage');
            
            // Validate form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Get form values
            const dosWhat = document.getElementById('dosWhat').value;
            const dosWhy = document.getElementById('dosWhy').value;
            const dosImpact = document.getElementById('dosImpact').value;
            const dosPriority = document.querySelector('input[name="dosPriority"]:checked')?.value;

            // Show loading state
            this.innerHTML = '<i class="ti ti-loader ti-spin"></i> Saving...';
            this.disabled = true;

            // API call to save DO
            fetch('/dos', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    dosWhat: dosWhat,
                    dosWhy: dosWhy,
                    dosImpact: dosImpact,
                    dosPriority: dosPriority
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Show success message
                    messageDiv.className = 'alert alert-success';
                    messageDiv.innerHTML = '<i class="ti ti-check-circle"></i> <strong>Success!</strong> Your DO has been saved successfully.';
                    messageDiv.classList.remove('d-none');
                    
                    // Reset form
                    form.reset();
                    
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    
                } else {
                    // Show error message
                    messageDiv.className = 'alert alert-danger';
                    messageDiv.innerHTML = '<i class="ti ti-alert-circle"></i> <strong>Error!</strong> ' + (data.message || 'Failed to save. Please try again.');
                    messageDiv.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.className = 'alert alert-danger';
                messageDiv.innerHTML = '<i class="ti ti-alert-circle"></i> <strong>Error!</strong> Network error. Please try again.';
                messageDiv.classList.remove('d-none');
            })
            .finally(() => {
                // Reset button
                this.innerHTML = '<i class="ti ti-check"></i> Save DO';
                this.disabled = false;
            });
        });
    }

    // Handle DON'T form submission
    const submitDontBtn = document.getElementById('submitDont');
    if (submitDontBtn) {
        submitDontBtn.addEventListener('click', function() {
            const form = document.getElementById('dontForm');
            const messageDiv = document.getElementById('dontMessage');
            
            // Validate form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Get form values
            const dontWhat = document.getElementById('dontWhat').value;
            const dontWhy = document.getElementById('dontWhy').value;
            const dontImpact = document.getElementById('dontImpact').value;
            const dontSeverity = document.querySelector('input[name="dontSeverity"]:checked')?.value;

            // Show loading state
            this.innerHTML = '<i class="ti ti-loader ti-spin"></i> Saving...';
            this.disabled = true;

            // API call to save DON'T
            fetch('/donts', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    dontWhat: dontWhat,
                    dontWhy: dontWhy,
                    dontImpact: dontImpact,
                    dontSeverity: dontSeverity
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Show success message
                    messageDiv.className = 'alert alert-success';
                    messageDiv.innerHTML = '<i class="ti ti-check-circle"></i> <strong>Success!</strong> Your DON\'T has been saved successfully.';
                    messageDiv.classList.remove('d-none');
                    
                    // Reset form
                    form.reset();
                    
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    
                } else {
                    // Show error message
                    messageDiv.className = 'alert alert-danger';
                    messageDiv.innerHTML = '<i class="ti ti-alert-circle"></i> <strong>Error!</strong> ' + (data.message || 'Failed to save. Please try again.');
                    messageDiv.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.className = 'alert alert-danger';
                messageDiv.innerHTML = '<i class="ti ti-alert-circle"></i> <strong>Error!</strong> Network error. Please try again.';
                messageDiv.classList.remove('d-none');
            })
            .finally(() => {
                // Reset button
                this.innerHTML = '<i class="ti ti-ban"></i> Save DON\'T';
                this.disabled = false;
            });
        });
    }

    // Reset forms when modals are closed
    document.getElementById('dosModal')?.addEventListener('hidden.bs.modal', function() {
        const form = document.getElementById('dosForm');
        const messageDiv = document.getElementById('dosMessage');
        form.reset();
        messageDiv.classList.add('d-none');
    });

    document.getElementById('dontModal')?.addEventListener('hidden.bs.modal', function() {
        const form = document.getElementById('dontForm');
        const messageDiv = document.getElementById('dontMessage');
        form.reset();
        messageDiv.classList.add('d-none');
    });
});
</script>
