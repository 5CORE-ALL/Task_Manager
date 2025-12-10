{{-- Incentive Notification Widget --}}
@php
    $userIncentiveData = null;
    if (class_exists('\App\Models\Incentive') && Auth::check()) {
        $userIncentiveData = \App\Models\Incentive::where('receiver_id', Auth::id())
                                              ->where('status', 'active')
                                              ->whereDate('start_date', '<=', now())
                                              ->whereDate('end_date', '>=', now())
                                              ->selectRaw('COUNT(*) as count, SUM(amount) as total_amount')
                                              ->first();
    }
@endphp

@if($userIncentiveData && $userIncentiveData->count > 0)
<div class="incentive-notification" id="incentiveNotification" style="position: fixed; top: 80px; right: 20px; z-index: 1050; max-width: 350px;">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ti ti-gift me-2"></i>
        <strong>{{ __('Active Incentive!') }}</strong><br>
        {{ __('You have :count active incentive(s) worth $:amount this period.', [
            'count' => $userIncentiveData->count, 
            'amount' => number_format($userIncentiveData->total_amount, 2)
        ]) }}
        <a href="{{ route('incentives.index') }}" class="btn btn-sm btn-outline-success mt-2">{{ __('View Details') }}</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

<style>
.incentive-notification .alert {
    border-left: 4px solid #28a745;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>

<script>
$(document).ready(function() {
    // Check if incentive notification should be shown
    const incentiveNotification = $('#incentiveNotification');
    
    if (incentiveNotification.length > 0) {
        // Check if notification was already shown in this session
        const notificationShown = sessionStorage.getItem('incentive_notification_shown');
        
        if (!notificationShown) {
            // Play notification sound
            try {
                // Create audio context for notification sound
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
                oscillator.frequency.setValueAtTime(800, audioContext.currentTime + 0.2);
                
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.3);
            } catch (e) {
                console.log('Audio notification not available');
            }
            
            // Mark notification as shown for this session
            sessionStorage.setItem('incentive_notification_shown', 'true');
            
            // Auto-hide after 10 seconds
            setTimeout(function() {
                incentiveNotification.fadeOut();
            }, 10000);
        } else {
            // Hide notification if already shown
            incentiveNotification.hide();
        }
    }
    
    // Clear session storage when notification is manually closed
    $('.incentive-notification .btn-close').on('click', function() {
        sessionStorage.removeItem('incentive_notification_shown');
    });
});
</script>
@endif
