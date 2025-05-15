<x-app-layout>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">View Agreement</h2>
                <div class="flex gap-3">
                    <a href="{{ route('agreements.edit', $agreement) }}" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                        Edit
                    </a>
                    <a href="{{ route('agreements.index') }}" class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Agreement Details -->
            <div class="mb-6 bg-gray-50 p-6 rounded-lg">
                <div class="flex justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ $agreement->title }}</h3>
                        <p class="text-sm text-gray-500">Version: {{ $agreement->version }}</p>
                    </div>
                    <div>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $agreement->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $agreement->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Effective Date</p>
                        <p class="mt-1">{{ $agreement->effective_date->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Created</p>
                        <p class="mt-1">{{ $agreement->created_at->format('F j, Y') }}</p>
                    </div>
                </div>

                @if($agreement->summary)
                <div class="mb-6">
                    <p class="text-sm font-medium text-gray-700">Summary</p>
                    <div class="mt-2 p-4 bg-white rounded-md border border-gray-200">
                        {{ $agreement->summary }}
                    </div>
                </div>
                @endif

                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Agreement Content</p>
                    <div class="mt-2 p-4 bg-white rounded-md border border-gray-200 prose max-w-none">
                        {!! $agreement->content !!}
                    </div>
                </div>
            </div>

            <!-- User Agreements Section -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Users Who Accepted This Agreement</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accepted At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($agreement->userAgreements as $userAgreement)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($userAgreement->user->name) }}" alt="{{ $userAgreement->user->name }}">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $userAgreement->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $userAgreement->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $userAgreement->accepted_at->format('M j, Y, g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $userAgreement->ip_address ?? 'Not recorded' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No users have accepted this agreement yet
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>