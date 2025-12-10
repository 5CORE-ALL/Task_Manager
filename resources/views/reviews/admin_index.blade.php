@extends('layouts.main')

@section('page-title')
    {{ __('Reviews Management') }}
@endsection

@section('page-breadcrumb')
    {{ __('Reviews') }}
@endsection

@section('content')
    <!-- Beautiful Review Loader -->
    <div id="reviewLoader" class="review-loader-overlay">
        <div class="review-loader-container">
            <div class="review-loader-content">
                <div class="loader-icon">
                    <div class="celestial-elements">
                        <i class="fas fa-globe earth-1"></i>
                        <i class="fas fa-moon moon-1"></i>
                        <i class="fas fa-star star-1"></i>
                        <i class="fas fa-globe earth-2"></i>
                        <i class="fas fa-star star-2"></i>
                        <i class="fas fa-moon moon-2"></i>
                        <i class="fas fa-star star-3"></i>
                        <i class="fas fa-globe earth-3"></i>
                    </div>
                    <div class="center-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
                <div class="loader-text">
                    <h4>Loading Reviews...</h4>
                    <div class="loading-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="mainContent" style="display: none;">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('All Reviews') }}</h5>
                    <small class="text-muted">{{ __('Manage all employee reviews') }}</small>
                </div>
                <div class="card-body">
                    <!-- Average Rating Card -->
                    <div class="row mb-4">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4 class="card-title mb-2">{{ __('Average Rating') }}</h4>
                                    <div class="mb-2">
                                        <span id="averageRatingStars">
                                            @php
                                                $overallAverage = $reviews->avg('rating');
                                                $roundedAverage = round($overallAverage);
                                            @endphp
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $roundedAverage)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-light"></i>
                                                @endif
                                            @endfor
                                        </span>
                                    </div>
                                    <h3 class="mb-1">
                                        <span id="averageRatingValue">{{ number_format($overallAverage, 1) }}</span>/5
                                    </h3>
                                    <small id="averageRatingText">{{ __('Overall Average') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    <!-- Search Filters -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="reviewer_search" class="form-label">{{ __('Reviewer') }}</label>
                            <div class="position-relative">
                                <input type="text" class="form-control" id="reviewer_search" placeholder="{{ __('Search reviewer...') }}">
                                <input type="hidden" id="reviewer_id">
                                <div id="reviewerDropdown" class="dropdown-menu position-absolute w-100" style="display: none; max-height: 200px; overflow-y: auto;"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="reviewee_search" class="form-label">{{ __('Reviewee') }}</label>
                            <div class="position-relative">
                                <input type="text" class="form-control" id="reviewee_search" placeholder="{{ __('Search reviewee...') }}">
                                <input type="hidden" id="reviewee_id">
                                <div id="revieweeDropdown" class="dropdown-menu position-absolute w-100" style="display: none; max-height: 200px; overflow-y: auto;"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="rating_search" class="form-label">{{ __('Rating') }}</label>
                            <select class="form-control" id="rating_search">
                                <option value="">{{ __('All Ratings') }}</option>
                                <option value="5">5 {{ __('Stars') }}</option>
                                <option value="4">4 {{ __('Stars') }}</option>
                                <option value="3">3 {{ __('Stars') }}</option>
                                <option value="2">2 {{ __('Stars') }}</option>
                                <option value="1">1 {{ __('Star') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-secondary" id="clearBtn">{{ __('Clear Filters') }}</button>
                            <button type="button" class="btn btn-primary ms-2" id="refreshBtn">
                                <i class="fas fa-sync-alt"></i> {{ __('Refresh Table') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Sr. No.') }}</th>
                                    <th>{{ __('Reviewer') }}</th>
                                    <th>{{ __('Reviewee') }}</th>
                                    <th>{{ __('Rating') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Screenshot') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $index => $review)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $review->reviewer ? $review->reviewer->name : 'N/A' }}</td>
                                        <td>{{ $review->reviewee ? $review->reviewee->name : 'N/A' }}</td>
                                        <td>
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                            ({{ $review->rating }}/5)
                                        </td>
                                        <td>
                                            <div class="text-wrap" style="max-width: 300px;">
                                                {{ $review->description }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($review->screenshot)
                                                <a href="{{ asset($review->screenshot) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-image"></i> {{ __('View Screenshot') }}
                                                </a>
                                            @else
                                                <span class="text-muted">{{ __('No Screenshot') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ date('M d, Y H:i', strtotime($review->created_at)) }}</td>
                                        <td>
                                            <div class="action-btn bg-danger ms-2">
                                                <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center text-white" 
                                                   onclick="deleteReview({{ $review->id }})" title="{{ __('Delete') }}">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('No reviews found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let employees = [];
    
    $(document).ready(function() {
        // Show loader initially
        $('#reviewLoader').show();
        $('#mainContent').hide();
        
        // Simulate loading time and then show content
        setTimeout(function() {
            $('#reviewLoader').fadeOut(800, function() {
                $('#mainContent').fadeIn(600);
            });
        }, 2000); // 2 seconds loading time
        
        // Load employees when page loads
        loadEmployees();
        
        // Clear filters functionality
        $('#clearBtn').on('click', function() {
            clearFilters();
        });
        
        // Refresh table functionality
        $('#refreshBtn').on('click', function() {
            refreshTable();
        });
        
        // Auto-filter on rating change
        $('#rating_search').on('change', function() {
            filterTable();
        });
    });
    
    // Load employees function (same as in header.blade.php)
    function loadEmployees() {
        $.ajax({
            url: '{{ route("reviews.employees") }}',
            method: 'GET',
            success: function(response) {
                employees = response.employees || response;
            },
            error: function(xhr, status, error) {
                console.error('Error loading employees:', error);
            }
        });
    }
    
    // Reviewer search dropdown
    $('#reviewer_search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const dropdown = $('#reviewerDropdown');
        
        if (searchTerm.length === 0) {
            dropdown.hide();
            $('#reviewer_id').val('');
            filterTable(); // Auto-filter when cleared
            return;
        }
        
        const filteredEmployees = employees.filter(employee => 
            employee.name.toLowerCase().includes(searchTerm)
        );
        
        if (filteredEmployees.length > 0) {
            let dropdownHtml = '';
            filteredEmployees.forEach(employee => {
                dropdownHtml += `<a class="dropdown-item reviewer-item" href="#" data-id="${employee.id}" data-name="${employee.name}">${employee.name}</a>`;
            });
            dropdown.html(dropdownHtml).show();
        } else {
            dropdown.html('<span class="dropdown-item-text text-muted">No employees found</span>').show();
        }
        
        // Auto-filter as user types
        filterTable();
    });
    
    // Reviewee search dropdown
    $('#reviewee_search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const dropdown = $('#revieweeDropdown');
        
        if (searchTerm.length === 0) {
            dropdown.hide();
            $('#reviewee_id').val('');
            filterTable(); // Auto-filter when cleared
            return;
        }
        
        const filteredEmployees = employees.filter(employee => 
            employee.name.toLowerCase().includes(searchTerm)
        );
        
        if (filteredEmployees.length > 0) {
            let dropdownHtml = '';
            filteredEmployees.forEach(employee => {
                dropdownHtml += `<a class="dropdown-item reviewee-item" href="#" data-id="${employee.id}" data-name="${employee.name}">${employee.name}</a>`;
            });
            dropdown.html(dropdownHtml).show();
        } else {
            dropdown.html('<span class="dropdown-item-text text-muted">No employees found</span>').show();
        }
        
        // Auto-filter as user types
        filterTable();
    });
    
    // Handle reviewer selection
    $(document).on('click', '.reviewer-item', function(e) {
        e.preventDefault();
        const employeeId = $(this).data('id');
        const employeeName = $(this).data('name');
        
        $('#reviewer_search').val(employeeName);
        $('#reviewer_id').val(employeeId);
        $('#reviewerDropdown').hide();
        
        // Auto-filter when selection is made
        filterTable();
    });
    
    // Handle reviewee selection
    $(document).on('click', '.reviewee-item', function(e) {
        e.preventDefault();
        const employeeId = $(this).data('id');
        const employeeName = $(this).data('name');
        
        $('#reviewee_search').val(employeeName);
        $('#reviewee_id').val(employeeId);
        $('#revieweeDropdown').hide();
        
        // Auto-filter when selection is made
        filterTable();
    });
    
    // Hide dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#reviewer_search, #reviewerDropdown').length) {
            $('#reviewerDropdown').hide();
        }
        if (!$(e.target).closest('#reviewee_search, #revieweeDropdown').length) {
            $('#revieweeDropdown').hide();
        }
    });
    
    // Clear hidden fields when input is manually cleared
    $('#reviewer_search').on('keyup', function() {
        if ($(this).val() === '') {
            $('#reviewer_id').val('');
            filterTable(); // Auto-filter when cleared
        }
    });
    
    $('#reviewee_search').on('keyup', function() {
        if ($(this).val() === '') {
            $('#reviewee_id').val('');
            filterTable(); // Auto-filter when cleared
        }
    });
    
    // Filter table function
    function filterTable() {
        const reviewerName = $('#reviewer_search').val().toLowerCase();
        const revieweeName = $('#reviewee_search').val().toLowerCase();
        const ratingFilter = $('#rating_search').val();
        
        let visibleRows = [];
        let totalRating = 0;
        let ratingCount = 0;
        
        $('tbody tr').each(function() {
            const row = $(this);
            const reviewer = row.find('td:nth-child(2)').text().toLowerCase();
            const reviewee = row.find('td:nth-child(3)').text().toLowerCase();
            const ratingText = row.find('td:nth-child(4)').text();
            const rating = ratingText.match(/\((\d+)\/5\)/)?.[1] || '';
            
            let showRow = true;
            
            // Check reviewer filter
            if (reviewerName && !reviewer.includes(reviewerName)) {
                showRow = false;
            }
            
            // Check reviewee filter
            if (revieweeName && !reviewee.includes(revieweeName)) {
                showRow = false;
            }
            
            // Check rating filter
            if (ratingFilter && rating !== ratingFilter) {
                showRow = false;
            }
            
            // Show/hide row
            if (showRow) {
                row.show();
                visibleRows.push(row);
                if (rating) {
                    totalRating += parseInt(rating);
                    ratingCount++;
                }
            } else {
                row.hide();
            }
        });
        
        // Update average rating
        updateAverageRating(totalRating, ratingCount, revieweeName);
    }
    
    // Update average rating display
    function updateAverageRating(totalRating, ratingCount, revieweeName) {
        let averageRating = 0;
        let displayText = '{{ __("Overall Average") }}';
        
        if (ratingCount > 0) {
            averageRating = totalRating / ratingCount;
        }
        
        // Update the text based on filter
        if (revieweeName) {
            // Find the actual reviewee name from visible rows
            let actualRevieweeName = '';
            $('tbody tr:visible').each(function() {
                const reviewee = $(this).find('td:nth-child(3)').text();
                if (reviewee.toLowerCase().includes(revieweeName) && reviewee !== 'N/A') {
                    actualRevieweeName = reviewee;
                    return false; // break the loop
                }
            });
            displayText = actualRevieweeName ? `${actualRevieweeName}'s Average` : 'Filtered Average';
        }
        
        // Update the rating value
        $('#averageRatingValue').text(ratingCount > 0 ? averageRating.toFixed(1) : '0.0');
        $('#averageRatingText').text(displayText);
        
        // Update the stars
        const roundedAverage = Math.round(averageRating);
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= roundedAverage) {
                starsHtml += '<i class="fas fa-star text-warning"></i>';
            } else {
                starsHtml += '<i class="far fa-star text-light"></i>';
            }
        }
        $('#averageRatingStars').html(starsHtml);
    }
    
    // Clear all filters
    function clearFilters() {
        $('#reviewer_search').val('');
        $('#reviewee_search').val('');
        $('#rating_search').val('');
        $('#reviewer_id').val('');
        $('#reviewee_id').val('');
        $('#reviewerDropdown').hide();
        $('#revieweeDropdown').hide();
        
        // Show all rows
        $('tbody tr').show();
        
        // Reset average rating to overall average
        resetToOverallAverage();
    }
    
    // Reset to overall average rating
    function resetToOverallAverage() {
        let totalRating = 0;
        let ratingCount = 0;
        
        $('tbody tr').each(function() {
            const ratingText = $(this).find('td:nth-child(4)').text();
            const rating = ratingText.match(/\((\d+)\/5\)/)?.[1] || '';
            if (rating) {
                totalRating += parseInt(rating);
                ratingCount++;
            }
        });
        
        updateAverageRating(totalRating, ratingCount, '');
    }

    // Refresh table function
    function refreshTable() {
        const refreshButton = $('#refreshBtn');
        const refreshIcon = refreshButton.find('i');
        
        // Show loading state
        refreshButton.prop('disabled', true);
        refreshIcon.removeClass('fa-sync-alt').addClass('fa-spinner fa-spin');
        refreshButton.html('<i class="fas fa-spinner fa-spin"></i> {{ __("Refreshing...") }}');
        
        // Make AJAX request to get fresh data
        $.ajax({
            url: '{{ route("reviews.index") }}',
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            },
            success: function(response) {
                try {
                    // Create a temporary element to parse the HTML
                    const tempDiv = $('<div>').html(response);
                    
                    // Look specifically for the table within the reviews structure
                    const reviewTable = tempDiv.find('.table-striped tbody');
                    
                    if (reviewTable.length > 0) {
                        // Get only the table rows that contain review data (should have 8 columns)
                        const validReviewRows = reviewTable.find('tr').filter(function() {
                            const cells = $(this).find('td');
                            // Check if it's a valid review row (has 8 columns and contains review data)
                            return cells.length === 8 && 
                                   (cells.eq(1).text().trim() !== '' || cells.eq(0).attr('colspan') === '8'); // Either has reviewer data or is "No reviews found"
                        });
                        
                        if (validReviewRows.length > 0) {
                            // Replace the table body content with only valid review rows
                            $('tbody').html(validReviewRows);
                            
                            // Recalculate and update average rating
                            resetToOverallAverage();
                            
                            // Clear any active filters and show all rows
                            clearFilters();
                            
                            // Show success message
                            showRefreshMessage('{{ __("Table refreshed successfully!") }}', 'success');
                        } else {
                            showRefreshMessage('{{ __("No valid review data found.") }}', 'error');
                        }
                    } else {
                        showRefreshMessage('{{ __("Could not find table data in response.") }}', 'error');
                    }
                } catch (error) {
                    console.error('Error parsing response:', error);
                    showRefreshMessage('{{ __("Error processing table data.") }}', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error refreshing table:', error);
                showRefreshMessage('{{ __("Failed to refresh table. Please try again.") }}', 'error');
            },
            complete: function() {
                // Reset button state
                setTimeout(function() {
                    refreshButton.prop('disabled', false);
                    refreshIcon.removeClass('fa-spinner fa-spin').addClass('fa-sync-alt');
                    refreshButton.html('<i class="fas fa-sync-alt"></i> {{ __("Refresh Table") }}');
                }, 500);
            }
        });
    }
    
    // Show refresh message
    function showRefreshMessage(message, type) {
        // Remove any existing message
        $('.refresh-message').remove();
        
        // Create and show new message
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const messageHtml = `
            <div class="alert ${alertClass} refresh-message" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <button type="button" class="btn-close" onclick="$(this).parent().fadeOut()"></button>
                ${message}
            </div>
        `;
        
        $('body').append(messageHtml);
        
        // Auto hide after 3 seconds
        setTimeout(function() {
            $('.refresh-message').fadeOut();
        }, 3000);
    }

    function deleteReview(id) {
        if (confirm('{{ __("Are you sure you want to delete this review?") }}')) {
            $.ajax({
                url: '{{ url("reviews") }}/' + id,
                type: 'DELETE',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.error);
                    }
                },
                error: function(xhr) {
                    alert('Something went wrong.');
                }
            });
        }
    }
</script>

<style>
/* Beautiful Review Loader Styles */
.review-loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #000428 0%, #004e92 50%, #000000 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    animation: spaceShift 5s ease-in-out infinite alternate;
    overflow: hidden;
}

