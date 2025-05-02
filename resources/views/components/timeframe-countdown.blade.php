<div class="mb-6">
    <div id="timeframeCountdown">
        <div class="bg-white/90 backdrop-blur-sm shadow-lg rounded-xl p-6">
            <div id="countdownContent" class="space-y-6">
                <!-- Loading State -->
                <div class="text-center animate-pulse text-gray-600">
                    <div class="inline-block">
                        <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="mt-2 text-gray-500">Loading timeframe information...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.countdown-box {
    transform: translateY(0);
    transition: all 0.3s ease;
}

.countdown-box:hover {
    transform: translateY(-2px);
}

.countdown-number {
    animation: pulse 1s infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.gradient-text {
    background: linear-gradient(45deg, #3b82f6, #10b981);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hover-scale {
    transition: transform 0.2s ease;
}

.hover-scale:hover {
    transform: scale(1.05);
}

.glow {
    animation: glow 2s infinite alternate;
}

@keyframes glow {
    from {
        box-shadow: 0 0 5px -5px #3b82f6;
    }
    to {
        box-shadow: 0 0 20px -5px #3b82f6;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchTimeframeInfo();
    
    function fetchTimeframeInfo() {
        fetch('{{ route('timeframe.active') }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateTimeframes(data);
                } else {
                    showNoTimeframe();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError();
            });
    }

    function updateTimeframes(data) {
        const content = document.getElementById('countdownContent');
        let html = '';

        if (data.has_active) {
            html += createTimeframeSection(data.active_timeframe);
        } else {
            html = `
                <div class="text-center">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <i class="fas fa-calendar-xmark text-4xl mb-4 text-gray-400"></i>
                        <p class="text-lg font-semibold text-gray-700">No Active Period</p>
                        <p class="text-sm mt-2 text-gray-500">Check back later for the next academic period</p>
                    </div>
                </div>
            `;
        }

        content.innerHTML = html;
    }

    function createTimeframeSection(timeframe) {
        const proposalDeadline = new Date(timeframe.proposal_deadline);
        const startDate = new Date(timeframe.start_date);
        const endDate = new Date(timeframe.end_date);
        const days = Math.floor(timeframe.days_to_proposal);

        // Create countdown HTML
        let countdownHtml = timeframe.proposal_passed ? `
            <div class="bg-red-50 rounded-lg p-6 mt-6">
                <div class="text-center">
                    <i class="fas fa-exclamation-circle text-4xl text-red-400 mb-3"></i>
                    <h4 class="text-xl font-semibold text-red-700">Proposal Submission Closed</h4>
                    <p class="text-red-600/80 mt-2">The deadline has passed</p>
                </div>
            </div>
        ` : `
            <div class="bg-emerald-50 rounded-lg p-6 mt-6">
                <h4 class="text-xl font-semibold text-emerald-700 text-center mb-4">Proposal Submission Deadline</h4>
                <div class="grid grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm text-center">
                        <div class="text-3xl font-bold text-emerald-600">${days}</div>
                        <div class="text-xs uppercase tracking-wider text-emerald-600/70">Days</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm text-center">
                        <div class="text-3xl font-bold text-emerald-600">${timeframe.hours_to_proposal}</div>
                        <div class="text-xs uppercase tracking-wider text-emerald-600/70">Hours</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm text-center">
                        <div class="text-3xl font-bold text-emerald-600">${timeframe.minutes_to_proposal}</div>
                        <div class="text-xs uppercase tracking-wider text-emerald-600/70">Minutes</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm text-center">
                        <div class="text-3xl font-bold text-emerald-600" id="seconds">00</div>
                        <div class="text-xs uppercase tracking-wider text-emerald-600/70">Seconds</div>
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <p class="text-emerald-600/70 text-sm">
                        <i class="far fa-clock mr-2"></i>Deadline: ${proposalDeadline.toLocaleString('en-GB')}
                    </p>
                </div>
            </div>
        `;

        return `
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Current Academic Period</h3>
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700">
                        ${timeframe.academic_year} - Semester ${timeframe.semester}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-700 mb-3">Period Duration</h4>
                        <div class="space-y-2">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-calendar-alt w-5 mr-2 text-blue-500"></i>
                                <span>Start: ${startDate.toLocaleDateString('en-GB', { 
                                    day: 'numeric', 
                                    month: 'long', 
                                    year: 'numeric'
                                })}</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-calendar-check w-5 mr-2 text-blue-500"></i>
                                <span>End: ${endDate.toLocaleDateString('en-GB', { 
                                    day: 'numeric', 
                                    month: 'long', 
                                    year: 'numeric'
                                })}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-700 mb-3">Important Limits</h4>
                        <div class="space-y-2">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-file-alt w-5 mr-2 text-blue-500"></i>
                                <span>Max ${timeframe.max_applications_per_student} applications per student</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-handshake w-5 mr-2 text-blue-500"></i>
                                <span>Max ${timeframe.max_appointments_per_student} appointments per student</span>
                            </div>
                        </div>
                    </div>
                </div>

                ${countdownHtml}
            </div>
        `;
    }

    function showNoTimeframe() {
        const content = document.getElementById('countdownContent');
        content.innerHTML = `
            <div class="text-center">
                <div class="bg-gray-50 rounded-lg p-6">
                    <i class="fas fa-calendar-xmark text-4xl mb-4 text-gray-400"></i>
                    <p class="text-lg font-semibold text-gray-700">No Active Timeframe</p>
                    <p class="text-sm mt-2 text-gray-500">Please check back later</p>
                </div>
            </div>
        `;
    }

    function showError() {
        const content = document.getElementById('countdownContent');
        content.innerHTML = `
            <div class="text-center">
                <div class="bg-red-50 rounded-lg p-6">
                    <i class="fas fa-exclamation-circle text-4xl mb-4 text-red-400"></i>
                    <p class="text-lg font-semibold text-red-700">Error Loading Information</p>
                    <p class="text-sm mt-2 text-red-600">Please try again later</p>
                </div>
            </div>
        `;
    }

    // Update seconds in real-time
    function updateSeconds() {
        const secondsElement = document.getElementById('seconds');
        if (secondsElement) {
            const now = new Date();
            const seconds = 59 - now.getSeconds();
            secondsElement.textContent = seconds.toString().padStart(2, '0');
        }
    }

    // Update the countdown every second
    setInterval(() => {
        updateSeconds();
        // Re-fetch timeframe data every minute
        if (new Date().getSeconds() === 0) {
            fetchTimeframeInfo();
        }
    }, 1000);
});
</script>
@endpush 