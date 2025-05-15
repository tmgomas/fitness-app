<div class="fixed left-0 top-0 w-64 h-full bg-[#f8f4f3] p-4 z-50 sidebar-menu transition-transform">
    <a href="#" class="flex items-center pb-4 border-b border-b-gray-800">
        <h2 class="font-bold text-2xl">LOREM <span class="bg-[#f84525] text-white px-2 rounded-md">IPSUM</span></h2>
    </a>

    <ul class="mt-4">
        <!-- ADMIN SECTION -->
        <span class="text-gray-400 font-bold">ADMIN</span>
        <li class="mb-1 group {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}"
                class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white">
                <i class="ri-home-2-line mr-3 text-lg"></i>
                <span class="text-sm">Dashboard</span>
            </a>
        </li>

        <!-- Users Management -->
        <li class="mb-1 group {{ request()->routeIs('users.*') ? 'selected' : '' }}">
            <a href="{{ route('users.index') }}"
                class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white sidebar-dropdown-toggle">
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
            <a href="{{ route('nutrition-types.index') }}"
                class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white sidebar-dropdown-toggle">
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
<!-- Agreement Management -->
<li class="mb-1 group {{ request()->routeIs('agreements.*') ? 'selected' : '' }}">
    <a href="{{ route('agreements.index') }}"
        class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white sidebar-dropdown-toggle">
        <i class='bx bx-file mr-3 text-lg'></i>
        <span class="text-sm">Agreements</span>
        <i class="ri-arrow-right-s-line ml-auto group-[.selected]:rotate-90"></i>
    </a>
    <ul class="pl-7 mt-2 hidden group-[.selected]:block">
        <li class="mb-4">
            <a href="{{ route('agreements.index') }}"
                class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                All Agreements
            </a>
        </li>
        <li class="mb-4">
            <a href="{{ route('agreements.create') }}"
                class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                Add Agreement
            </a>
        </li>
    </ul>
</li>
        <!-- Food Items -->
        <li class="mb-1 group {{ request()->routeIs('food-items.*') ? 'selected' : '' }}">
            <a href="{{ route('food-items.index') }}"
                class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white sidebar-dropdown-toggle">
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

        <!-- Meals -->
        <li class="mb-1 group {{ request()->routeIs('meals.*') ? 'selected' : '' }}">
            <a href="{{ route('meals.index') }}"
                class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white sidebar-dropdown-toggle">
                <i class='bx bx-dish mr-3 text-lg'></i>
                <span class="text-sm">Meals</span>
                <i class="ri-arrow-right-s-line ml-auto group-[.selected]:rotate-90"></i>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="{{ route('meals.index') }}"
                        class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                        All Meals
                    </a>
                </li>
                <li class="mb-4">
                    <a href="{{ route('meals.create') }}"
                        class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                        Add Meal
                    </a>
                </li>
            </ul>
        </li>

        <!-- Food Nutrition -->
        <li class="mb-1 group {{ request()->routeIs('food-nutrition.*') ? 'selected' : '' }}">
            <a href="{{ route('food-nutrition.index') }}"
                class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white sidebar-dropdown-toggle">
                <i class='bx bx-calculator mr-3 text-lg'></i>
                <span class="text-sm">Food Nutrition</span>
                <i class="ri-arrow-right-s-line ml-auto group-[.selected]:rotate-90"></i>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="{{ route('food-nutrition.index') }}"
                        class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                        All Nutrition
                    </a>
                </li>
                <li class="mb-4">
                    <a href="{{ route('food-nutrition.create') }}"
                        class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                        Add Nutrition
                    </a>
                </li>
            </ul>
        </li>

        <span class="text-gray-400 font-bold mt-6">EXERCISE MANAGEMENT</span>

        <!-- Exercise Categories -->
        <li class="mb-1 group {{ request()->routeIs('exercise-categories.*') ? 'selected' : '' }}">
            <a href="{{ route('exercise-categories.index') }}"
                class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white sidebar-dropdown-toggle">
                <i class='bx bx-category mr-3 text-lg'></i>
                <span class="text-sm">Exercise Categories</span>
                <i class="ri-arrow-right-s-line ml-auto group-[.selected]:rotate-90"></i>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="{{ route('exercise-categories.index') }}"
                        class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                        All Categories
                    </a>
                </li>
                <li class="mb-4">
                    <a href="{{ route('exercise-categories.create') }}"
                        class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                        Add Category
                    </a>
                </li>
            </ul>
        </li>

        <!-- Exercises -->
        <li class="mb-1 group {{ request()->routeIs('exercises.*') ? 'selected' : '' }}">
            <a href="{{ route('exercises.index') }}"
                class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white sidebar-dropdown-toggle">
                <i class='bx bx-dumbbell mr-3 text-lg'></i>
                <span class="text-sm">Exercises</span>
                <i class="ri-arrow-right-s-line ml-auto group-[.selected]:rotate-90"></i>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="{{ route('exercises.index') }}"
                        class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                        All Exercises
                    </a>
                </li>
                <li class="mb-4">
                    <a href="{{ route('exercises.create') }}"
                        class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                        Add Exercise
                    </a>
                </li>
            </ul>
        </li>

        <!-- Exercise Intensities -->
        <li class="mb-1 group {{ request()->routeIs('exercise-intensities.*') ? 'selected' : '' }}">
            <a href="{{ route('exercise-intensities.index') }}"
                class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md group-[.active]:bg-gray-800 group-[.active]:text-white sidebar-dropdown-toggle">
                <i class='bx bx-pulse mr-3 text-lg'></i>
                <span class="text-sm">Exercise Intensities</span>
                <i class="ri-arrow-right-s-line ml-auto group-[.selected]:rotate-90"></i>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="{{ route('exercise-intensities.index') }}"
                        class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                        All Intensities
                    </a>
                </li>
                <li class="mb-4">
                    <a href="{{ route('exercise-intensities.create') }}"
                        class="text-gray-900 text-sm flex items-center hover:text-[#f84525] before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">
                        Add Intensity
                    </a>
                </li>
            </ul>
        </li>

        <!-- PROFILE SECTION -->
        <span class="text-gray-400 font-bold mt-6">PROFILE</span>
        <li class="mb-1 group">
            <a href="{{ route('profile.edit') }}"
                class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md">
                <i class='bx bx-user-circle mr-3 text-lg'></i>
                <span class="text-sm">My Profile</span>
            </a>
        </li>
        <li class="mb-1 group">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                    class="flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-gray-950 hover:text-gray-100 rounded-md">
                    <i class='bx bx-log-out mr-3 text-lg'></i>
                    <span class="text-sm">Logout</span>
                </a>
            </form>
        </li>
    </ul>
</div>