<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- ডাইনামিক কোম্পানির নাম দেখাচ্ছে -->
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Join {{ company.name }} Team
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Take the first step towards your career at {{ company.name }}.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                
                <!-- Form Submission -->
                <form class="space-y-6" @submit.prevent="submitForm">
                    
                    <!-- Name Input -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700"> Full Name </label>
                        <div class="mt-1">
                            <input id="name" v-model="form.name" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        </div>
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>

                    <!-- Phone Input -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700"> Phone Number </label>
                        <div class="mt-1">
                            <input id="phone" v-model="form.phone" type="tel" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        </div>
                        <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</div>
                    </div>

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700"> Email Address </label>
                        <div class="mt-1">
                            <input id="email" v-model="form.email" type="email" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        </div>
                        <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                    </div>

                    <!-- Position Dropdown (Fixed 'applied_for' to 'position') -->
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700"> Position Applied For </label>
                        <div class="mt-1">
                            <select id="position" v-model="form.position" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="" disabled>Select your role...</option>
                                <option v-for="dept in departments" :key="dept" :value="dept">
                                    {{ dept }}
                                </option>
                            </select>
                        </div>
                        <div v-if="form.errors.position" class="text-red-500 text-xs mt-1">{{ form.errors.position }}</div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" :disabled="form.processing" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-colors">
                            <span v-if="form.processing">Processing...</span>
                            <span v-else>Continue to Assessment →</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

// Props received from CandidateFunnelController
const props = defineProps({
    company: Object,
    departments: Array
});

// Form state (Fixed key names to match Laravel validation)
const form = useForm({
    name: '',
    phone: '',
    email: '',
    position: '', // Changed from applied_for to position
});

const submitForm = () => {
    form.post(route('candidate.apply.process', { company_id: props.company.id }), {
        preserveScroll: true,
        onError: (errors) => {
            console.error("Form Validation Failed:", errors);
        }
    });
};
</script>   