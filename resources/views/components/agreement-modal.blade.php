<div
    x-data="{ 
        show: false,
        agreement: null,
        accepted: false,
        loading: false,
        init() {
            this.checkAgreement();
        },
        checkAgreement() {
            fetch('/api/agreements/check-status')
                .then(response => response.json())
                .then(data => {
                    if (data.needs_acceptance && data.agreement) {
                        this.agreement = data.agreement;
                        this.show = true;
                    }
                });
        },
        acceptAgreement() {
            this.loading = true;
            fetch('/api/agreements/accept', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
                },
                body: JSON.stringify({
                    agreement_id: this.agreement.id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    this.accepted = true;
                    setTimeout(() => {
                        this.show = false;
                    }, 1500);
                }
                this.loading = false;
            })
            .catch(error => {
                console.error('Error:', error);
                this.loading = false;
            });
        }
    }"
    @keydown.escape.window="if (!loading) show = false"
    class="relative"
>
    <!-- Modal Background Overlay -->
    <div 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black bg-opacity-50"
    ></div>

    <!-- Modal Content -->
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
    >
        <div class="relative w-full max-w-2xl mx-auto bg-white rounded-lg shadow-xl overflow-hidden">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-xl font-semibold text-gray-900" x-text="agreement ? agreement.title : 'Agreement'"></h3>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4 max-h-[60vh] overflow-y-auto">
                <template x-if="agreement && !accepted">
                    <div>
                        <!-- Agreement Content -->
                        <div class="prose max-w-none" x-html="agreement.content"></div>
                        
                        <!-- Agreement Version -->
                        <div class="mt-4 text-sm text-gray-500">
                            <span>Version: </span>
                            <span x-text="agreement.version"></span>
                            <span class="mx-2">|</span>
                            <span>Effective Date: </span>
                            <span x-text="agreement.effective_date"></span>
                        </div>
                    </div>
                </template>
                
                <!-- Acceptance Message -->
                <template x-if="accepted">
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="mt-4 text-xl font-medium text-gray-900">Agreement Accepted</p>
                        <p class="mt-2 text-gray-500">Thank you for accepting our agreement.</p>
                    </div>
                </template>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end">
                <template x-if="!accepted">
                    <button
                        @click="acceptAgreement"
                        :disabled="loading"
                        class="bg-[#f84525] text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span x-show="!loading">I Accept</span>
                        <span x-show="loading" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </template>
                <template x-if="accepted">
                    <button
                        @click="show = false"
                        class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                    >
                        Close
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>