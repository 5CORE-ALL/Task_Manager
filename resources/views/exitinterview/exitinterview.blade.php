@extends('layouts.main')

@section('page-title')
    {{ __('Exit Interview Form') }}
@endsection

@section('page-breadcrumb')
    {{ __('Exit Interview') }}
@endsection

@push('page-head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">{{ __('Exit Interview Form') }}</h5>
                        <p class="text-muted mb-0">{{ __('Please fill in your details to access the exit interview form') }}</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="exitInterviewForm" class="needs-validation" novalidate>
                    @csrf
                    <div class="row">
                        <!-- Email Input -->
                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label for="email" class="form-label">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-mail"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           placeholder="{{ __('Enter your email address') }}"
                                           required>
                                    <div class="invalid-feedback">
                                        {{ __('Please provide a valid email address.') }}
                                    </div>
                                </div>
                                <small class="form-text text-muted">{{ __('We will use this email to send you the form confirmation.') }}</small>
                            </div>
                        </div>

                        <!-- Form Link Input (Readonly) -->
                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label for="form_link" class="form-label">{{ __('Exit Interview Form Link') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-link"></i>
                                    </span>
                                    <input type="url" 
                                           class="form-control" 
                                           id="form_link" 
                                           name="form_link" 
                                           value="https://docs.google.com/forms/d/e/1FAIpQLSczsUidaIR3YKIezxPz7SyIaaI0kHVq7NgM9ndUNByWEjT47Q/viewform"
                                           readonly>
                                    <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard()">
                                        <i class="ti ti-copy"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">{{ __('This is the official exit interview form link. Click the copy button to copy the link.') }}</small>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="col-md-12 mb-4">
                            <div class="alert alert-info" role="alert">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="ti ti-info-circle fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="alert-heading">{{ __('Instructions') }}</h6>
                                        <p class="mb-0">
                                            {{ __('1. Please ensure your email address is correct.') }}<br>
                                            {{ __('2. Click the "Send Form Link" button to receive the form via email.') }}<br>
                                            {{ __('3. You can also copy the form link directly using the copy button.') }}<br>
                                            {{ __('4. The exit interview form is confidential and helps us improve our workplace.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="button" class="btn btn-outline-primary" onclick="openFormInNewTab()">
                                        <i class="ti ti-external-link me-2"></i>{{ __('Open Form') }}
                                    </button>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-send me-2"></i>{{ __('Send Form Link') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">
                    <i class="ti ti-check-circle me-2"></i>{{ __('Form Link Sent Successfully') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="ti ti-mail-check text-success" style="font-size: 4rem;"></i>
                </div>
                <h6>{{ __('Email Sent!') }}</h6>
                <p class="text-muted mb-0">
                    {{ __('The exit interview form link has been sent to your email address. Please check your inbox and spam folder.') }}
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Form submission handler
document.getElementById('exitInterviewForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form elements
    const form = e.target;
    const email = document.getElementById('email').value;
    
    // Validate form
    if (!form.checkValidity()) {
        e.stopPropagation();
        form.classList.add('was-validated');
        return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="ti ti-loader-2 spin me-2"></i>{{ __("Sending...") }}';
    submitBtn.disabled = true;
    
    // Create FormData object
    const formData = new FormData(form);
    
    // Make AJAX request to send email
    fetch('{{ route("exitinterview.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            // Show success modal
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            
            // Reset form
            form.reset();
            form.classList.remove('was-validated');
            
            // Show success toast
            showToast(data.message, 'success');
        } else {
            // Show error toast
            showToast(data.message || '{{ __("Failed to send email. Please try again.") }}', 'error');
        }
    })
.catch(error => {
    console.error('Error:', error);
    
    // Reset button
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
    
    // Show detailed error in development, generic in production
    const errorMsg = '{{ env("APP_DEBUG") ? "An error occurred: " : "An error occurred. Please try again later." }}';
    showToast(errorMsg, 'error');
})
});

// Copy to clipboard function
function copyToClipboard() {
    const formLink = document.getElementById('form_link');
    formLink.select();
    formLink.setSelectionRange(0, 99999); // For mobile devices
    
    navigator.clipboard.writeText(formLink.value).then(function() {
        // Show temporary tooltip or notification
        showToast('{{ __("Form link copied to clipboard!") }}', 'success');
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        showToast('{{ __("Failed to copy link. Please copy manually.") }}', 'error');
    });
}

// Open form in new tab
function openFormInNewTab() {
    const formLink = document.getElementById('form_link').value;
    window.open(formLink, '_blank');
}

// Simple toast notification function
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Add to page
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Add custom CSS for spinning animation
const style = document.createElement('style');
style.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .btn:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
`;
document.head.appendChild(style);
</script>
@endpush
