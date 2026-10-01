<template>
    <!-- এমপ্লয়ি এবং অ্যাডমিন সবার জন্যই এখন AdminLayout ব্যবহার করা হচ্ছে -->
    <AdminLayout>
        
        <template #header>
            <span v-if="isEmployee()">Employee Workspace</span>
            <span v-else>
                Dashboard Overview 
                <span v-if="$page.props.active_company_id" class="text-xs bg-indigo-100 text-indigo-800 px-2.5 py-1 rounded-full font-semibold ml-2">Client Workspace Mode</span>
            </span>
        </template>

        <!-- ==========================================
             ১. ADMIN & SUPER ADMIN DASHBOARD CONTENT
        =========================================== -->
        <div v-if="!isEmployee()">
            
            <!-- Super Admin View -->
            <div v-if="hasRole('Super Admin') && !$page.props.active_company_id">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                        <div class="p-4 rounded-full bg-blue-50 text-blue-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Total Companies</p>
                            <p class="text-2xl font-bold text-gray-900">3</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                        <div class="p-4 rounded-full bg-purple-50 text-purple-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Active Subscriptions</p>
                            <p class="text-2xl font-bold text-gray-900">2</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Companies List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-800">Recently Onboarded Companies</h3>
                        <Link :href="route('companies.index')" class="text-sm text-blue-600 hover:underline">View All</Link>
                    </div>
                    <div class="p-6 text-center text-gray-500 py-12">
                        Company data will load here...
                    </div>
                </div>
            </div>

            <!-- Company Admin View -->
            <div v-if="hasRole('Company Admin') || $page.props.active_company_id">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                        <div class="p-4 rounded-full bg-green-50 text-green-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Total Employees</p>
                            <p class="text-2xl font-bold text-gray-900">45</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                        <div class="p-4 rounded-full bg-blue-50 text-blue-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">New Job Applications</p>
                            <p class="text-2xl font-bold text-gray-900">12</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Applications -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800">Recent Applications</h3>
                    </div>
                    <div class="p-6 text-center text-gray-500 py-12">
                        Candidate table will be implemented here...
                    </div>
                </div>
            </div>

        </div>


        <!-- ==========================================
             ২. EMPLOYEE DASHBOARD CONTENT (With Sidebar)
        =========================================== -->
        <div v-else class="space-y-6">
            
            <!-- Welcome Banner & Check IN/OUT -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-8 text-white shadow-sm relative overflow-hidden flex flex-col md:flex-row justify-between items-center">
                <div class="relative z-10 mb-6 md:mb-0 text-center md:text-left">
                    <h1 class="text-3xl font-extrabold mb-2">Welcome back, {{ $page.props.auth.user.name.split(' ')[0] }}! 👋</h1>
                    <p class="text-blue-100 font-medium">{{ isCheckedIn ? 'You are currently clocked in and working.' : "Ready to start your day? Don't forget to check in." }}</p>
                    
                    <button v-if="!isCheckedIn" @click="handleCheckIn" class="mt-6 px-8 py-3 bg-white text-blue-700 hover:bg-gray-50 font-bold rounded-lg shadow transition flex items-center gap-2 mx-auto md:mx-0">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Check In Now
                    </button>

                    <button v-else @click="handleCheckOut" class="mt-6 px-8 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-lg shadow transition flex items-center gap-2 mx-auto md:mx-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Check Out
                    </button>
                </div>

                <!-- Clock Info -->
                <div class="relative z-10 bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-xl text-center min-w-[200px]">
                    <p class="text-xs text-blue-100 font-bold uppercase tracking-wider mb-1">Current Time</p>
                    <p class="text-3xl font-black mb-2">{{ currentTime }}</p>
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold" :class="isCheckedIn ? 'bg-green-400/20 text-green-100' : 'bg-yellow-400/20 text-yellow-100'">
                        <span class="w-2 h-2 rounded-full mr-2" :class="isCheckedIn ? 'bg-green-400 animate-pulse' : 'bg-yellow-400'"></span>
                        {{ isCheckedIn ? 'Checked In' : 'Not Checked In' }}
                    </div>
                </div>
            </div>

            <!-- Dashboard Details Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Attendance Details -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Recent Attendance
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-white border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                                    <th class="p-4 font-semibold">Date</th>
                                    <th class="p-4 font-semibold">Check In</th>
                                    <th class="p-4 font-semibold">Check Out</th>
                                    <th class="p-4 font-semibold text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 text-sm font-medium text-gray-900">Today</td>
                                    <td class="p-4 text-sm text-gray-600">{{ isCheckedIn ? '09:05 AM' : '--:--' }}</td>
                                    <td class="p-4 text-sm text-gray-600">--:--</td>
                                    <td class="p-4 text-center">
                                        <span v-if="isCheckedIn" class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded">Present</span>
                                        <span v-else class="px-2 py-1 bg-gray-100 text-gray-500 text-xs font-bold rounded">Pending</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Leave Management Overview -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Leave Balance</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <span class="text-sm font-semibold text-blue-900">Casual Leave</span>
                                <span class="font-black text-blue-700">08 / 10</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                                <span class="text-sm font-semibold text-red-900">Sick Leave</span>
                                <span class="font-black text-red-700">12 / 14</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Notice Board</h3>
                        <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded text-sm text-yellow-800">
                            <strong>Upcoming Holiday:</strong> Office will remain closed on Friday for National Holiday.
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { usePage, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const page = usePage();

// Role Checker (Case Insensitive)
const hasRole = (roleName) => {
    const roles = page.props.auth.user?.roles || [];
    const normalizedRoles = Object.values(roles).map(r => r.toLowerCase());
    return normalizedRoles.includes(roleName.toLowerCase());
};

// Employee Checker
const isEmployee = () => {
    return hasRole('employee') || page.props.auth.user?.role === 'employee';
};

// =====================================
// Employee Dashboard Reactive States
// =====================================

// Clock state
const currentTime = ref('');
let timer;
const updateTime = () => {
    currentTime.value = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
};

onMounted(() => {
    updateTime();
    timer = setInterval(updateTime, 1000);
});
onUnmounted(() => clearInterval(timer));

// Attendance Simulation 
const isCheckedIn = ref(false);

const handleCheckIn = () => {
    isCheckedIn.value = true;
    alert('Checked In Successfully!');
};

const handleCheckOut = () => {
    isCheckedIn.value = false;
    alert('Checked Out Successfully!');
};
</script>