/* Add twinkling stars background */
.review-loader-overlay::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(2px 2px at 20px 30px, #eee, transparent),
        radial-gradient(2px 2px at 40px 70px, #fff, transparent),
        radial-gradient(1px 1px at 90px 40px, #ddd, transparent),
        radial-gradient(1px 1px at 130px 80px, #fff, transparent),
        radial-gradient(2px 2px at 160px 30px, #eee, transparent);
    background-repeat: repeat;
    background-size: 200px 100px;
    animation: starfield 20s linear infinite;
    opacity: 0.8;
}

@keyframes starfield {
    from { transform: translate(0, 0); }
    to { transform: translate(-200px, -100px); }
}

@keyframes spaceShift {
    0% { background: linear-gradient(135deg, #000428 0%, #004e92 50%, #000000 100%); }
    50% { background: linear-gradient(135deg, #232526 0%, #414345 50%, #000000 100%); }
    100% { background: linear-gradient(135deg, #0f0c29 0%, #24243e 50%, #313862 100%); }
}

.review-loader-container {
    text-align: center;
    color: white;
    position: relative;
    z-index: 10;
}

.loader-icon {
    position: relative;
    width: 160px;
    height: 160px;
    margin: 0 auto 30px;
}

.celestial-elements {
    position: absolute;
    width: 100%;
    height: 100%;
    animation: solarSystem 8s linear infinite;
}

@keyframes solarSystem {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Earth Styles */
.celestial-elements .fa-globe {
    position: absolute;
    font-size: 24px;
    color: #4CAF50;
    animation: earthRotation 4s ease-in-out infinite;
    background: linear-gradient(45deg, #2196F3, #4CAF50, #8BC34A);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

@keyframes earthRotation {
    0%, 100% { 
        transform: scale(1) rotate(0deg);
        text-shadow: 0 0 20px rgba(76, 175, 80, 0.8);
        filter: hue-rotate(0deg);
    }
    25% { 
        transform: scale(1.1) rotate(90deg);
        text-shadow: 0 0 30px rgba(33, 150, 243, 0.8);
        filter: hue-rotate(90deg);
    }
    50% { 
        transform: scale(1.2) rotate(180deg);
        text-shadow: 0 0 25px rgba(139, 195, 74, 0.8);
        filter: hue-rotate(180deg);
    }
    75% { 
        transform: scale(1.1) rotate(270deg);
        text-shadow: 0 0 30px rgba(33, 150, 243, 0.8);
        filter: hue-rotate(270deg);
    }
}

/* Moon Styles */
.celestial-elements .fa-moon {
    position: absolute;
    font-size: 20px;
    color: #E8EAF6;
    animation: moonPhases 3s ease-in-out infinite alternate;
}

@keyframes moonPhases {
    0% { 
        transform: scale(0.9);
        text-shadow: 0 0 15px rgba(232, 234, 246, 0.6);
        opacity: 0.8;
        color: #B0BEC5;
    }
    50% { 
        transform: scale(1.1);
        text-shadow: 0 0 25px rgba(255, 255, 255, 0.9);
        opacity: 1;
        color: #E8EAF6;
    }
    100% { 
        transform: scale(1.2);
        text-shadow: 0 0 30px rgba(255, 255, 255, 1);
        opacity: 0.9;
        color: #FFFFFF;
    }
}

/* Star Styles */
.celestial-elements .fa-star {
    position: absolute;
    font-size: 18px;
    color: #FFD700;
    animation: stellarTwinkle 2s ease-in-out infinite alternate;
}

@keyframes stellarTwinkle {
    0% { 
        transform: scale(0.7) rotate(0deg);
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
        opacity: 0.6;
    }
    25% { 
        transform: scale(1.0) rotate(45deg);
        text-shadow: 0 0 20px rgba(255, 223, 0, 0.8);
        opacity: 0.9;
    }
    50% { 
        transform: scale(1.3) rotate(90deg);
        text-shadow: 0 0 25px rgba(255, 215, 0, 1);
        opacity: 1;
    }
    75% { 
        transform: scale(1.1) rotate(135deg);
        text-shadow: 0 0 20px rgba(255, 223, 0, 0.8);
        opacity: 0.8;
    }
    100% { 
        transform: scale(0.8) rotate(180deg);
        text-shadow: 0 0 15px rgba(255, 215, 0, 0.7);
        opacity: 0.7;
    }
}

/* Positioning for celestial elements - like a solar system */
.earth-1 { top: 5%; left: 50%; transform: translateX(-50%); animation-delay: 0s; }
.moon-1 { top: 25%; right: 10%; animation-delay: 0.7s; }
.star-1 { top: 45%; right: 0%; animation-delay: 1.4s; }
.earth-2 { bottom: 25%; right: 10%; animation-delay: 2.1s; }
.star-2 { bottom: 5%; left: 50%; transform: translateX(-50%); animation-delay: 2.8s; }
.moon-2 { bottom: 25%; left: 10%; animation-delay: 3.5s; }
.star-3 { top: 45%; left: 0%; animation-delay: 4.2s; }
.earth-3 { top: 25%; left: 10%; animation-delay: 4.9s; }

.center-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    width: 80px;
    height: 80px;
    display: flex;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(20px);
    border: 3px solid rgba(255, 255, 255, 0.2);
    animation: centerIconFloat 4s ease-in-out infinite alternate;
    box-shadow: 
        0 0 30px rgba(255, 255, 255, 0.2),
        inset 0 0 20px rgba(255, 255, 255, 0.1);
}

@keyframes centerIconFloat {
    0% { 
        transform: translate(-50%, -50%) scale(1);
        box-shadow: 
            0 0 30px rgba(255, 255, 255, 0.2),
            inset 0 0 20px rgba(255, 255, 255, 0.1);
    }
    100% { 
        transform: translate(-50%, -50%) scale(1.1);
        box-shadow: 
            0 0 50px rgba(255, 255, 255, 0.4),
            inset 0 0 30px rgba(255, 255, 255, 0.2);
    }
}

.center-icon .fas {
    font-size: 36px;
    color: white;
    text-shadow: 0 0 25px rgba(255, 255, 255, 0.8);
    animation: iconPulse 3s ease-in-out infinite alternate;
}

@keyframes iconPulse {
    0% { opacity: 0.8; }
    100% { opacity: 1; }
}

.loader-text h4 {
    font-size: 32px;
    font-weight: 600;
    margin-bottom: 25px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    animation: textGlow 4s ease-in-out infinite alternate;
    background: linear-gradient(45deg, #fff, #4CAF50, #2196F3, #FFD700);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 400% 400%;
    animation: gradientText 3s ease infinite, textGlow 4s ease-in-out infinite alternate;
}

@keyframes gradientText {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes textGlow {
    0% { 
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        filter: brightness(1);
    }
    100% { 
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5), 0 0 30px rgba(255, 255, 255, 0.6);
        filter: brightness(1.3);
    }
}

.loading-dots {
    display: flex;
    justify-content: center;
    gap: 12px;
}

.loading-dots span {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    animation: planetDots 2s ease-in-out infinite;
}

.loading-dots span:nth-child(1) { 
    background: linear-gradient(45deg, #2196F3, #4CAF50);
    animation-delay: 0s; 
}
.loading-dots span:nth-child(2) { 
    background: linear-gradient(45deg, #E8EAF6, #FFFFFF);
    animation-delay: 0.4s; 
}
.loading-dots span:nth-child(3) { 
    background: linear-gradient(45deg, #FFD700, #FFF176);
    animation-delay: 0.8s; 
}

@keyframes planetDots {
    0%, 60%, 100% {
        transform: scale(1);
        opacity: 0.7;
    }
    30% {
        transform: scale(1.5);
        opacity: 1;
        box-shadow: 0 0 20px currentColor;
    }
}

/* Search dropdown styles */
#reviewerDropdown, #revieweeDropdown {
    z-index: 1050;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

#reviewerDropdown .dropdown-item, #revieweeDropdown .dropdown-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
    border: none;
}

#reviewerDropdown .dropdown-item:hover, #revieweeDropdown .dropdown-item:hover {
    background-color: #f8f9fa;
}

#reviewerDropdown .dropdown-item-text, #revieweeDropdown .dropdown-item-text {
    padding: 0.5rem 1rem;
    color: #6c757d;
}

/* Responsive loader */
@media (max-width: 768px) {
    .loader-icon {
        width: 140px;
        height: 140px;
    }
    
    .celestial-elements .fa-globe {
        font-size: 20px;
    }
    
    .celestial-elements .fa-moon {
        font-size: 16px;
    }
    
    .celestial-elements .fa-star {
        font-size: 14px;
    }
    
    .center-icon {
        width: 70px;
        height: 70px;
    }
    
    .center-icon .fas {
        font-size: 32px;
    }
    
    .loader-text h4 {
        font-size: 28px;
    }
    
    .loading-dots span {
        width: 14px;
        height: 14px;
    }
}
</style>
@endpush
