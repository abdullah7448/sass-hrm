<template>
    <AdminLayout>
        <template #header>
            Leave & Holidays
        </template>

        <div class="space-y-6">
            
            <!-- 🟢 Apply for Leave Form -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Apply for Leave
                </h3>

                <form @submit.prevent="submitLeave" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Leave Type</label>
                        <select v-model="form.leave_type" class="w-full border-gray-300 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                            <option value="Casual Leave">Casual Leave</option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Annual Leave">Annual Leave</option>
                            <option value="Unpaid Leave">Unpaid Leave</option>
                        </select>
                    </div>

                    <div></div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Start Date</label>
                        <input type="date" v-model="form.start_date" class="w-full border-gray-300 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">End Date</label>
                        <input type="date" v-model="form.end_date" class="w-full border-gray-300 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Reason for Leave</label>
                        <textarea v-model="form.reason" rows="3" class="w-full border-gray-300 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Write your reason clearly..."></textarea>
                    </div>

                    <div class="sm:col-span-2 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow transition" :disabled="form.processing">
                            Submit Application
                        </button>
                    </div>
                </form>
            </div>

            <!-- 🟢 My Leave History Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">My Leave History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="p-4 font-semibold">Type</th>
                                <th class="p-4 font-semibold">From - To</th>
                                <th class="p-4 font-semibold">Reason</th>
                                <th class="p-4 font-semibold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            <tr v-for="leave in leaves" :key="leave.id" class="hover:bg-gray-50">
                                <td class="p-4 font-bold text-gray-800">{{ leave.leave_type }}</td>
                                <td class="p-4 text-gray-600 font-mono text-xs">{{ leave.start_date }} to {{ leave.end_date }}</td>
                                <td class="p-4 text-gray-600 max-w-xs truncate">{{ leave.reason }}</td>
                                <td class="p-4 text-center">
                                    <span v-if="leave.status === 'approved'" class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full capitalize">Approved</span>
                                    <span v-else-if="leave.status === 'rejected'" class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full capitalize">Rejected</span>
                                    <span v-else class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full capitalize">Pending</span>
                                </td>
                            </tr>
                            <tr v-if="leaves.length === 0">
                                <td colspan="4" class="p-8 text-center text-gray-400">No leave applications found.</td>
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
import { useForm } from '@inertiajs/vue3';

defineProps({
    leaves: Array,
});

const form = useForm({
    leave_type: 'Casual Leave',
    start_date: '',
    end_date: '',
    reason: '',
});

const submitLeave = () => {
    form.post(route('leaves.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            alert('Leave application submitted successfully!');
        }
    });
};
</script>