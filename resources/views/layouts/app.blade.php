<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="text-gray-800 font-inter">
    <!--sidenav -->
    @include('layouts.navigation')
    <!-- Overlay -->
    <div class="fixed top-0 left-0 w-full h-full bg-black/50 z-40 md:hidden sidebar-overlay hidden"></div>

    <main class="w-full md:w-[calc(100%-256px)] md:ml-64 bg-gray-100 min-h-screen transition-all main">
        <!-- Top Navigation -->
        <div class="py-2 px-6 bg-white flex items-center shadow-md shadow-black/5 sticky top-0 left-0 z-30">
            <button type="button" class="text-lg text-gray-900 font-semibold sidebar-toggle md:hidden">
                <i class="ri-menu-line"></i>
            </button>

            <!-- User Profile Dropdown -->
            <ul class="ml-auto flex items-center">
                <li class="dropdown ml-3">
                    <button type="button" class="dropdown-toggle flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 relative">
                            <div class="p-1 bg-white rounded-full focus:outline-none focus:ring">
                                <img class="w-8 h-8 rounded-full"
                                    src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}"
                                    alt="{{ Auth::user()->name }}" />
                            </div>
                        </div>
                        <div class="p-2 md:block text-left">
                            <h2 class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</h2>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                    </button>
                    <ul
                        class="dropdown-menu shadow-md shadow-black/5 z-30 hidden py-1.5 rounded-md bg-white border border-gray-100 w-full max-w-[140px]">
                        <li>
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-[#f84525] hover:bg-gray-50">Profile</a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-[#f84525] hover:bg-gray-50">
                                    Logout
                                </a>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- Page Content -->
        <div class="p-6">
            {{ $slot }}
        </div>
    </main>
    
    @stack('scripts')
    <script>
        // Sidebar Toggle Functionality
        function setupSidebar() {
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebarMenu = document.querySelector('.sidebar-menu');
            const sidebarOverlay = document.querySelector('.sidebar-overlay');

            // Toggle menu
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                sidebarMenu.classList.toggle('-translate-x-full');
                sidebarOverlay.classList.toggle('hidden');
            });

            // Close menu when clicking overlay
            sidebarOverlay.addEventListener('click', function() {
                sidebarMenu.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            });

            // Handle dropdowns
            const dropdownToggles = document.querySelectorAll('.sidebar-dropdown-toggle');
            dropdownToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const parent = toggle.closest('.group');
                    parent.classList.toggle('selected');
                });
            });

            // Top Navigation Dropdowns
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    menu.classList.toggle('hidden');
                });

                // Close when clicking outside
                document.addEventListener('click', (e) => {
                    if (!dropdown.contains(e.target)) {
                        menu.classList.add('hidden');
                    }
                });
            });
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', setupSidebar);
    </script>
</body>

</html>