<x-app-layout>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Create Agreement</h2>
                <a href="{{ route('agreements.index') }}" class="text-gray-500 hover:text-gray-700">
                    Back to List
                </a>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('agreements.store') }}" method="POST">
                @csrf

                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Version -->
                        <div>
                            <label for="version" class="block text-sm font-medium text-gray-700">Version</label>
                            <input type="text" name="version" id="version" value="{{ old('version') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('version') border-red-500 @enderror"
                                placeholder="e.g. 1.0, 2.1">
                            @error('version')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Effective Date -->
                        <div>
                            <label for="effective_date" class="block text-sm font-medium text-gray-700">Effective Date</label>
                            <input type="date" name="effective_date" id="effective_date" value="{{ old('effective_date', now()->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('effective_date') border-red-500 @enderror">
                            @error('effective_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="flex items-center mt-6">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-[#f84525] shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50">
                            <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">Active</label>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="mt-6">
                        <label for="summary" class="block text-sm font-medium text-gray-700">Summary</label>
                        <textarea name="summary" id="summary" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('summary') border-red-500 @enderror">{{ old('summary') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Provide a brief summary of the agreement (optional)</p>
                        @error('summary')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="mt-6">
                        <label for="content" class="block text-sm font-medium text-gray-700">Agreement Content</label>
                        <textarea name="content" id="content" rows="15"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f84525] focus:ring focus:ring-[#f84525] focus:ring-opacity-50 @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-[#f84525] text-white py-2 px-4 rounded-md hover:bg-red-700">
                        Create Agreement
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // If you want to use a rich text editor like TinyMCE or CKEditor for the content field
        // Add the initialization code here
    </script>
    @endpush
</x-app-layout>