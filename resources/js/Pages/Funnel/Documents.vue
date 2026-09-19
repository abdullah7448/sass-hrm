<template>
    <div class="min-h-screen bg-gray-100 py-16 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
        <div class="max-w-3xl w-full">
            
            <!-- Header Section -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Upload Your Documents</h2>
                <p class="text-lg text-gray-600 max-w-xl mx-auto">
                    We need a few documents to verify your profile. Please upload your updated CV, NID, and academic certificate below.
                </p>
            </div>

            <!-- Upload Form Card -->
            <div class="bg-white shadow-2xl rounded-2xl p-8 sm:p-12">
                <form @submit.prevent="submitDocs" class="space-y-10">
                    
                    <!-- CV Upload Box -->
                    <div>
                        <label class="block text-lg font-bold text-gray-800 mb-3">1. Resume / CV <span class="text-sm font-normal text-gray-500">(PDF, DOCX)</span></label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 hover:border-blue-500 hover:bg-blue-50 transition-all bg-gray-50 cursor-pointer relative">
                            <input type="file" @change="e => form.cv = e.target.files[0]" accept=".pdf,.doc,.docx" required 
                                class="block w-full text-base text-gray-700 
                                file:mr-6 file:py-3 file:px-6 file:rounded-lg file:border-0 
                                file:text-sm file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" />
                        </div>
                        <p v-if="form.errors.cv" class="text-red-500 text-sm mt-2 font-medium">{{ form.errors.cv }}</p>
                    </div>

                    <!-- NID Upload Box -->
                    <div>
                        <label class="block text-lg font-bold text-gray-800 mb-3">2. NID Copy <span class="text-sm font-normal text-gray-500">(PDF, JPG, PNG)</span></label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 hover:border-blue-500 hover:bg-blue-50 transition-all bg-gray-50 cursor-pointer relative">
                            <input type="file" @change="e => form.nid = e.target.files[0]" accept=".pdf,.jpg,.jpeg,.png" required 
                                class="block w-full text-base text-gray-700 
                                file:mr-6 file:py-3 file:px-6 file:rounded-lg file:border-0 
                                file:text-sm file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" />
                        </div>
                        <p v-if="form.errors.nid" class="text-red-500 text-sm mt-2 font-medium">{{ form.errors.nid }}</p>
                    </div>

                    <!-- Certificate Upload Box -->
                    <div>
                        <label class="block text-lg font-bold text-gray-800 mb-3">3. Academic Certificate <span class="text-sm font-normal text-gray-500">(PDF, JPG, PNG)</span></label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 hover:border-blue-500 hover:bg-blue-50 transition-all bg-gray-50 cursor-pointer relative">
                            <input type="file" @change="e => form.certificate = e.target.files[0]" accept=".pdf,.jpg,.jpeg,.png" required 
                                class="block w-full text-base text-gray-700 
                                file:mr-6 file:py-3 file:px-6 file:rounded-lg file:border-0 
                                file:text-sm file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" />
                        </div>
                        <p v-if="form.errors.certificate" class="text-red-500 text-sm mt-2 font-medium">{{ form.errors.certificate }}</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6">
                        <button type="submit" :disabled="form.processing" 
                            class="w-full py-5 px-6 rounded-xl text-lg sm:text-xl font-bold text-white transition-all duration-200 shadow-md focus:outline-none focus:ring-4 focus:ring-opacity-50 bg-blue-600 hover:bg-blue-700 hover:shadow-xl hover:-translate-y-1 focus:ring-blue-500 disabled:opacity-70 disabled:cursor-not-allowed">
                            <span v-if="form.processing">Uploading Documents (Please Wait)...</span>
                            <span v-else>Upload & Continue to Rules →</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({ 
    cv: null, 
    nid: null, 
    certificate: null 
});

const submitDocs = () => {
    form.post(route('candidate.documents'), { 
        forceFormData: true 
    });
};
</script>