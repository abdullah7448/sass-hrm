<template>
    <AdminLayout>
        <template #header>
            Today's Attendance Dashboard
        </template>

        <div class="space-y-6">
            <!-- 🟢 Header & Overview -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Daily Report</h2>
                    <p class="text-sm text-gray-500">{{ todayDate }}</p>
                </div>
            </div>

            <!-- 🟢 Summary Cards (Clickable Filters) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div @click="currentFilter = 'all'" :class="currentFilter === 'all' ? 'ring-2 ring-blue-500' : ''" class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between cursor-pointer hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Total</p>
                        <h4 class="text-2xl font-black text-blue-600">{{ summary.total }}</h4>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                </div>
                <div @click="currentFilter = 'present'" :class="currentFilter === 'present' ? 'ring-2 ring-green-500' : ''" class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between cursor-pointer hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Present</p>
                        <h4 class="text-2xl font-black text-green-600">{{ summary.present }}</h4>
                    </div>
                    <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div @click="currentFilter = 'late'" :class="currentFilter === 'late' ? 'ring-2 ring-yellow-500' : ''" class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between cursor-pointer hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Late</p>
                        <h4 class="text-2xl font-black text-yellow-600">{{ summary.late }}</h4>
                    </div>
                    <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div @click="currentFilter = 'absent'" :class="currentFilter === 'absent' ? 'ring-2 ring-red-500' : ''" class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between cursor-pointer hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Absent</p>
                        <h4 class="text-2xl font-black text-red-500">{{ summary.absent }}</h4>
                    </div>
                    <div class="p-3 bg-red-50 text-red-500 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- 🟢 Employee Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="p-4 font-semibold">Employee Info</th>
                                <th class="p-4 font-semibold text-center">Status</th>
                                <th class="p-4 font-semibold">Check In</th>
                                <th class="p-4 font-semibold">Check Out</th>
                                <th class="p-4 font-semibold">IP & Late Reason</th>
                                <th class="p-4 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="emp in filteredAttendanceList" :key="emp.id" class="hover:bg-gray-50">
                                <td class="p-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                                            {{ emp.name.charAt(0) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ emp.name }}</p>
                                            <p class="text-xs text-gray-500">{{ emp.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <span v-if="emp.status === 'present'" class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded capitalize">Present</span>
                                    <span v-else-if="emp.status === 'late'" class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded capitalize">
                                        Late <span v-if="emp.is_late_approved" class="text-green-700 font-bold">(Approved)</span>
                                    </span>
                                    <span v-else class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded capitalize">Absent</span>
                                </td>
                                <td class="p-4 text-sm text-gray-600 font-mono">{{ emp.check_in || '--:--' }}</td>
                                <td class="p-4 text-sm text-gray-600 font-mono">{{ emp.check_out || '--:--' }}</td>
                                
                                <!-- IP & Reason Info -->
                                <td class="p-4 text-sm text-gray-600">
                                    <div v-if="emp.ip_address" class="text-xs font-mono text-gray-500 mb-1">
                                        IP: <span class="text-gray-800 font-semibold">{{ emp.ip_address }}</span>
                                    </div>
                                    <div v-if="emp.late_reason" class="text-xs text-yellow-800 bg-yellow-50 p-1.5 rounded border border-yellow-200">
                                        <span class="font-bold">Reason:</span> {{ emp.late_reason }}
                                    </div>
                                    <span v-else class="text-xs text-gray-400 italic">--</span>
                                </td>

                                <td class="p-4 text-right space-x-2">
                                    <button 
                                        v-if="emp.status === 'late' && !emp.is_late_approved" 
                                        @click="approveLate(emp.attendance_id)" 
                                        class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded shadow-sm transition mr-2"
                                    >
                                        Approve
                                    </button>

                                    <Link :href="route('employees.show', emp.id)" class="text-blue-600 hover:underline text-sm font-medium">
                                        Details
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="attendanceList.length === 0">
                                <td colspan="6" class="p-8 text-center text-gray-500 text-sm">No employees found in your company.</td>
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
import { usePage, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const attendanceList = computed(() => page.props.attendanceList || []);
const summary = computed(() => page.props.summary || {});
const todayDate = computed(() => page.props.todayDate);

// Filter State
const currentFilter = ref('all');

const filteredAttendanceList = computed(() => {
    if (currentFilter.value === 'all') return attendanceList.value;
    return attendanceList.value.filter(emp => emp.status === currentFilter.value);
});

// Approve Late Logic
const approveLate = (attendanceId) => {
    if (!attendanceId) {
        alert('Attendance record not found.');
        return;
    }
    
    if (confirm('Are you sure you want to approve this late arrival? This will waive the fine.')) {
        router.post(route('attendance.approve-late', attendanceId), {}, {
            preserveScroll: true,
            onSuccess: () => alert('Late arrival approved successfully!')
        });
    }
};
</script>