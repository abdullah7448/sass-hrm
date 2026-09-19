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
                <!-- ফর্ম সাবমিটে route() এর সাথে company_id পাঠানো হচ্ছে -->
                <form class="space-y-6" @submit.prevent="submitForm">
                    <!-- আগের ফিল্ডগুলো (Name, Phone, Email) ঠিক তেমনই থাকবে... -->
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700"> Full Name </label>
                        <div class="mt-1">
                            <input id="name" v-model="form.name" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700"> Phone Number </label>
                        <div class="mt-1">
                            <input id="phone" v-model="form.phone" type="tel" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700"> Email Address </label>
                        <div class="mt-1">
                            <input id="email" v-model="form.email" type="email" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        </div>
                        <!-- আগের মতো এরর মেসেজগুলো রাখতে পারেন -->
                    </div>

                    <!-- department -->
                    <div>
                        <label for="applied_for" class="block text-sm font-medium text-gray-700"> Position Applied For </label>
                        <div class="mt-1">
                            <select id="applied_for" v-model="form.applied_for" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="" disabled>Select your role...</option>
                                <option v-for="dept in departments" :key="dept" :value="dept">
                                    {{ dept }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <button type="submit" :disabled="form.processing" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
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

// কন্ট্রোলার থেকে পাঠানো ডাটা রিসিভ করছি
const props = defineProps({
    company: Object,
    departments: Array
});

// এখানে applied_for যোগ করা হলো
const form = useForm({
    name: '',
    phone: '',
    email: '',
    applied_for: '', 
});

const submitForm = () => {
    form.post(route('candidate.apply.process', { company_id: props.company.id }));
};
</script>