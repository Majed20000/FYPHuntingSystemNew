<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FYP Mentor System</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Additional Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

        <!-- AOS Animation -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                overflow-x: hidden;
            }
            .gradient-text {
                background: linear-gradient(45deg, #3B82F6, #8B5CF6);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .card-hover {
                transition: transform 0.3s ease-in-out;
            }
            .card-hover:hover {
                transform: translateY(-10px);
            }
            .main-content {
                min-height: 100vh;
                padding-top: 4rem; /* Adjust based on navbar height */
            }
            #particles-js {
                position: absolute;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                z-index: 1;
            }
        </style>
    </head>
    <body class="antialiased bg-gray-50">
        <!-- Navbar -->
        <nav class="fixed w-full bg-white/90 backdrop-blur-md shadow-sm z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <i class="fas fa-graduation-cap text-3xl text-blue-600"></i>
                            <span class="ml-2 text-xl font-bold gradient-text">FYP Mentor</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" 
                                   class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-2 rounded-full
                                          hover:shadow-lg transform hover:scale-105 transition duration-300">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-2 rounded-full
                                          hover:shadow-lg transform hover:scale-105 transition duration-300">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Hero Section with Particles -->
            <div class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 py-20">
                <div class="absolute inset-0 overflow-hidden">
                    <div id="particles-js"></div>
                </div>
                <div class="relative z-10 text-center px-4 w-full max-w-6xl mx-auto" data-aos="fade-up">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 text-white">Welcome to FYP Mentor System</h1>
                    <p class="text-xl mb-8 text-white/90">Connect with expert supervisors and exciting projects</p>
                    
                    <!-- Timeframe Information -->
                    <div id="timeframeCountdown" class="mt-8">
                        <div class="bg-white/10 backdrop-blur-md rounded-xl p-8">
                            <div id="countdownContent" class="space-y-6">
                                <div class="text-center animate-pulse text-white">
                                    <div class="inline-block">
                                        <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                    <p class="mt-2">Loading timeframe information...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Rest of your scripts -->
        <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
        
        <!-- Your existing scripts -->
        <script>
            // Initialize AOS
            AOS.init({
                duration: 1000,
                once: true
            });

            // Initialize Particles.js
            particlesJS('particles-js', {
                particles: {
                    number: { value: 80 },
                    color: { value: '#ffffff' },
                    opacity: { value: 0.5 },
                    size: { value: 3 },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: '#ffffff',
                        opacity: 0.4,
                        width: 1
                    },
                    move: {
                        enable: true,
                        speed: 2
                    }
                }
            });

            // Countdown Timer
            function updateCountdown() {
                const targetDate = new Date('March 3, 2025 00:00:00').getTime();
                const now = new Date().getTime();
                const timeLeft = targetDate - now;

                document.getElementById('days').textContent = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                document.getElementById('hours').textContent = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                document.getElementById('minutes').textContent = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                document.getElementById('seconds').textContent = Math.floor((timeLeft % (1000 * 60)) / 1000);
            }

            setInterval(updateCountdown, 1000);
            updateCountdown();
        </script>

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
                        <div class="text-center text-white">
                            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6">
                                <i class="fas fa-calendar-xmark text-4xl mb-4"></i>
                                <p class="text-lg font-semibold">No Active Period</p>
                                <p class="text-sm mt-2 text-white/80">Check back later for the next academic period</p>
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
                    <div class="bg-red-500/20 backdrop-blur-md rounded-lg p-6 mt-6">
                        <div class="text-center">
                            <i class="fas fa-exclamation-circle text-4xl text-red-400 mb-3"></i>
                            <h4 class="text-xl font-semibold text-white">Proposal Submission Closed</h4>
                            <p class="text-white/80 mt-2">The deadline has passed</p>
                        </div>
                    </div>
                ` : `
                    <div class="bg-emerald-500/20 backdrop-blur-md rounded-lg p-6 mt-6">
                        <h4 class="text-xl font-semibold text-white text-center mb-4">Proposal Submission Deadline</h4>
                        <div class="grid grid-cols-4 gap-4">
                            <div class="bg-white/10 rounded-lg p-4 backdrop-blur-md text-center">
                                <div class="text-3xl font-bold text-white">${days}</div>
                                <div class="text-xs uppercase tracking-wider text-white/70">Days</div>
                            </div>
                            <div class="bg-white/10 rounded-lg p-4 backdrop-blur-md text-center">
                                <div class="text-3xl font-bold text-white">${timeframe.hours_to_proposal}</div>
                                <div class="text-xs uppercase tracking-wider text-white/70">Hours</div>
                            </div>
                            <div class="bg-white/10 rounded-lg p-4 backdrop-blur-md text-center">
                                <div class="text-3xl font-bold text-white">${timeframe.minutes_to_proposal}</div>
                                <div class="text-xs uppercase tracking-wider text-white/70">Minutes</div>
                            </div>
                            <div class="bg-white/10 rounded-lg p-4 backdrop-blur-md text-center">
                                <div class="text-3xl font-bold text-white" id="seconds">00</div>
                                <div class="text-xs uppercase tracking-wider text-white/70">Seconds</div>
                            </div>
                        </div>
                        <div class="mt-6 text-center">
                            <p class="text-white/70 text-sm">
                                <i class="far fa-clock mr-2"></i>Deadline: ${proposalDeadline.toLocaleString('en-GB', { 
                                    day: 'numeric',
                                    month: 'long',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}
                                    </p>
                                </div>
                    </div>
                `;

                return `
                    <div class="bg-white/10 backdrop-blur-md rounded-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-white">Current Academic Period</h3>
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-100">
                                ${timeframe.academic_year} - Semester ${timeframe.semester}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white/10 backdrop-blur-md rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-3">Period Duration</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center text-white/80">
                                        <i class="fas fa-calendar-alt w-5 mr-2"></i>
                                        <span>Start: ${startDate.toLocaleDateString('en-GB', { 
                                            day: 'numeric', 
                                            month: 'long', 
                                            year: 'numeric'
                                        })}</span>
                                    </div>
                                    <div class="flex items-center text-white/80">
                                        <i class="fas fa-calendar-check w-5 mr-2"></i>
                                        <span>End: ${endDate.toLocaleDateString('en-GB', { 
                                            day: 'numeric', 
                                            month: 'long', 
                                            year: 'numeric'
                                        })}</span>
                                    </div>
                                </div>
                                </div>

                            <div class="bg-white/10 backdrop-blur-md rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-3">Important Limits</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center text-white/80">
                                        <i class="fas fa-file-alt w-5 mr-2"></i>
                                        <span>Max ${timeframe.max_applications_per_student} applications per student</span>
                                    </div>
                                    <div class="flex items-center text-white/80">
                                        <i class="fas fa-handshake w-5 mr-2"></i>
                                        <span>Max ${timeframe.max_appointments_per_student} appointments per student</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        ${countdownHtml}
                </div>
                `;
            }

            function getCountdownText(deadline) {
                const now = new Date();
                const diff = deadline - now;

                if (diff <= 0) {
                    return 'Deadline has passed';
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                return `${days}d ${hours}h ${minutes}m ${seconds}s remaining`;
            }

            function showNoTimeframe() {
                const content = document.getElementById('countdownContent');
                content.innerHTML = `
                    <div class="text-center text-gray-600 dark:text-gray-400">
                        <p class="text-lg">No active timeframe found</p>
                        <p class="text-sm mt-2">Please check back later</p>
            </div>
                `;
            }

            function showError() {
                const content = document.getElementById('countdownContent');
                content.innerHTML = `
                    <div class="text-center text-red-600 dark:text-red-400">
                        <p class="text-lg">Error loading timeframe information</p>
                        <p class="text-sm mt-2">Please try again later</p>
        </div>
                `;
            }

            // Add this function to update seconds in real-time
            function updateSeconds() {
                const secondsElement = document.getElementById('seconds');
                if (secondsElement) {
                    const now = new Date();
                    const seconds = 59 - now.getSeconds();
                    secondsElement.textContent = seconds.toString().padStart(2, '0');
                }
            }

            // Update the existing interval to include seconds
            setInterval(() => {
                updateSeconds();
                // Re-fetch timeframe data every minute to keep the display current
                if (new Date().getSeconds() === 0) {
                    fetchTimeframeInfo();
                }
            }, 1000);
        });
        </script>

        @push('styles')
        <style>
        /* Add these styles for smooth animations */
        .countdown-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .8;
            }
        }

        /* Improved backdrop blur */
        .backdrop-blur-md {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Gradient animation */
        .bg-gradient-animate {
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        </style>
        @endpush
    </body>
</html>
