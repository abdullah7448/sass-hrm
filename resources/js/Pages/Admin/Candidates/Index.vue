<template>
    <AdminLayout>
        <template #header>
            Job Applications
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Candidate Info</th>
                            <th class="p-4 font-semibold">Applied Position</th>
                            <th class="p-4 font-semibold">IQ Score</th>
                            <th class="p-4 font-semibold">Dept. Score</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="candidate in candidates" :key="candidate.id" class="hover:bg-gray-50 transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-gray-900">{{ candidate.name }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ candidate.phone }}</div>
                            </td>
                            <td class="p-4 text-sm font-semibold text-blue-600">
                                {{ candidate.position }}
                            </td>
                            <td class="p-4 text-sm font-bold text-gray-700">
                                {{ candidate.iq_score || 'N/A' }}
                            </td>
                            <td class="p-4 text-sm font-bold text-gray-700">
                                {{ candidate.department_score || 'N/A' }}
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">
                                    {{ candidate.status || 'Pending' }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-3">
                                <button class="text-blue-600 hover:text-blue-900 text-sm font-semibold">View CV</button>
                                <button class="text-green-600 hover:text-green-900 text-sm font-semibold">Approve</button>
                            </td>
                        </tr>
                        
                        <!-- Empty State -->
                        <tr v-if="candidates.length === 0">
                            <td colspan="6" class="p-8 text-center text-gray-500">
                                No applications found for your company yet.
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
import { Link } from '@inertiajs/vue3';

defineProps({
    candidates: Array
});
</script>