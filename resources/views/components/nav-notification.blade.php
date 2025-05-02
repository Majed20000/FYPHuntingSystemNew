<div x-data="notificationData()" 
     x-init="init()"
     class="relative ml-3">
    <!-- Notification Bell -->
    <button 
        @click="isOpen = !isOpen" 
        class="relative p-1 text-gray-400 hover:text-gray-500 focus:outline-none"
        aria-label="Notifications"
    >
        <i class="fas fa-bell text-xl"></i>
        <!-- Notification Badge -->
        <div x-cloak 
             x-show="hasNotifications" 
             class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white">
        </div>
    </button>

    <!-- Dropdown -->
    <div x-cloak
         x-show="isOpen"
         @click.away="isOpen = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
    >
        <div class="p-4">
            <div x-show="notifications.length > 0">
                <template x-for="notification in notifications" :key="notification.id">
                    <div class="mb-4 last:mb-0">
                        <div class="flex items-start p-3 bg-white rounded-lg hover:bg-gray-50">
                            <div class="flex-shrink-0">
                                <i :class="['fas', notification.icon]"></i>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                <p class="mt-1 text-sm text-gray-500" x-text="notification.message"></p>
                                <template x-if="notification.action">
                                    <a :href="notification.action.url" 
                                       class="mt-2 inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                                        <span x-text="notification.action.text"></span>
                                    </a>
                                </template>
                            </div>
                            <button @click="removeNotification(notification.id)" 
                                    class="ml-4 text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
            <div x-show="notifications.length === 0" class="text-center py-4">
                <p class="text-gray-500">No new notifications</p>
            </div>
        </div>
    </div>

    <audio id="notificationSound" class="hidden">
        <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
    </audio>
</div>

