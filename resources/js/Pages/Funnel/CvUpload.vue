<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Upload Your CV
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Almost there! Please upload your updated resume/CV.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form class="space-y-6" @submit.prevent="submitForm">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700"> Resume/CV (PDF, DOCX) </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-500 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 justify-center mt-2">
                                    <label for="cv-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span>Select a file</span>
                                        <input id="cv-upload" type="file" class="sr-only" @change="handleFileChange" accept=".pdf,.doc,.docx" required>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">PDF, DOC up to 5MB</p>
                            </div>
                        </div>
                        <p v-if="form.cv" class="mt-2 text-sm text-green-600 font-medium">Selected file: {{ form.cv.name }}</p>
                        <p v-if="form.errors.cv" class="mt-2 text-sm text-red-600">{{ form.errors.cv }}</p>
                    </div>

                    <div>
                        <button type="submit" :disabled="form.processing" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none disabled:opacity-50">
                            <span v-if="form.processing">Uploading...</span>
                            <span v-else>Submit Application</span>
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
});

const handleFileChange = (e) => {
    form.cv = e.target.files[0];
};

const submitForm = () => {
    form.post(route('candidate.cv_upload'), {
        forceFormData: true,
    });
};
</script>