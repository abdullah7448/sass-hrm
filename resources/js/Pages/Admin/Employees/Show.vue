<template>
    <AdminLayout>
        <template #header>
            Employee Digital Profile
        </template>

        <div class="mt-4 max-w-5xl mx-auto">
            <!-- Top Header Action -->
            <div class="mb-4 flex justify-between items-center">
                <Link :href="route('employees.index')" class="flex items-center text-gray-500 hover:text-blue-600 transition font-medium text-sm">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path></svg>
                    Back to Employee List
                </Link>
                <button @click="confirmTerminate" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-200 hover:border-red-600 text-sm font-bold rounded shadow-sm transition-colors">
                    Terminate Employee
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left Sidebar: Basic Info Card -->
                <div class="col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 text-center border-b border-gray-100 bg-gray-50">
                        <div class="w-24 h-24 mx-auto rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-4xl border-4 border-white shadow-sm mb-4">
                            {{ employee.name.charAt(0) }}
                        </div>
                        <h2 class="text-xl font-extrabold text-gray-900">{{ employee.name }}</h2>
                        <p class="text-blue-600 font-medium text-sm mt-1">
                            {{ candidateData?.position || 'Employee' }}
                        </p>
                        <span class="inline-block mt-3 px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Active Status</span>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Email Address</p>
                            <p class="text-sm font-medium text-gray-800 mt-1 break-words">{{ employee.email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Phone Number</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">{{ employee.phone || candidateData?.phone || 'Not Provided' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Joined Date</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">{{ formatDate(employee.created_at) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right Content: Documents & Assessment Data -->
                <div class="col-span-1 md:col-span-2 space-y-6">
                    
                   <!-- Candidate Journey Info -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Recruitment Documents
                        </h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- NID Card -->
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 flex items-start gap-4">
                                <div class="p-3 bg-white rounded-lg shadow-sm border border-gray-100 text-blue-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">National ID Card</p>
                                    <a v-if="employee.nid_path || candidateData?.nid_path" :href="`/storage/${employee.nid_path || candidateData.nid_path}`" target="_blank" class="text-xs text-blue-600 hover:underline font-medium mt-1 inline-block">View Document &rarr;</a>
                                    <p v-else class="text-xs text-red-500 font-medium mt-1">Not Uploaded</p>
                                </div>
                            </div>
                            
                            <!-- Certificate/CV -->
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 flex items-start gap-4">
                                <div class="p-3 bg-white rounded-lg shadow-sm border border-gray-100 text-indigo-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">Certificate / CV</p>
                                    <a v-if="employee.certificate_path || candidateData?.certificate_path" :href="`/storage/${employee.certificate_path || candidateData.certificate_path}`" target="_blank" class="text-xs text-blue-600 hover:underline font-medium mt-1 inline-block">View Document &rarr;</a>
                                    <p v-else class="text-xs text-red-500 font-medium mt-1">Not Uploaded</p>
                                </div>
                            </div>

                            <!-- Resume (Optional - if separate from Certificate) -->
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 flex items-start gap-4 sm:col-span-2">
                                <div class="p-3 bg-white rounded-lg shadow-sm border border-gray-100 text-green-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">Resume / CV</p>
                                    <a v-if="employee.resume_path || candidateData?.resume_path" :href="`/storage/${employee.resume_path || candidateData.resume_path}`" target="_blank" class="text-xs text-blue-600 hover:underline font-medium mt-1 inline-block">View Document &rarr;</a>
                                    <p v-else class="text-xs text-red-500 font-medium mt-1">Not Uploaded</p>
                                </div>
                            </div>

                            <!-- Portfolio Link -->
                            <div v-if="candidateData?.portfolio" class="col-span-1 sm:col-span-2 border border-gray-200 rounded-lg p-4 bg-gray-50 flex items-center justify-between mt-2">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    <span class="font-bold text-gray-800 text-sm">Portfolio Link:</span>
                                </div>
                                <a :href="candidateData.portfolio" target="_blank" class="text-sm text-blue-600 hover:underline truncate max-w-xs">{{ candidateData.portfolio }}</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    employee: Object,
    candidateData: Object
});

// Terminate Logic
const confirmTerminate = () => {
    if(confirm(`Are you absolutely sure you want to terminate ${props.employee.name}? \n\nThis action cannot be undone.`)) {
        router.delete(route('employees.terminate', props.employee.id));
    }
};

// Date Formatter
const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
};
</script>