@push('scripts')
<script>
function notificationData() {
    return {
        isOpen: false,
        notifications: [],
        hasNotifications: false,
        lastCheck: null,

        init() {
            console.log('Initializing notification component'); // Debug line
            this.lastCheck = localStorage.getItem('lastNotificationCheck');
            this.loadStoredNotifications();
            this.checkTimeframe();
            setInterval(() => this.checkTimeframe(), 30000);
        },

        loadStoredNotifications() {
            const stored = localStorage.getItem('notifications');
            if (stored) {
                this.notifications = JSON.parse(stored);
                this.hasNotifications = this.notifications.length > 0;
            }
        },

        saveNotifications() {
            localStorage.setItem('notifications', JSON.stringify(this.notifications));
        },

        async checkTimeframe() {
            try {
                const response = await fetch('{{ route('timeframe.active') }}');
                const data = await response.json();
                
                if (data.success && data.has_active) {
                    const timeframe = data.active_timeframe;
                    const now = new Date();
                    const startDate = new Date(timeframe.start_date);
                    const endDate = new Date(timeframe.end_date);
                    const proposalDeadline = new Date(timeframe.proposal_deadline);

                    // Check for new activation
                    if (timeframe.activation_timestamp) {
                        const activationTime = new Date(timeframe.activation_timestamp * 1000);
                        const lastCheck = this.lastCheck ? new Date(this.lastCheck) : new Date(0);
                        
                        if (activationTime > lastCheck) {
                            this.addNotification({
                                id: `activation-${timeframe.academic_year}-${timeframe.semester}-${activationTime.getTime()}`,
                                title: 'Timeframe Activated',
                                message: `A new timeframe for ${timeframe.academic_year} Semester ${timeframe.semester} has been activated!`,
                                type: 'info',
                                timestamp: new Date().toISOString(),
                                action: {
                                    text: 'View Details',
                                    url: '{{ auth()->user()->role === 'student' ? route('student.dashboard', ['user_id' => auth()->id()]) : route('lecturer.dashboard', ['user_id' => auth()->id()]) }}'
                                }
                            });
                        }
                    }

                    // Update last check time
                    this.lastCheck = new Date().toISOString();
                    localStorage.setItem('lastNotificationCheck', this.lastCheck);

                    // New Timeframe Activation Notification
                    const timeframeKey = `timeframe-${timeframe.academic_year}-${timeframe.semester}`;
                    const lastNotified = localStorage.getItem(timeframeKey);
                    
                    if (!lastNotified) {
                        this.addNotification({
                            id: timeframeKey,
                            title: 'New Academic Period Activated',
                            message: `A new timeframe for ${timeframe.academic_year} Semester ${timeframe.semester} has been activated.`,
                            type: 'info',
                            timestamp: new Date().toISOString()
                        });
                        localStorage.setItem(timeframeKey, new Date().toISOString());
                    }

                    // Supervisor Hunting Period Notification
                    if (now >= startDate && now <= endDate) {
                        this.addNotification({
                            id: 'supervisor-hunting',
                            title: 'Supervisor Hunting Period Active',
                            message: 'The supervisor hunting period has started. Don\'t miss your chance to secure your preferred supervisor!',
                            type: 'warning',
                            action: {
                                text: 'View Available Slots',
                                url: '{{ auth()->user()->role === 'student' ? route('student.view-slots', ['user_id' => auth()->id()]) : route('lecturer.calendar', ['user_id' => auth()->id()]) }}'
                            },
                            timestamp: new Date().toISOString()
                        });
                    }

                    // Deadline Approaching Notification (7 days before)
                    const daysToDeadline = Math.ceil((proposalDeadline - now) / (1000 * 60 * 60 * 24));
                    if (daysToDeadline <= 7 && daysToDeadline > 0) {
                        const deadlineKey = `deadline-${timeframeKey}`;
                        const lastDeadlineNotif = localStorage.getItem(deadlineKey);
                        
                        if (!lastDeadlineNotif) {
                            this.addNotification({
                                id: deadlineKey,
                                title: 'Deadline Approaching',
                                message: `Only ${daysToDeadline} days left until the proposal submission deadline!`,
                                type: 'urgent',
                                timestamp: new Date().toISOString()
                            });
                            localStorage.setItem(deadlineKey, new Date().toISOString());
                        }
                    }

                    // Add Coordinator End Date Reminder (7 days before end date)
                    @if(Auth::user()->isCoordinator())
                        const daysToEnd = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24));
                        if (daysToEnd <= 7 && daysToEnd > 0) {
                            const endReminderKey = `end-reminder-${timeframe.academic_year}-${timeframe.semester}`;
                            const lastEndReminder = localStorage.getItem(endReminderKey);
                            
                            if (!lastEndReminder) {
                                this.addNotification({
                                    id: endReminderKey,
                                    title: 'Supervisor Hunting Period Ending Soon',
                                    message: `The supervisor hunting period for ${timeframe.academic_year} Semester ${timeframe.semester} will end in ${daysToEnd} days. Please review all pending applications.`,
                                    type: 'warning',
                                    icon: 'fa-clock text-yellow-500',
                                    action: {
                                        text: 'Review Applications',
                                        url: '{{ route('coordinator.timeframes.index') }}'
                                    },
                                    timestamp: new Date().toISOString()
                                });
                                localStorage.setItem(endReminderKey, new Date().toISOString());

                                // Send browser notification for coordinator
                                if ("Notification" in window && Notification.permission === "granted") {
                                    new Notification('Supervisor Hunting Period Ending Soon', {
                                        body: `Only ${daysToEnd} days remaining in the current supervisor hunting period.`,
                                        icon: '/path/to/your/icon.png',
                                        tag: endReminderKey,
                                        requireInteraction: true
                                    });
                                }
                            }
                        }

                        // Daily reminders for the last 3 days
                        if (daysToEnd <= 3 && daysToEnd > 0) {
                            const dailyReminderKey = `daily-end-reminder-${timeframe.academic_year}-${timeframe.semester}-${now.toDateString()}`;
                            const lastDailyReminder = localStorage.getItem(dailyReminderKey);

                            if (!lastDailyReminder) {
                                this.addNotification({
                                    id: dailyReminderKey,
                                    title: 'Urgent: Period Ending Soon',
                                    message: `Only ${daysToEnd} day${daysToEnd > 1 ? 's' : ''} left in the current supervisor hunting period!`,
                                    type: 'urgent',
                                    icon: 'fa-exclamation-circle text-red-500',
                                    timestamp: new Date().toISOString()
                                });
                                localStorage.setItem(dailyReminderKey, new Date().toISOString());
                            }
                        }

                        // Final day reminder
                        if (daysToEnd === 0) {
                            const finalDayKey = `final-day-${timeframe.academic_year}-${timeframe.semester}`;
                            const lastFinalReminder = localStorage.getItem(finalDayKey);

                            if (!lastFinalReminder) {
                                this.addNotification({
                                    id: finalDayKey,
                                    title: 'Final Day',
                                    message: 'The supervisor hunting period ends today! Please ensure all necessary actions are completed.',
                                    type: 'urgent',
                                    icon: 'fa-bell text-red-500',
                                    action: {
                                        text: 'View Status',
                                        url: '{{ route('coordinator.timeframes.index') }}'
                                    },
                                    timestamp: new Date().toISOString()
                                });
                                localStorage.setItem(finalDayKey, new Date().toISOString());
                            }
                        }

                        
                    @endif

                    // Check for application status notifications
                    @if(Auth::user()->isStudent())
                        try {
                            const studentId = {{ Auth::id() }};
                            const storedNotifications = JSON.parse(localStorage.getItem('studentNotifications') || '{}');
                            
                            if (storedNotifications[studentId]) {
                                storedNotifications[studentId].forEach(notification => {
                                    if (!notification.read) {
                                        this.addNotification({
                                            id: notification.id,
                                            title: notification.title,
                                            message: notification.message,
                                            type: notification.type,
                                            timestamp: notification.timestamp,
                                            icon: this.getNotificationIcon(notification.type),
                                            action: {
                                                text: 'View Application',
                                                url: '{{ route('student.my-applications', ['user_id' => Auth::id()]) }}'
                                            }
                                        });
                                        
                                        // Play notification sound for new notifications
                                        this.playNotificationSound();
                                        
                                        // Mark as read
                                        notification.read = true;
                                    }
                                });
                                
                                // Save back updated read status
                                localStorage.setItem('studentNotifications', JSON.stringify(storedNotifications));
                                
                                // Update notification count
                                this.hasNotifications = this.notifications.length > 0;
                            }
                        } catch (error) {
                            console.error('Error checking application notifications:', error);
                        }
                    @endif
                }
            } catch (error) {
                console.error('Error checking timeframe:', error);
            }
        },

        addNotification(notification) {
            // Check if notification already exists
            const exists = this.notifications.some(n => n.id === notification.id);
            if (!exists) {
                this.notifications.unshift(notification);
                // Keep only the last 10 notifications
                this.notifications = this.notifications.slice(0, 10);
                this.hasNotifications = true;
            }
        },

        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
            this.hasNotifications = this.notifications.length > 0;
            this.saveNotifications();
        },

        playNotificationSound() {
            const audio = new Audio('/notification-sound.mp3'); // Make sure to add this file to your public directory
            audio.play().catch(error => console.log('Error playing sound:', error));
        },

        getNotificationIcon(type) {
            const icons = {
                'success': 'fa-check-circle text-green-500',
                'info': 'fa-info-circle text-blue-500',
                'warning': 'fa-exclamation-triangle text-yellow-500',
                'error': 'fa-exclamation-circle text-red-500',
                'urgent': 'fa-bell text-red-500'
            };
            return icons[type] || 'fa-bell text-blue-500';
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleString();
        }
    }
}
</script>
@endpush 