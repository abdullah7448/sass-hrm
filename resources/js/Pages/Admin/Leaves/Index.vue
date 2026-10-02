<template>
    <AdminLayout>
        <template #header>
            Manage Leave Requests
        </template>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">All Employee Leave Requests</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="p-4 font-semibold">Employee</th>
                                <th class="p-4 font-semibold">Leave Type</th>
                                <th class="p-4 font-semibold">Duration</th>
                                <th class="p-4 font-semibold">Reason</th>
                                <th class="p-4 font-semibold text-center">Status</th>
                                <th class="p-4 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            <tr v-for="leave in leaves" :key="leave.id" class="hover:bg-gray-50">
                                <td class="p-4">
                                    <p class="font-bold text-gray-900">{{ leave.user?.name }}</p>
                                    <p class="text-xs text-gray-500">{{ leave.user?.email }}</p>
                                </td>
                                <td class="p-4 font-semibold text-blue-600">{{ leave.leave_type }}</td>
                                <td class="p-4 text-gray-600 font-mono text-xs">{{ leave.start_date }} <br>to {{ leave.end_date }}</td>
                                <td class="p-4 text-gray-600 max-w-xs">{{ leave.reason }}</td>
                                <td class="p-4 text-center">
                                    <span v-if="leave.status === 'approved'" class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full capitalize">Approved</span>
                                    <span v-else-if="leave.status === 'rejected'" class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full capitalize">Rejected</span>
                                    <span v-else class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full capitalize">Pending</span>
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <button 
                                        v-if="leave.status === 'pending'"
                                        @click="updateStatus(leave.id, 'approved')" 
                                        class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded shadow-sm transition"
                                    >
                                        Approve
                                    </button>
                                    <button 
                                        v-if="leave.status === 'pending'"
                                        @click="updateStatus(leave.id, 'rejected')" 
                                        class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded shadow-sm transition"
                                    >
                                        Reject
                                    </button>
                                    <span v-if="leave.status !== 'pending'" class="text-xs text-gray-400 italic">No action</span>
                                </td>
                            </tr>
                            <tr v-if="leaves.length === 0">
                                <td colspan="6" class="p-8 text-center text-gray-400">No leave requests found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';

defineProps({
    leaves: Array,
});

const updateStatus = (id, status) => {
    if (confirm(`Are you sure you want to ${status} this leave request?`)) {
        router.patch(route('leaves.update-status', id), {
            status: status
        }, {
            preserveScroll: true,
            onSuccess: () => alert(`Leave request ${status} successfully!`)
        });
    }
};
</script>