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
    <div class="fixed left-0 top-0 w-64 h-full bg-[#f8f4f3] p-4 z-50 sidebar-menu transition-transform">
    <a href="#" class="flex items-center pb-4 border-b border-b-gray-800">
        <h2 class="font-bold text-2xl">LOREM <span class="bg-[#f84525] text-white px-2 rounded-md">IPSUM</span></h2>
    </a>
    
    <ul class="mt-4">
        <!-- ADMIN SECTION -->
        <span class="text-gray-400 font-bold">ADMIN</span>
        <li class="mb-1 group {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white">
                <i class="ri-home-2-line mr-3 text-lg"></i>
                <span class="text-sm">Dashboard</span>
            </a>
        </li>
        
        <!-- Users Management -->
        <li class="mb-1 group {{ request()->routeIs('users.*') ? 'selected' : '' }}">
            <a href="{{ route('users.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white sidebar-dropdown-toggle">
                <i class='bx bx-user mr-3 text-lg'></i>                
                <span class="text-sm">Users Management</span>
                <i class="ri-arrow-right-s-line ml-auto group-[.selected]:rotate-90"></i>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="{{ route('users.index') }}" 
                       class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                       All Users
                    </a>
                </li>
                <li class="mb-4">
                    <a href="{{ route('users.create') }}" 
                       class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                       Add User
                    </a>
                </li>
            </ul>
        </li>

        <!-- NUTRITION SECTION -->
        <span class="text-gray-400 font-bold mt-6">NUTRITION</span>
        
        <!-- Nutrition Types -->
        <li class="mb-1 group {{ request()->routeIs('nutrition-types.*') ? 'selected' : '' }}">
            <a href="{{ route('nutrition-types.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white sidebar-dropdown-toggle">
                <i class='bx bx-food-menu mr-3 text-lg'></i>                
                <span class="text-sm">Nutrition Types</span>
                <i class="ri-arrow-right-s-line ml-auto group-[.selected]:rotate-90"></i>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="{{ route('nutrition-types.index') }}" 
                       class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                       All Types
                    </a>
                </li>
                <li class="mb-4">
                    <a href="{{ route('nutrition-types.create') }}" 
                       class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                       Add Type
                    </a>
                </li>
            </ul>
        </li>

        <!-- Food Items -->
        <li class="mb-1 group {{ request()->routeIs('food-items.*') ? 'selected' : '' }}">
            <a href="{{ route('food-items.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white sidebar-dropdown-toggle">
                <i class='bx bx-food-tag mr-3 text-lg'></i>                
                <span class="text-sm">Food Items</span>
                <i class="ri-arrow-right-s-line ml-auto group-[.selected]:rotate-90"></i>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="{{ route('food-items.index') }}" 
                       class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                       All Foods
                    </a>
                </li>
                <li class="mb-4">
                    <a href="{{ route('food-items.create') }}" 
                       class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                       Add Food
                    </a>
                </li>
            </ul>
        </li>

        <!-- PROFILE SECTION -->
        <span class="text-gray-400 font-bold mt-6">PROFILE</span>
        <li class="mb-1 group">
            <a href="{{ route('profile.edit') }}" class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md">
                <i class='bx bx-user-circle mr-3 text-lg'></i>                
                <span class="text-sm">My Profile</span>
            </a>
        </li>
        <li class="mb-1 group">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); this.closest('form').submit();" 
                   class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md">
                    <i class='bx bx-log-out mr-3 text-lg'></i>
                    <span class="text-sm">Logout</span>
                </a>
            </form>
        </li>
    </ul>
</div>

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
                                <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}" alt="{{ Auth::user()->name }}"/>
                            </div>
                        </div>
                        <div class="p-2 md:block text-left">
                            <h2 class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</h2>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>                
                    </button>
                    <ul class="dropdown-menu shadow-md shadow-black/5 z-30 hidden py-1.5 rounded-md bg-white border border-gray-100 w-full max-w-[140px]">
                        <li>
                            <a href="{{ route('profile.edit') }}" class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-[#f84525] hover:bg-gray-50">Profile</a>
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