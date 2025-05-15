<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-2xl font-semibold text-gray-900">{{ $agreement->title }}</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Version {{ $agreement->version }} | Effective: {{ $agreement->effective_date->format('F j, Y') }}
                    </p>
                </div>

                <div class="p-6">
                    @if($agreement->summary)
                    <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                        <h3 class="text-md font-medium text-blue-800 mb-2">Summary</h3>
                        <p class="text-blue-700">{{ $agreement->summary }}</p>
                    </div>
                    @endif

                    <div class="prose max-w-none mb-8">
                        {!! $agreement->content !!}
                    </div>

                    @if($hasAccepted)
                    <div class="flex items-center p-4 mb-6 bg-green-50 border border-green-200 rounded">
                        <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-green-800">You have already accepted this agreement</p>
                            <p class="text-sm text-green-600">Accepted on {{ $acceptedDate->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    @else
                    <form action="{{ route('agreements.accept-submit', $agreement) }}" method="POST" class="mt-6">
                        @csrf
                        <div class="flex items-start mb-6">
                            <div class="flex items-center h-5">
                                <input id="accept" name="accept" type="checkbox" required
                                    class="w-4 h-4 rounded border-gray-300 text-[#f84525] focus:ring-[#f84525]">
                            </div>
                            <label for="accept" class="ml-3 text-sm text-gray-700">
                                I have read and agree to the terms and conditions outlined in this agreement.
                            </label>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" 
                                class="bg-[#f84525] text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                Accept Agreement
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>