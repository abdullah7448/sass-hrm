<template>
    <AdminLayout>
        <template #header>
            Employees Directory
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-4">
            <!-- Header Actions -->
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-700">All Employees</h3>
                <button class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 transition-colors shadow-sm">
                    + Add New Employee
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Employee Info</th>
                            <th class="p-4 font-semibold">Role / Position</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold">Joined Date</th>
                            <th class="p-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="employee in employees" :key="employee.id" class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 flex items-center space-x-3">
                                <!-- Avatar Placeholder -->
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                                    {{ employee.name.charAt(0) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ employee.name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ employee.email }} | {{ employee.phone || 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="p-4 text-sm font-semibold text-gray-700">
                                Employee
                            </td>
                            <td class="p-4">
                                <span v-if="employee.is_active" class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Active</span>
                                <span v-else class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Inactive</span>
                            </td>
                            <td class="p-4 text-sm text-gray-600 font-medium">
                                {{ new Date(employee.created_at).toLocaleDateString() }}
                            </td>
                            <td class="p-4 text-right space-x-3">
                                <button class="text-blue-600 hover:text-blue-900 text-sm font-semibold">Edit</button>
                                <button class="text-red-500 hover:text-red-700 text-sm font-semibold">Deactivate</button>
                            </td>
                        </tr>
                        
                        <!-- Empty State -->
                        <tr v-if="employees.length === 0">
                            <td colspan="5" class="p-12 text-center">
                                <div class="text-gray-400 mb-2">
                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <p class="text-gray-500 font-medium">No employees found.</p>
                                <p class="text-sm text-gray-400 mt-1">Approve a candidate to add them to this list.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    employees: Array
});
</script>