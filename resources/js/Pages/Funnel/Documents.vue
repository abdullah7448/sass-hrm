<template>
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
            
            <div class="bg-blue-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Phase 3: Documents Upload</h2>
                <p class="text-blue-100 text-sm mt-1">Please upload your Resume, NID, and Certificate. (PDF, JPG, PNG)</p>
            </div>
            
            <form @submit.prevent="submitForm" class="p-6 space-y-6">
                
                <!-- Resume Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Resume / CV <span class="text-red-500">*</span></label>
                    <input 
                        type="file" 
                        @input="form.resume = $event.target.files[0]" 
                        accept=".pdf,.doc,.docx" 
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-md shadow-sm p-2" 
                    />
                    <div v-if="form.errors.resume" class="text-red-500 text-xs mt-1">{{ form.errors.resume }}</div>
                </div>

                <!-- NID Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">National ID (NID) / Passport <span class="text-red-500">*</span></label>
                    <input 
                        type="file" 
                        @input="form.nid = $event.target.files[0]" 
                        accept=".pdf,.jpg,.jpeg,.png" 
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-md shadow-sm p-2" 
                    />
                    <div v-if="form.errors.nid" class="text-red-500 text-xs mt-1">{{ form.errors.nid }}</div>
                </div>

                <!-- Certificate Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Latest Educational Certificate</label>
                    <input 
                        type="file" 
                        @input="form.certificate = $event.target.files[0]" 
                        accept=".pdf,.jpg,.jpeg,.png" 
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-md shadow-sm p-2" 
                    />
                    <div v-if="form.errors.certificate" class="text-red-500 text-xs mt-1">{{ form.errors.certificate }}</div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 flex justify-end">
                    <button 
                        type="submit" 
                        :disabled="form.processing" 
                        class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-md disabled:opacity-50 flex items-center"
                    >
                        <span v-if="form.processing">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Uploading...
                        </span>
                        <span v-else>Submit & Continue →</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    resume: null,
    nid: null,
    certificate: null,
});

const submitForm = () => {
    form.post(route('candidate.process_documents'), {
        preserveScroll: true,
        forceFormData: true, // Inertia তে ফাইল আপলোডের জন্য এটি বাধ্যতামূলক!
        onSuccess: () => {
            console.log('Documents uploaded successfully!');
        },
        onError: (errors) => {
            console.error('Upload Error:', errors);
        }
    });
};
</script>