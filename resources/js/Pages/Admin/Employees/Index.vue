<template>
    <AdminLayout>
        <template #header>
            Employee Management
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-4">
            
            <!-- Header & Search -->
            <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center bg-gray-50 gap-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Active Employees ({{ employees.length }})
                </h3>
                
                <!-- Live Search Bar -->
                <div class="relative w-full sm:w-72">
                    <input 
                        v-model="searchQuery" 
                        @input="performSearch"
                        type="text" 
                        placeholder="Search by name, email or phone..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Employee Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Employee Details</th>
                            <th class="p-4 font-semibold">Contact Info</th>
                            <th class="p-4 font-semibold text-center">Status</th>
                            <th class="p-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="employee in employees" :key="employee.id" class="hover:bg-slate-50 transition">
                            
                            <!-- Profile Info -->
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <!-- Avatar -->
                                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg border border-blue-200">
                                        {{ employee.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-sm">{{ employee.name }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">Joined: {{ formatDate(employee.created_at) }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td class="p-4">
                                <div class="text-sm font-medium text-gray-800">{{ employee.email }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ employee.phone || 'N/A' }}</div>
                            </td>

                            <!-- Status -->
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Active</span>
                            </td>

                        
                           <!-- Actions (View & Terminate) -->
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- View Profile Button -->
                                    <Link :href="route('employees.show', employee.id)" class="px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-200 hover:border-blue-600 text-sm font-bold rounded shadow-sm transition-colors flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Profile
                                    </Link>
                                    
                                    <!-- Terminate Button -->
                                    <button @click="confirmTerminate(employee)" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-200 hover:border-red-600 text-sm font-bold rounded shadow-sm transition-colors flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Fire
                                    </button>
                                </div>
                            </td>

                        </tr>

                        <!-- Empty State -->
                        <tr v-if="employees.length === 0">
                            <td colspan="4" class="p-10 text-center text-gray-500 font-medium">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                No active employees found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router ,Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    employees: Array,
    filters: Object
});

// Search Logic (Debounced for performance)
const searchQuery = ref(props.filters.search || '');

const performSearch = debounce(() => {
    router.get(route('employees.index'), { search: searchQuery.value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300); // 300ms delay so it doesn't search on every single keystroke

// Terminate Logic
const confirmTerminate = (employee) => {
    if(confirm(`Are you absolutely sure you want to terminate ${employee.name}? \n\nThis will remove their access and release their email to be used in other companies.`)) {
        router.delete(route('employees.terminate', employee.id), {
            preserveScroll: true
        });
    }
};

// Date Formatter
const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
};
</script>