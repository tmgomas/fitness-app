<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">User Details</h2>
                    <a href="{{ route('users.edit', $user) }}" 
                        class="bg-[#f84525] text-white py-2 px-4 rounded-md hover:bg-red-700">
                        Edit User
                    </a>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-20 w-20">
                            <img class="h-20 w-20 rounded-full" 
                                src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}" 
                                alt="{{ $user->name }}">
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Role</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $user->is_admin ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $user->is_admin ? 'Administrator' : 'User' }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $user->created_at->format('F j, Y') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $user->updated_at->format('F j, Y') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('users.index') }}" 
                        class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600">
                        Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>