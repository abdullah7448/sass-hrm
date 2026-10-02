<template>
    <AdminLayout>
        <template #header>
            My Attendance History
        </template>

        <div class="space-y-6">
            
            <!-- 🟢 Filter Section -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
                <h3 class="font-bold text-gray-800 text-lg">Filter Records</h3>
                <div class="flex items-center gap-3">
                    <!-- Dynamic Month & Year Picker -->
                    <input 
                        type="month" 
                        v-model="selectedMonthYear" 
                        class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                    >
                    <button 
                        @click="applyFilter" 
                        class="px-5 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-md hover:bg-blue-700 transition shadow-sm flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filter Data
                    </button>
                </div>
            </div>

            <!-- 🟢 Overview Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Present</p>
                        <h4 class="text-2xl font-black text-green-600">{{ summary.present }}</h4>
                    </div>
                    <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Late</p>
                        <h4 class="text-2xl font-black text-yellow-600">{{ summary.late }}</h4>
                    </div>
                    <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Absent</p>
                        <h4 class="text-2xl font-black text-gray-800">{{ summary.absent }}</h4>
                    </div>
                    <div class="p-3 bg-gray-100 text-gray-700 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">On Leave</p>
                        <h4 class="text-2xl font-black text-red-500">{{ summary.leave }}</h4>
                    </div>
                    <div class="p-3 bg-red-50 text-red-500 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- 🟢 Attendance Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Detailed Log: <span class="text-blue-600">{{ summary.month_name }}</span>
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="p-4 font-semibold">Date</th>
                                <th class="p-4 font-semibold">Check In</th>
                                <th class="p-4 font-semibold">Check Out</th>
                                <th class="p-4 font-semibold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="att in attendances" :key="att.id" class="hover:bg-gray-50">
                                <td class="p-4 text-sm font-medium text-gray-900">{{ att.date }}</td>
                                <td class="p-4 text-sm text-gray-600 font-mono">{{ att.check_in || '--:--' }}</td>
                                <td class="p-4 text-sm text-gray-600 font-mono">{{ att.check_out || '--:--' }}</td>
                                <td class="p-4 text-center">
                                    <span v-if="att.status === 'present'" class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded capitalize">Present</span>
                                    <span v-else-if="att.status === 'late'" class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded capitalize">Late</span>
                                    <span v-else-if="att.status === 'absent'" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded capitalize">Absent</span>
                                    <span v-else class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded capitalize">{{ att.status }}</span>
                                </td>
                            </tr>
                            <tr v-if="attendances.length === 0">
                                <td colspan="4" class="p-12 text-center text-gray-500 text-sm flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    No attendance records found for {{ summary.month_name }}.
                                </td>
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
import { usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();

// ডাটাবেস থেকে পাওয়া ডাইনামিক ডেটা
const attendances = computed(() => page.props.attendances || []);
const summary = computed(() => page.props.summary || {});

// ফিল্টারের জন্য ডাইনামিক Date Value (YYYY-MM)
const selectedMonthYear = ref(page.props.filterDate);

// ফিল্টার অ্যাপ্লাই করার ফাংশন
const applyFilter = () => {
    router.get(route('my-attendance'), { 
        month_year: selectedMonthYear.value 
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>