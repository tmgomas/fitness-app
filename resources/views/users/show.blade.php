<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Profile Header Card - Modern Design -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 shadow-lg rounded-lg mb-6 overflow-hidden">
                <div class="relative h-40 sm:h-48 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/30 to-indigo-700/30"></div>
                    <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-blue-900/80 to-transparent">
                    </div>
                </div>

                <div class="px-6 py-4 relative -mt-20">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div
                            class="flex-shrink-0 h-24 w-24 rounded-full border-4 border-white shadow-lg bg-white overflow-hidden">
                            <img class="h-full w-full object-cover"
                                src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff"
                                alt="{{ $user->name }}">
                        </div>
                        <div class="flex-1 text-white">
                            <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                            <div class="flex flex-wrap mt-1 text-sm text-white/80 gap-3">
                                <span>{{ $user->email }}</span>
                                <span
                                    class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                    {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <span
                                    class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                    {{ $user->is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $user->is_admin ? 'Administrator' : 'User' }}
                                </span>
                            </div>
                        </div>
                        <div class="sm:self-start mt-4 sm:mt-0">
                            <a href="{{ route('users.edit', $user) }}"
                                class="bg-white text-indigo-600 py-2 px-4 rounded-md shadow-sm hover:bg-indigo-50 transition duration-200">
                                <i class="fas fa-edit mr-2"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Modern Tab Navigation -->
                <div x-data="{ activeTab: 'dashboard' }" class="bg-white border-b">
                    <div class="flex overflow-x-auto scrollbar-hide px-4">
                        <button @click="activeTab = 'dashboard'"
                            :class="{'text-indigo-600 border-indigo-600': activeTab === 'dashboard', 'border-transparent': activeTab !== 'dashboard'}"
                            class="px-4 py-3 text-sm font-medium border-b-2 hover:text-indigo-600 hover:border-indigo-600 transition duration-200">
                            <i class="fas fa-chart-line mr-2"></i> Dashboard
                        </button>
                        <button @click="activeTab = 'profile'"
                            :class="{'text-indigo-600 border-indigo-600': activeTab === 'profile', 'border-transparent': activeTab !== 'profile'}"
                            class="px-4 py-3 text-sm font-medium border-b-2 hover:text-indigo-600 hover:border-indigo-600 transition duration-200">
                            <i class="fas fa-user mr-2"></i> Basic Info
                        </button>
                        <button @click="activeTab = 'health'"
                            :class="{'text-indigo-600 border-indigo-600': activeTab === 'health', 'border-transparent': activeTab !== 'health'}"
                            class="px-4 py-3 text-sm font-medium border-b-2 hover:text-indigo-600 hover:border-indigo-600 transition duration-200">
                            <i class="fas fa-heartbeat mr-2"></i> Health
                        </button>
                        <button @click="activeTab = 'measurements'"
                            :class="{'text-indigo-600 border-indigo-600': activeTab === 'measurements', 'border-transparent': activeTab !== 'measurements'}"
                            class="px-4 py-3 text-sm font-medium border-b-2 hover:text-indigo-600 hover:border-indigo-600 transition duration-200">
                            <i class="fas fa-ruler mr-2"></i> Measurements
                        </button>
                        <button @click="activeTab = 'foodlogs'"
                            :class="{'text-indigo-600 border-indigo-600': activeTab === 'foodlogs', 'border-transparent': activeTab !== 'foodlogs'}"
                            class="px-4 py-3 text-sm font-medium border-b-2 hover:text-indigo-600 hover:border-indigo-600 transition duration-200">
                            <i class="fas fa-utensils mr-2"></i> Food Logs
                        </button>
                        <button @click="activeTab = 'exerciselogs'"
                            :class="{'text-indigo-600 border-indigo-600': activeTab === 'exerciselogs', 'border-transparent': activeTab !== 'exerciselogs'}"
                            class="px-4 py-3 text-sm font-medium border-b-2 hover:text-indigo-600 hover:border-indigo-600 transition duration-200">
                            <i class="fas fa-running mr-2"></i> Exercise Logs
                        </button>
                    </div>

                    <!-- Dashboard Tab - New Summary View -->
                    <div x-show="activeTab === 'dashboard'" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Health Summary Card -->
                            <div class="bg-white rounded-lg shadow-md p-5 border border-gray-100">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Health Summary</h3>
                                        <p class="text-sm text-gray-500">Latest health metrics</p>
                                    </div>
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Health</span>
                                </div>

                                @if(isset($healthData) && $healthData->count() > 0)
                                @php $latestHealth = $healthData->first(); @endphp
                                <div class="mt-4 space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Height</span>
                                        <span class="text-sm font-medium">{{ $latestHealth->height }} cm</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Weight</span>
                                        <span class="text-sm font-medium">{{ $latestHealth->weight }} kg</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">BMI</span>
                                        <span class="text-sm font-medium">{{ $latestHealth->bmi }}</span>
                                    </div>
                                    <div class="pt-2 text-right">
                                        <button @click="activeTab = 'health'"
                                            class="text-xs text-indigo-600 hover:underline">
                                            View details →
                                        </button>
                                    </div>
                                </div>
                                @else
                                <div class="mt-4 p-4 bg-gray-50 rounded-lg text-center">
                                    <p class="text-sm text-gray-500">No health data available</p>
                                </div>
                                @endif
                            </div>

                            <!-- Fitness Goals Card -->
                            <div class="bg-white rounded-lg shadow-md p-5 border border-gray-100">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Fitness Goals</h3>
                                        <p class="text-sm text-gray-500">Personal preferences</p>
                                    </div>
                                    <span
                                        class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Preferences</span>
                                </div>

                                @if(isset($preferences) && $preferences->count() > 0)
                                @php $latestPrefs = $preferences->first(); @endphp
                                <div class="mt-4 space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Fitness Goal</span>
                                        <span class="text-sm font-medium">{{ ucfirst(str_replace('_', ' ',
                                            $latestPrefs->fitness_goals ?? 'Not specified')) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Activity Level</span>
                                        <span class="text-sm font-medium">{{ ucfirst(str_replace('_', ' ',
                                            $latestPrefs->activity_level ?? 'Not specified')) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Diet Restrictions</span>
                                        <span class="text-sm font-medium">{{ $latestPrefs->dietary_restrictions ? 'Yes'
                                            : 'None' }}</span>
                                    </div>
                                    <div class="pt-2 text-right">
                                        <button @click="activeTab = 'preferences'"
                                            class="text-xs text-indigo-600 hover:underline">
                                            View details →
                                        </button>
                                    </div>
                                </div>
                                @else
                                <div class="mt-4 p-4 bg-gray-50 rounded-lg text-center">
                                    <p class="text-sm text-gray-500">No preferences set</p>
                                </div>
                                @endif
                            </div>

                            <!-- Activity Summary Card -->
                            <div class="bg-white rounded-lg shadow-md p-5 border border-gray-100">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                                        <p class="text-sm text-gray-500">Last 7 days summary</p>
                                    </div>
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Activity</span>
                                </div>

                                <div class="mt-4 space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Food Logs</span>
                                        <span class="text-sm font-medium">{{ isset($foodLogs) ? $foodLogs->count() : 0
                                            }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Exercise Logs</span>
                                        <span class="text-sm font-medium">{{ isset($exerciseLogs) ?
                                            $exerciseLogs->count() : 0 }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Last Active</span>
                                        <span class="text-sm font-medium">
                                            @php
                                            $lastActivity = null;
                                            if (isset($foodLogs) && $foodLogs->count() > 0) {
                                            $lastActivity = $foodLogs->first()->date;
                                            }
                                            if (isset($exerciseLogs) && $exerciseLogs->count() > 0) {
                                            $exerciseDate = $exerciseLogs->first()->start_time;
                                            if (!$lastActivity || $exerciseDate > $lastActivity) {
                                            $lastActivity = $exerciseDate;
                                            }
                                            }
                                            @endphp
                                            {{ $lastActivity ? $lastActivity->format('M j, Y') : 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="pt-2 flex justify-between">
                                        <button @click="activeTab = 'foodlogs'"
                                            class="text-xs text-indigo-600 hover:underline">
                                            Food logs →
                                        </button>
                                        <button @click="activeTab = 'exerciselogs'"
                                            class="text-xs text-indigo-600 hover:underline">
                                            Exercise logs →
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Row -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                            <!-- Exercise Activity Chart Card -->
                            <div class="bg-white rounded-lg shadow-md p-5 border border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Exercise Activity</h3>

                                @if(isset($exerciseLogs) && $exerciseLogs->count() > 0)
                                <!-- Exercise Chart -->
                                <div class="h-64">
                                    <canvas id="exerciseChart"></canvas>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                            const ctx = document.getElementById('exerciseChart').getContext('2d');
                                            
                                            // Process exercise data for chart
                                            const exerciseLogs = @json($exerciseLogs);
                                            
                                            // Group exercises by type
                                            const exerciseTypes = {};
                                            exerciseLogs.forEach(log => {
                                                const exerciseName = log.exercise ? log.exercise.name : 'Unknown';
                                                if (!exerciseTypes[exerciseName]) {
                                                    exerciseTypes[exerciseName] = {
                                                        count: 0,
                                                        totalDuration: 0,
                                                        totalCalories: 0
                                                    };
                                                }
                                                exerciseTypes[exerciseName].count += 1;
                                                exerciseTypes[exerciseName].totalDuration += log.duration_minutes;
                                                exerciseTypes[exerciseName].totalCalories += log.calories_burned;
                                            });
                                            
                                            // Prepare data for chart
                                            const labels = Object.keys(exerciseTypes);
                                            const durations = labels.map(label => exerciseTypes[label].totalDuration);
                                            const calories = labels.map(label => exerciseTypes[label].totalCalories);
                                            
                                            const chart = new Chart(ctx, {
                                                type: 'bar',
                                                data: {
                                                    labels: labels,
                                                    datasets: [
                                                        {
                                                            label: 'Duration (minutes)',
                                                            data: durations,
                                                            backgroundColor: 'rgba(99, 102, 241, 0.7)',
                                                            borderColor: 'rgba(99, 102, 241, 1)',
                                                            borderWidth: 1
                                                        },
                                                        {
                                                            label: 'Calories Burned',
                                                            data: calories,
                                                            backgroundColor: 'rgba(244, 114, 182, 0.7)',
                                                            borderColor: 'rgba(244, 114, 182, 1)',
                                                            borderWidth: 1,
                                                            yAxisID: 'y1'
                                                        }
                                                    ]
                                                },
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: false,
                                                    scales: {
                                                        y: {
                                                            beginAtZero: true,
                                                            title: {
                                                                display: true,
                                                                text: 'Duration (minutes)'
                                                            }
                                                        },
                                                        y1: {
                                                            beginAtZero: true,
                                                            position: 'right',
                                                            grid: {
                                                                drawOnChartArea: false
                                                            },
                                                            title: {
                                                                display: true,
                                                                text: 'Calories Burned'
                                                            }
                                                        }
                                                    }
                                                }
                                            });
                                        });
                                </script>
                                @else
                                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-500">No exercise data available</p>
                                </div>
                                @endif
                            </div>

                            <!-- Nutrition Summary Chart Card -->
                            <div class="bg-white rounded-lg shadow-md p-5 border border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Nutrition Summary</h3>

                                @if(isset($foodLogs) && $foodLogs->count() > 0)
                                <!-- Food Chart -->
                                <div class="h-64">
                                    <canvas id="nutritionChart"></canvas>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                            const ctx = document.getElementById('nutritionChart').getContext('2d');
                                            
                                            // Process food log data for chart
                                            const foodLogs = @json($foodLogs);
                                            
                                            // Group food logs by meal type
                                            const mealTypes = {
                                                'breakfast': 0,
                                                'lunch': 0,
                                                'dinner': 0,
                                                'snack': 0
                                            };
                                            
                                            foodLogs.forEach(log => {
                                                let calories = 0;
                                                if (log.foodItem && log.foodItem.foodNutrition) {
                                                    log.foodItem.foodNutrition.forEach(nutrition => {
                                                        if (nutrition.nutritionType && 
                                                            (nutrition.nutritionType.name.toLowerCase() === 'calories' || 
                                                             nutrition.nutritionType.name.toLowerCase().includes('calorie'))) {
                                                            const servingRatio = log.serving_size / 100;
                                                            calories = nutrition.amount_per_100g * servingRatio;
                                                        }
                                                    });
                                                }
                                                
                                                const mealType = log.meal_type.toLowerCase();
                                                if (mealTypes.hasOwnProperty(mealType)) {
                                                    mealTypes[mealType] += calories;
                                                }
                                            });
                                            
                                            // Prepare data for chart
                                            const labels = Object.keys(mealTypes).map(type => type.charAt(0).toUpperCase() + type.slice(1));
                                            const calorieData = Object.values(mealTypes);
                                            
                                            const chart = new Chart(ctx, {
                                                type: 'doughnut',
                                                data: {
                                                    labels: labels,
                                                    datasets: [{
                                                        data: calorieData,
                                                        backgroundColor: [
                                                            'rgba(255, 159, 64, 0.7)',   // Breakfast
                                                            'rgba(255, 99, 132, 0.7)',   // Lunch
                                                            'rgba(54, 162, 235, 0.7)',   // Dinner
                                                            'rgba(75, 192, 192, 0.7)'    // Snack
                                                        ],
                                                        borderColor: [
                                                            'rgba(255, 159, 64, 1)',
                                                            'rgba(255, 99, 132, 1)',
                                                            'rgba(54, 162, 235, 1)',
                                                            'rgba(75, 192, 192, 1)'
                                                        ],
                                                        borderWidth: 1
                                                    }]
                                                },
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: false,
                                                    plugins: {
                                                        title: {
                                                            display: true,
                                                            text: 'Calorie Distribution by Meal Type'
                                                        },
                                                        legend: {
                                                            position: 'bottom'
                                                        }
                                                    }
                                                }
                                            });
                                        });
                                </script>
                                @else
                                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-500">No nutrition data available</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Body Measurements Trend -->
                        <div class="bg-white rounded-lg shadow-md p-5 border border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Body Measurements Trend</h3>

                            @if(isset($measurements) && $measurements->count() > 1)
                            <!-- Measurements Chart -->
                            <div class="h-64">
                                <canvas id="measurementsChart"></canvas>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                        const ctx = document.getElementById('measurementsChart').getContext('2d');
                                        
                                        // Process measurements data for chart
                                        const measurements = @json($measurements);
                                        
                                        // Prepare data for chart
                                        const dates = measurements.map(m => m.recorded_at);
                                        const waistData = measurements.map(m => m.waist);
                                        const chestData = measurements.map(m => m.chest);
                                        const hipsData = measurements.map(m => m.hips);
                                        
                                        // Format dates for display
                                        const formattedDates = dates.map(date => {
                                            const d = new Date(date);
                                            return `${d.getFullYear()}-${(d.getMonth()+1).toString().padStart(2, '0')}-${d.getDate().toString().padStart(2, '0')}`;
                                        });
                                        
                                        const chart = new Chart(ctx, {
                                            type: 'line',
                                            data: {
                                                labels: formattedDates.reverse(), // Show earliest date first
                                                datasets: [
                                                    {
                                                        label: 'Waist (cm)',
                                                        data: waistData.reverse(),
                                                        borderColor: 'rgba(99, 102, 241, 1)',
                                                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                                        tension: 0.3,
                                                        fill: false
                                                    },
                                                    {
                                                        label: 'Chest (cm)',
                                                        data: chestData.reverse(),
                                                        borderColor: 'rgba(220, 38, 38, 1)',
                                                        backgroundColor: 'rgba(220, 38, 38, 0.1)',
                                                        tension: 0.3,
                                                        fill: false
                                                    },
                                                    {
                                                        label: 'Hips (cm)',
                                                        data: hipsData.reverse(),
                                                        borderColor: 'rgba(245, 158, 11, 1)',
                                                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                                        tension: 0.3,
                                                        fill: false
                                                    }
                                                ]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                plugins: {
                                                    title: {
                                                        display: true,
                                                        text: 'Measurement Changes Over Time'
                                                    }
                                                },
                                                scales: {
                                                    y: {
                                                        title: {
                                                            display: true,
                                                            text: 'Measurement (cm)'
                                                        }
                                                    },
                                                    x: {
                                                        title: {
                                                            display: true,
                                                            text: 'Date'
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    });
                            </script>
                            @elseif(isset($measurements) && $measurements->count() == 1)
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-500">Not enough data to show trends (minimum 2 measurements
                                    required)</p>
                            </div>
                            @else
                            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-500">No measurement data available</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Profile Tab -->
                    <div x-show="activeTab === 'profile'" class="p-6">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>

                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Username</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->username ?? 'Not set' }}</dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Gender</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($user->gender ?? 'Not
                                            specified') }}</dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Birthday</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $user->birthday ? $user->birthday->format('F j, Y') : 'Not specified' }}
                                        </dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                                        <dd class="mt-1">
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                                {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Role</dt>
                                        <dd class="mt-1">
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                                {{ $user->is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $user->is_admin ? 'Administrator' : 'User' }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $user->created_at->format('F j, Y') }}
                                        </dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $user->updated_at->format('F j, Y') }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Health Data Tab -->
                    <div x-show="activeTab === 'health'" class="p-6">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Health Data History</h3>

                                    @if(isset($healthData) && $healthData->count() > 0)
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        Last Updated: {{ $healthData->first()->recorded_at->format('M j, Y') }}
                                    </span>
                                    @endif
                                </div>

                                @if(isset($healthData) && $healthData->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 rounded-lg">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Date Recorded
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Height (cm)
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Weight (kg)
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    BMI
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Blood Type
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Medical Conditions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($healthData as $data)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $data->recorded_at->format('M j, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $data->height }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $data->weight }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <span
                                                        class="px-2 py-1 text-xs font-medium rounded-full 
                                                                {{ $data->bmi < 18.5 ? 'bg-blue-100 text-blue-800' : 
                                                                  ($data->bmi < 25 ? 'bg-green-100 text-green-800' : 
                                                                   ($data->bmi < 30 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                                        {{ number_format($data->bmi, 1) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $data->blood_type }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    {{ $data->medical_conditions ?? 'None' }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- BMI Chart Card -->
                                <div class="mt-8">
                                    <h4 class="text-md font-medium text-gray-900 mb-4">BMI Trend</h4>
                                    <div class="h-64">
                                        <canvas id="bmiChart"></canvas>
                                    </div>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                                const ctx = document.getElementById('bmiChart').getContext('2d');
                                                
                                                // Process health data for chart
                                                const healthData = @json($healthData);
                                                
                                                // Prepare data for chart
                                                const dates = healthData.map(h => h.recorded_at);
                                                const bmiData = healthData.map(h => h.bmi);
                                                
                                                // Format dates for display
                                                const formattedDates = dates.map(date => {
                                                    const d = new Date(date);
                                                    return `${d.getFullYear()}-${(d.getMonth()+1).toString().padStart(2, '0')}-${d.getDate().toString().padStart(2, '0')}`;
                                                });
                                                
                                                // Reverse arrays to show earliest date first
                                                const reversedDates = [...formattedDates].reverse();
                                                const reversedBMI = [...bmiData].reverse();
                                                
                                                const chart = new Chart(ctx, {
                                                    type: 'line',
                                                    data: {
                                                        labels: reversedDates,
                                                        datasets: [{
                                                            label: 'BMI',
                                                            data: reversedBMI,
                                                            borderColor: 'rgba(79, 70, 229, 1)',
                                                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                                            tension: 0.3,
                                                            fill: true
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false,
                                                        scales: {
                                                            y: {
                                                                beginAtZero: false,
                                                                title: {
                                                                    display: true,
                                                                    text: 'BMI Value'
                                                                }
                                                            },
                                                            x: {
                                                                title: {
                                                                    display: true,
                                                                    text: 'Date'
                                                                }
                                                            }
                                                        },
                                                        plugins: {
                                                            annotation: {
                                                                annotations: {
                                                                    underweightLine: {
                                                                        type: 'line',
                                                                        yMin: 18.5,
                                                                        yMax: 18.5,
                                                                        borderColor: 'rgba(147, 197, 253, 0.7)',
                                                                        borderWidth: 2,
                                                                        label: {
                                                                            content: 'Underweight',
                                                                            enabled: true,
                                                                            position: 'end'
                                                                        }
                                                                    },
                                                                    normalLine: {
                                                                        type: 'line',
                                                                        yMin: 25,
                                                                        yMax: 25,
                                                                        borderColor: 'rgba(110, 231, 183, 0.7)',
                                                                        borderWidth: 2,
                                                                        label: {
                                                                            content: 'Normal',
                                                                            enabled: true,
                                                                            position: 'end'
                                                                        }
                                                                    },
                                                                    overweightLine: {
                                                                        type: 'line',
                                                                        yMin: 30,
                                                                        yMax: 30,
                                                                        borderColor: 'rgba(251, 191, 36, 0.7)',
                                                                        borderWidth: 2,
                                                                        label: {
                                                                            content: 'Overweight',
                                                                            enabled: true,
                                                                            position: 'end'
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            });
                                    </script>
                                </div>
                                @else
                                <div class="text-center py-8 bg-gray-50 rounded-lg">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                        </path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No health data available for this user.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Preferences Tab -->
                    <div x-show="activeTab === 'preferences'" class="p-6">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">User Preferences</h3>

                                    @if(isset($preferences) && $preferences->count() > 0)
                                    <span
                                        class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        Last Updated: {{ $preferences->first()->updated_at->format('M j, Y') }}
                                    </span>
                                    @endif
                                </div>

                                @if(isset($preferences) && $preferences->count() > 0)
                                @php $pref = $preferences->first(); @endphp

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-indigo-50 p-5 rounded-lg border border-indigo-100">
                                        <h4 class="font-medium text-indigo-800 mb-3">Fitness Goals</h4>
                                        <div class="text-gray-700">
                                            {{ ucfirst(str_replace('_', ' ', $pref->fitness_goals ?? 'Not specified'))
                                            }}
                                        </div>

                                        <div class="mt-4">
                                            <h5 class="text-xs font-medium text-indigo-600 uppercase mb-2">
                                                Recommendations</h5>
                                            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                                @if($pref->fitness_goals == 'lose_weight')
                                                <li>Focus on calorie deficit of 500-700 calories per day</li>
                                                <li>Aim for 150+ minutes of moderate cardio per week</li>
                                                <li>Include 2-3 days of strength training</li>
                                                @elseif($pref->fitness_goals == 'gain_weight' || $pref->fitness_goals ==
                                                'build_muscle')
                                                <li>Aim for calorie surplus of 300-500 calories per day</li>
                                                <li>Focus on protein intake (1.6-2.2g per kg of body weight)</li>
                                                <li>Prioritize strength training 4-5 days per week</li>
                                                @elseif($pref->fitness_goals == 'maintain_weight')
                                                <li>Balance calories in with calories out</li>
                                                <li>Mix of cardio and strength training</li>
                                                <li>Focus on consistency in your routine</li>
                                                @else
                                                <li>Set specific, measurable fitness goals</li>
                                                <li>Consult with a fitness professional</li>
                                                <li>Track your progress regularly</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="bg-green-50 p-5 rounded-lg border border-green-100">
                                        <h4 class="font-medium text-green-800 mb-3">Activity Level</h4>
                                        <div class="text-gray-700">
                                            {{ ucfirst(str_replace('_', ' ', $pref->activity_level ?? 'Not specified'))
                                            }}
                                        </div>

                                        <div class="mt-4">
                                            <h5 class="text-xs font-medium text-green-600 uppercase mb-2">Daily Calorie
                                                Adjustment</h5>
                                            <div class="flex items-center mb-2">
                                                @php
                                                $multiplier = 1.2; // Default: Sedentary

                                                if (!empty($pref->activity_level)) {
                                                switch ($pref->activity_level) {
                                                case 'sedentary':
                                                $multiplier = 1.2;
                                                break;
                                                case 'lightly_active':
                                                $multiplier = 1.375;
                                                break;
                                                case 'moderately_active':
                                                $multiplier = 1.55;
                                                break;
                                                case 'very_active':
                                                $multiplier = 1.725;
                                                break;
                                                case 'extra_active':
                                                $multiplier = 1.9;
                                                break;
                                                }
                                                }

                                                $percentage = ($multiplier - 1) * 100;
                                                @endphp

                                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                    <div class="bg-green-600 h-2.5 rounded-full"
                                                        style="width: {{ min(100, $percentage * 1.4) }}%"></div>
                                                </div>
                                                <span class="ml-3 text-sm text-gray-700">+{{ number_format($percentage,
                                                    0) }}%</span>
                                            </div>
                                            <p class="text-sm text-gray-600">
                                                Based on your activity level, your daily calorie needs are multiplied by
                                                approximately {{ number_format($multiplier, 2) }}x
                                            </p>
                                        </div>
                                    </div>

                                    <div class="bg-red-50 p-5 rounded-lg border border-red-100">
                                        <h4 class="font-medium text-red-800 mb-3">Allergies</h4>
                                        @if($pref->allergies)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(explode(',', $pref->allergies) as $allergy)
                                            <span
                                                class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                                {{ trim($allergy) }}
                                            </span>
                                            @endforeach
                                        </div>
                                        @else
                                        <p class="text-gray-500">No allergies specified</p>
                                        @endif
                                    </div>

                                    <div class="bg-yellow-50 p-5 rounded-lg border border-yellow-100">
                                        <h4 class="font-medium text-yellow-800 mb-3">Dietary Restrictions</h4>
                                        @if($pref->dietary_restrictions)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(explode(',', $pref->dietary_restrictions) as $restriction)
                                            <span
                                                class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                                {{ trim($restriction) }}
                                            </span>
                                            @endforeach
                                        </div>
                                        @else
                                        <p class="text-gray-500">No dietary restrictions specified</p>
                                        @endif
                                    </div>
                                </div>

                                @if($pref->disliked_foods)
                                <div class="mt-6 bg-gray-50 p-5 rounded-lg border border-gray-100">
                                    <h4 class="font-medium text-gray-800 mb-3">Disliked Foods</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(explode(',', $pref->disliked_foods) as $food)
                                        <span
                                            class="px-2 py-1 bg-gray-200 text-gray-800 text-xs font-medium rounded-full">
                                            {{ trim($food) }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                @else
                                <div class="text-center py-8 bg-gray-50 rounded-lg">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No preference data available for this user.
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Measurements Tab -->
                    <div x-show="activeTab === 'measurements'" class="p-6">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Body Measurements</h3>

                                    @if(isset($measurements) && $measurements->count() > 0)
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        Last Updated: {{ $measurements->first()->recorded_at->format('M j, Y') }}
                                    </span>
                                    @endif
                                </div>

                                @if(isset($measurements) && $measurements->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 rounded-lg">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Date Recorded
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Chest (cm)
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Waist (cm)
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Hips (cm)
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Arms (cm)
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Thighs (cm)
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($measurements as $measurement)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $measurement->recorded_at->format('M j, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $measurement->chest ?? '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $measurement->waist ?? '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $measurement->hips ?? '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $measurement->arms ?? '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $measurement->thighs ?? '-' }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Measurements Comparison Chart -->
                                <div class="mt-8">
                                    @if($measurements->count() > 1)
                                    <h4 class="text-md font-medium text-gray-900 mb-4">Measurement Changes</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="h-64">
                                            <canvas id="measurementsComparisonChart"></canvas>
                                        </div>

                                        @php
                                        $latest = $measurements->first();
                                        $previous = $measurements->count() > 1 ? $measurements[1] : null;
                                        @endphp

                                        @if($latest && $previous)
                                        <div class="bg-gray-50 p-5 rounded-lg">
                                            <h5 class="text-sm font-medium text-gray-700 mb-3">Changes Since Last
                                                Measurement</h5>
                                            <div class="space-y-4">
                                                @if($latest->chest && $previous->chest)
                                                @php $chestDiff = $latest->chest - $previous->chest; @endphp
                                                <div>
                                                    <div class="flex justify-between mb-1">
                                                        <span class="text-xs font-medium text-gray-500">Chest</span>
                                                        <span
                                                            class="text-xs font-medium {{ $chestDiff < 0 ? 'text-green-600' : ($chestDiff > 0 ? 'text-red-600' : 'text-gray-500') }}">
                                                            {{ $chestDiff === 0 ? 'No change' : ($chestDiff > 0 ? '+' .
                                                            number_format($chestDiff, 1) : number_format($chestDiff, 1))
                                                            }} cm
                                                        </span>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($latest->waist && $previous->waist)
                                                @php $waistDiff = $latest->waist - $previous->waist; @endphp
                                                <div>
                                                    <div class="flex justify-between mb-1">
                                                        <span class="text-xs font-medium text-gray-500">Waist</span>
                                                        <span
                                                            class="text-xs font-medium {{ $waistDiff < 0 ? 'text-green-600' : ($waistDiff > 0 ? 'text-red-600' : 'text-gray-500') }}">
                                                            {{ $waistDiff === 0 ? 'No change' : ($waistDiff > 0 ? '+' .
                                                            number_format($waistDiff, 1) : number_format($waistDiff, 1))
                                                            }} cm
                                                        </span>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($latest->hips && $previous->hips)
                                                @php $hipsDiff = $latest->hips - $previous->hips; @endphp
                                                <div>
                                                    <div class="flex justify-between mb-1">
                                                        <span class="text-xs font-medium text-gray-500">Hips</span>
                                                        <span
                                                            class="text-xs font-medium {{ $hipsDiff < 0 ? 'text-green-600' : ($hipsDiff > 0 ? 'text-red-600' : 'text-gray-500') }}">
                                                            {{ $hipsDiff === 0 ? 'No change' : ($hipsDiff > 0 ? '+' .
                                                            number_format($hipsDiff, 1) : number_format($hipsDiff, 1))
                                                            }} cm
                                                        </span>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($latest->arms && $previous->arms)
                                                @php $armsDiff = $latest->arms - $previous->arms; @endphp
                                                <div>
                                                    <div class="flex justify-between mb-1">
                                                        <span class="text-xs font-medium text-gray-500">Arms</span>
                                                        <span
                                                            class="text-xs font-medium {{ $armsDiff < 0 ? 'text-green-600' : ($armsDiff > 0 ? 'text-red-600' : 'text-gray-500') }}">
                                                            {{ $armsDiff === 0 ? 'No change' : ($armsDiff > 0 ? '+' .
                                                            number_format($armsDiff, 1) : number_format($armsDiff, 1))
                                                            }} cm
                                                        </span>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($latest->thighs && $previous->thighs)
                                                @php $thighsDiff = $latest->thighs - $previous->thighs; @endphp
                                                <div>
                                                    <div class="flex justify-between mb-1">
                                                        <span class="text-xs font-medium text-gray-500">Thighs</span>
                                                        <span
                                                            class="text-xs font-medium {{ $thighsDiff < 0 ? 'text-green-600' : ($thighsDiff > 0 ? 'text-red-600' : 'text-gray-500') }}">
                                                            {{ $thighsDiff === 0 ? 'No change' : ($thighsDiff > 0 ? '+'
                                                            . number_format($thighsDiff, 1) : number_format($thighsDiff,
                                                            1)) }} cm
                                                        </span>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                                    const ctx = document.getElementById('measurementsComparisonChart').getContext('2d');
                                                    
                                                    // Process measurements data for chart
                                                    const measurements = @json($measurements);
                                                    
                                                    // Get the latest two measurements
                                                    const latest = measurements[0];
                                                    const previous = measurements.length > 1 ? measurements[1] : null;
                                                    
                                                    if (latest && previous) {
                                                        const chart = new Chart(ctx, {
                                                            type: 'radar',
                                                            data: {
                                                                labels: ['Chest', 'Waist', 'Hips', 'Arms', 'Thighs'],
                                                                datasets: [
                                                                    {
                                                                        label: 'Latest (' + new Date(latest.recorded_at).toLocaleDateString() + ')',
                                                                        data: [
                                                                            latest.chest || 0,
                                                                            latest.waist || 0,
                                                                            latest.hips || 0,
                                                                            latest.arms || 0,
                                                                            latest.thighs || 0
                                                                        ],
                                                                        backgroundColor: 'rgba(99, 102, 241, 0.2)',
                                                                        borderColor: 'rgba(99, 102, 241, 1)',
                                                                        borderWidth: 2,
                                                                        pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                                                                        pointRadius: 4
                                                                    },
                                                                    {
                                                                        label: 'Previous (' + new Date(previous.recorded_at).toLocaleDateString() + ')',
                                                                        data: [
                                                                            previous.chest || 0,
                                                                            previous.waist || 0,
                                                                            previous.hips || 0,
                                                                            previous.arms || 0,
                                                                            previous.thighs || 0
                                                                        ],
                                                                        backgroundColor: 'rgba(209, 213, 219, 0.2)',
                                                                        borderColor: 'rgba(107, 114, 128, 1)',
                                                                        borderWidth: 2,
                                                                        pointBackgroundColor: 'rgba(107, 114, 128, 1)',
                                                                        pointRadius: 4
                                                                    }
                                                                ]
                                                            },
                                                            options: {
                                                                responsive: true,
                                                                maintainAspectRatio: false,
                                                                scales: {
                                                                    r: {
                                                                        beginAtZero: false,
                                                                        min: 0
                                                                    }
                                                                },
                                                                plugins: {
                                                                    title: {
                                                                        display: true,
                                                                        text: 'Measurements Comparison (in cm)'
                                                                    }
                                                                }
                                                            }
                                                        });
                                                    }
                                                });
                                    </script>
                                    @endif
                                </div>
                                @else
                                <div class="text-center py-8 bg-gray-50 rounded-lg">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No measurement data available for this user.
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Food Logs Tab -->
                    <div x-show="activeTab === 'foodlogs'" class="p-6">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Food Tracking History</h3>

                                    @if(isset($foodLogs) && $foodLogs->count() > 0)
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $foodLogs->count() }} Entries
                                    </span>
                                    @endif
                                </div>

                                @if(isset($foodLogs) && $foodLogs->count() > 0)
                                <!-- Meal Type Distribution Chart -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                    <div class="md:col-span-1 bg-green-50 rounded-lg p-4">
                                        <h4 class="text-md font-medium text-green-800 mb-4">Meal Distribution</h4>
                                        <div class="h-56">
                                            <canvas id="mealTypeChart"></canvas>
                                        </div>

                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                    const ctx = document.getElementById('mealTypeChart').getContext('2d');
                                                    
                                                    // Process food log data for chart
                                                    const foodLogs = @json($foodLogs);
                                                    
                                                    // Count logs by meal type
                                                    const mealTypeCount = {
                                                        'breakfast': 0,
                                                        'lunch': 0,
                                                        'dinner': 0,
                                                        'snack': 0
                                                    };
                                                    
                                                    foodLogs.forEach(log => {
                                                        const mealType = log.meal_type.toLowerCase();
                                                        if (mealTypeCount.hasOwnProperty(mealType)) {
                                                            mealTypeCount[mealType]++;
                                                        }
                                                    });
                                                    
                                                    const chart = new Chart(ctx, {
                                                        type: 'pie',
                                                        data: {
                                                            labels: ['Breakfast', 'Lunch', 'Dinner', 'Snack'],
                                                            datasets: [{
                                                                data: [
                                                                    mealTypeCount.breakfast,
                                                                    mealTypeCount.lunch,
                                                                    mealTypeCount.dinner,
                                                                    mealTypeCount.snack
                                                                ],
                                                                backgroundColor: [
                                                                    'rgba(255, 159, 64, 0.7)',   // Breakfast
                                                                    'rgba(255, 99, 132, 0.7)',   // Lunch
                                                                    'rgba(54, 162, 235, 0.7)',   // Dinner
                                                                    'rgba(75, 192, 192, 0.7)'    // Snack
                                                                ],
                                                                borderColor: [
                                                                    'rgba(255, 159, 64, 1)',
                                                                    'rgba(255, 99, 132, 1)',
                                                                    'rgba(54, 162, 235, 1)',
                                                                    'rgba(75, 192, 192, 1)'
                                                                ],
                                                                borderWidth: 1
                                                            }]
                                                        },
                                                        options: {
                                                            responsive: true,
                                                            maintainAspectRatio: false,
                                                            plugins: {
                                                                legend: {
                                                                    position: 'bottom'
                                                                }
                                                            }
                                                        }
                                                    });
                                                });
                                        </script>
                                    </div>

                                    <div class="md:col-span-2">
                                        <h4 class="text-md font-medium text-gray-800 mb-4">Recent Food Entries</h4>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 rounded-lg">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col"
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Date
                                                        </th>
                                                        <th scope="col"
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Meal
                                                        </th>
                                                        <th scope="col"
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Food Item
                                                        </th>
                                                        <th scope="col"
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Serving
                                                        </th>
                                                        <th scope="col"
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Calories
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach($foodLogs as $log)
                                                    <tr>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $log->date->format('M j, Y') }}
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap">
                                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                                        {{ $log->meal_type == 'breakfast' ? 'bg-orange-100 text-orange-800' : 
                                                                           ($log->meal_type == 'lunch' ? 'bg-pink-100 text-pink-800' : 
                                                                            ($log->meal_type == 'dinner' ? 'bg-blue-100 text-blue-800' : 
                                                                             'bg-teal-100 text-teal-800')) }}">
                                                                {{ ucfirst($log->meal_type) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                            {{ $log->foodItem->name ?? 'Unknown Food' }}
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                            {{ $log->serving_size }} {{ $log->serving_unit }}
                                                        </td>
                                                        <td
                                                            class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            @php
                                                            $calories = 0;
                                                            if ($log->foodItem && $log->foodItem->foodNutrition) {
                                                            foreach ($log->foodItem->foodNutrition as $nutrition) {
                                                            if (strtolower($nutrition->nutritionType->name) ===
                                                            'calories' ||
                                                            strpos(strtolower($nutrition->nutritionType->name),
                                                            'calorie') !== false) {
                                                            $servingRatio = $log->serving_size / 100;
                                                            $calories = $nutrition->amount_per_100g * $servingRatio;
                                                            break;
                                                            }
                                                            }
                                                            }
                                                            @endphp
                                                            {{ round($calories, 1) }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Calorie Intake Timeline -->
                                <div class="mt-8">
                                    <h4 class="text-md font-medium text-gray-800 mb-4">Calorie Intake Timeline</h4>
                                    <div class="h-64">
                                        <canvas id="calorieTimelineChart"></canvas>
                                    </div>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                                const ctx = document.getElementById('calorieTimelineChart').getContext('2d');
                                                
                                                // Process food log data for chart
                                                const foodLogs = @json($foodLogs);
                                                
                                                // Group by date and calculate total calories
                                                const caloriesByDate = {};
                                                
                                                foodLogs.forEach(log => {
                                                    const dateStr = new Date(log.date).toISOString().split('T')[0];
                                                    
                                                    if (!caloriesByDate[dateStr]) {
                                                        caloriesByDate[dateStr] = { total: 0, meals: {} };
                                                    }
                                                    
                                                    // Calculate calories for this log
                                                    let calories = 0;
                                                    if (log.foodItem && log.foodItem.foodNutrition) {
                                                        log.foodItem.foodNutrition.forEach(nutrition => {
                                                            if (nutrition.nutritionType && 
                                                                (nutrition.nutritionType.name.toLowerCase() === 'calories' || 
                                                                 nutrition.nutritionType.name.toLowerCase().includes('calorie'))) {
                                                                const servingRatio = log.serving_size / 100;
                                                                calories = nutrition.amount_per_100g * servingRatio;
                                                            }
                                                        });
                                                    }
                                                    
                                                    // Add to total calories for this date
                                                    caloriesByDate[dateStr].total += calories;
                                                    
                                                    // Group by meal type
                                                    const mealType = log.meal_type.toLowerCase();
                                                    if (!caloriesByDate[dateStr].meals[mealType]) {
                                                        caloriesByDate[dateStr].meals[mealType] = 0;
                                                    }
                                                    caloriesByDate[dateStr].meals[mealType] += calories;
                                                });
                                                
                                                // Sort dates
                                                const sortedDates = Object.keys(caloriesByDate).sort();
                                                
                                                // Prepare data for stacked bar chart
                                                const labels = sortedDates.map(date => {
                                                    const d = new Date(date);
                                                    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                                                });
                                                
                                                const breakfastData = sortedDates.map(date => 
                                                    caloriesByDate[date].meals.breakfast || 0
                                                );
                                                
                                                const lunchData = sortedDates.map(date => 
                                                    caloriesByDate[date].meals.lunch || 0
                                                );
                                                
                                                const dinnerData = sortedDates.map(date => 
                                                    caloriesByDate[date].meals.dinner || 0
                                                );
                                                
                                                const snackData = sortedDates.map(date => 
                                                    caloriesByDate[date].meals.snack || 0
                                                );
                                                
                                                const chart = new Chart(ctx, {
                                                    type: 'bar',
                                                    data: {
                                                        labels: labels,
                                                        datasets: [
                                                            {
                                                                label: 'Breakfast',
                                                                data: breakfastData,
                                                                backgroundColor: 'rgba(255, 159, 64, 0.7)',
                                                                borderColor: 'rgba(255, 159, 64, 1)',
                                                                borderWidth: 1
                                                            },
                                                            {
                                                                label: 'Lunch',
                                                                data: lunchData,
                                                                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                                                borderColor: 'rgba(255, 99, 132, 1)',
                                                                borderWidth: 1
                                                            },
                                                            {
                                                                label: 'Dinner',
                                                                data: dinnerData,
                                                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                                                borderColor: 'rgba(54, 162, 235, 1)',
                                                                borderWidth: 1
                                                            },
                                                            {
                                                                label: 'Snack',
                                                                data: snackData,
                                                                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                                                borderColor: 'rgba(75, 192, 192, 1)',
                                                                borderWidth: 1
                                                            }
                                                        ]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false,
                                                        scales: {
                                                            x: {
                                                                stacked: true,
                                                                title: {
                                                                    display: true,
                                                                    text: 'Date'
                                                                }
                                                            },
                                                            y: {
                                                                stacked: true,
                                                                beginAtZero: true,
                                                                title: {
                                                                    display: true,
                                                                    text: 'Calories'
                                                                }
                                                            }
                                                        },
                                                        plugins: {
                                                            title: {
                                                                display: true,
                                                                text: 'Daily Calorie Intake by Meal Type'
                                                            }
                                                        }
                                                    }
                                                });
                                            });
                                    </script>
                                </div>
                                @else
                                <div class="text-center py-8 bg-gray-50 rounded-lg">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No food logs available for this user.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Exercise Logs Tab -->
                    <div x-show="activeTab === 'exerciselogs'" class="p-6">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Exercise Tracking History</h3>

                                    @if(isset($exerciseLogs) && $exerciseLogs->count() > 0)
                                    <span
                                        class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $exerciseLogs->count() }} Entries
                                    </span>
                                    @endif
                                </div>

                                @if(isset($exerciseLogs) && $exerciseLogs->count() > 0)
                                <!-- Exercise Stats -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                    @php
                                    $totalCalories = 0;
                                    $totalDuration = 0;
                                    $uniqueExercises = [];

                                    foreach ($exerciseLogs as $log) {
                                    $totalCalories += $log->calories_burned;
                                    $totalDuration += $log->duration_minutes;
                                    if ($log->exercise && !in_array($log->exercise->name, $uniqueExercises)) {
                                    $uniqueExercises[] = $log->exercise->name;
                                    }
                                    }
                                    @endphp

                                    <!-- Calories Burned Card -->
                                    <div
                                        class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg p-6 text-white shadow">
                                        <div class="flex items-center">
                                            <div class="rounded-full bg-white/20 p-3 mr-4">
                                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-white/80 text-sm">Total Calories Burned</p>
                                                <p class="text-3xl font-bold">{{ number_format($totalCalories, 0) }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Duration Card -->
                                    <div
                                        class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg p-6 text-white shadow">
                                        <div class="flex items-center">
                                            <div class="rounded-full bg-white/20 p-3 mr-4">
                                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-white/80 text-sm">Total Workout Duration</p>
                                                <p class="text-3xl font-bold">{{ number_format($totalDuration, 0) }} min
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Exercise Variety Card -->
                                    <div
                                        class="bg-gradient-to-br from-rose-500 to-pink-600 rounded-lg p-6 text-white shadow">
                                        <div class="flex items-center">
                                            <div class="rounded-full bg-white/20 p-3 mr-4">
                                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-white/80 text-sm">Exercise Variety</p>
                                                <p class="text-3xl font-bold">{{ count($uniqueExercises) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Exercise Table and Chart -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Exercise Table -->
                                    <div>
                                        <h4 class="text-md font-medium text-gray-800 mb-4">Recent Exercises</h4>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 rounded-lg">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col"
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Date
                                                        </th>
                                                        <th scope="col"
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Exercise
                                                        </th>
                                                        <th scope="col"
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Duration
                                                        </th>
                                                        <th scope="col"
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Calories
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach($exerciseLogs as $log)
                                                    <tr>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $log->start_time->format('M j, Y') }}
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                            {{ $log->exercise->name ?? 'Unknown Exercise' }}
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                            {{ $log->duration_minutes }} min
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                            {{ round($log->calories_burned, 0) }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Exercise Intensity Chart -->
                                    <div>
                                        <h4 class="text-md font-medium text-gray-800 mb-4">Workout Intensity
                                            Distribution</h4>
                                        <div class="h-64">
                                            <canvas id="intensityChart"></canvas>
                                        </div>

                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                    const ctx = document.getElementById('intensityChart').getContext('2d');
                                                    
                                                    // Process exercise log data for chart
                                                    const exerciseLogs = @json($exerciseLogs);
                                                    
                                                    // Count exercises by intensity
                                                    const intensityCount = {
                                                        'low': 0,
                                                        'medium': 0,
                                                        'high': 0,
                                                        'moderate': 0
                                                    };
                                                    
                                                    exerciseLogs.forEach(log => {
                                                        const intensity = log.intensity_level.toLowerCase();
                                                        if (intensityCount.hasOwnProperty(intensity)) {
                                                            intensityCount[intensity]++;
                                                        }
                                                    });
                                                    
                                                    // Prepare data for chart
                                                    const labels = Object.keys(intensityCount).map(
                                                        intensity => intensity.charAt(0).toUpperCase() + intensity.slice(1)
                                                    );
                                                    const data = Object.values(intensityCount);
                                                    
                                                    const chart = new Chart(ctx, {
                                                        type: 'doughnut',
                                                        data: {
                                                            labels: labels,
                                                            datasets: [{
                                                                data: data,
                                                                backgroundColor: [
                                                                    'rgba(74, 222, 128, 0.7)',   // Low
                                                                    'rgba(251, 191, 36, 0.7)',   // Medium
                                                                    'rgba(239, 68, 68, 0.7)',    // High
                                                                    'rgba(96, 165, 250, 0.7)'    // Moderate
                                                                ],
                                                                borderColor: [
                                                                    'rgba(74, 222, 128, 1)',
                                                                    'rgba(251, 191, 36, 1)',
                                                                    'rgba(239, 68, 68, 1)',
                                                                    'rgba(96, 165, 250, 1)'
                                                                ],
                                                                borderWidth: 1
                                                            }]
                                                        },
                                                        options: {
                                                            responsive: true,
                                                            maintainAspectRatio: false,
                                                            plugins: {
                                                                legend: {
                                                                    position: 'bottom'
                                                                }
                                                            }
                                                        }
                                                    });
                                                });
                                        </script>
                                    </div>
                                </div>

                                <!-- Calories Burned Timeline -->
                                <div class="mt-8">
                                    <h4 class="text-md font-medium text-gray-800 mb-4">Calories Burned Timeline</h4>
                                    <div class="h-64">
                                        <canvas id="caloriesBurnedChart"></canvas>
                                    </div>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                                const ctx = document.getElementById('caloriesBurnedChart').getContext('2d');
                                                
                                                // Process exercise log data for chart
                                                const exerciseLogs = @json($exerciseLogs);
                                                
                                                // Group by date and calculate total calories burned
                                                const caloriesByDate = {};
                                                
                                                exerciseLogs.forEach(log => {
                                                    const dateStr = new Date(log.start_time).toISOString().split('T')[0];
                                                    
                                                    if (!caloriesByDate[dateStr]) {
                                                        caloriesByDate[dateStr] = 0;
                                                    }
                                                    
                                                    caloriesByDate[dateStr] += log.calories_burned;
                                                });
                                                
                                                // Sort dates
                                                const sortedDates = Object.keys(caloriesByDate).sort();
                                                
                                                // Prepare data for chart
                                                const labels = sortedDates.map(date => {
                                                    const d = new Date(date);
                                                    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                                                });
                                                
                                                const caloriesData = sortedDates.map(date => caloriesByDate[date]);
                                                
                                                const chart = new Chart(ctx, {
                                                    type: 'line',
                                                    data: {
                                                        labels: labels,
                                                        datasets: [{
                                                            label: 'Calories Burned',
                                                            data: caloriesData,
                                                            backgroundColor: 'rgba(147, 51, 234, 0.2)',
                                                            borderColor: 'rgba(147, 51, 234, 1)',
                                                            borderWidth: 2,
                                                            tension: 0.3,
                                                            fill: true,
                                                            pointBackgroundColor: 'rgba(147, 51, 234, 1)',
                                                            pointRadius: 4
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false,
                                                        scales: {
                                                            y: {
                                                                beginAtZero: true,
                                                                title: {
                                                                    display: true,
                                                                    text: 'Calories'
                                                                }
                                                            },
                                                            x: {
                                                                title: {
                                                                    display: true,
                                                                    text: 'Date'
                                                                }
                                                            }
                                                        },
                                                        plugins: {
                                                            title: {
                                                                display: true,
                                                                text: 'Daily Calories Burned from Exercise'
                                                            }
                                                        }
                                                    }
                                                });
                                            });
                                    </script>
                                </div>
                                @else
                                <div class="text-center py-8 bg-gray-50 rounded-lg">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No exercise logs available for this user.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex justify-end mt-6 gap-4">
                <a href="{{ route('users.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-200 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Users
                </a>

                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include Chart.js Annotations plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>
</x-app-layout>