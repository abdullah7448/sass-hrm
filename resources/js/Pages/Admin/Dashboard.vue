<template>
    <AdminLayout>
        
        <template #header>
            <span v-if="isEmployee()">Employee Workspace</span>
            <span v-else>
                Dashboard Overview 
                <span v-if="$page.props.active_company_id" class="text-xs bg-indigo-100 text-indigo-800 px-2.5 py-1 rounded-full font-semibold ml-2">Client Workspace Mode</span>
            </span>
        </template>

        <!-- ==========================================
             ১. ADMIN DASHBOARD CONTENT (লুকানো আছে)
        =========================================== -->
        <div v-if="!isEmployee()">
            <!-- অ্যাডমিনদের কোড আগের মতোই থাকবে, এখানে হাত দেইনি -->
            <div class="p-6 text-center text-gray-500 py-12 bg-white rounded-xl shadow-sm border border-gray-100">
                Admin Dashboard Content Here...
            </div>
        </div>

        <!-- ==========================================
             ২. EMPLOYEE DASHBOARD CONTENT
        =========================================== -->
        <div v-else class="space-y-6">
            
            <!-- Welcome Banner & Check IN/OUT -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-8 text-white shadow-sm relative overflow-hidden flex flex-col md:flex-row justify-between items-center">
                <div class="relative z-10 mb-6 md:mb-0 text-center md:text-left">
                    <!-- 🟢 FIXED: Added safe optional chaining and fallback for user name -->
                    <h1 class="text-3xl font-extrabold mb-2">Welcome back, {{ $page.props.auth?.user?.name?.split(' ')[0] || 'User' }}! 👋</h1>
                    <div class="bg-red-500 text-white p-2 font-bold mb-4 rounded">
                        My Roles: {{ $page.props.auth?.user?.roles }}
                    </div>
                    <p v-if="hasCheckedOut" class="text-blue-100 font-medium">Great job today! Your shift is completed.</p>
                    <p v-else-if="isCheckedIn" class="text-blue-100 font-medium">You are currently clocked in and working.</p>
                    <p v-else class="text-blue-100 font-medium">Ready to start your day? Don't forget to check in.</p>
                    
                    <!-- Action Buttons -->
                    <div v-if="!todayAttendance">
                        <button @click="handleCheckIn" class="mt-6 px-8 py-3 bg-white text-blue-700 hover:bg-gray-50 font-bold rounded-lg shadow transition flex items-center gap-2 mx-auto md:mx-0">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Check In Now
                        </button>
                    </div>
                    <div v-else-if="!hasCheckedOut">
                        <button @click="handleCheckOut" class="mt-6 px-8 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-lg shadow transition flex items-center gap-2 mx-auto md:mx-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Check Out
                        </button>
                    </div>
                    <div v-else class="mt-6 px-8 py-3 bg-green-500 text-white font-bold rounded-lg shadow mx-auto md:mx-0 inline-flex items-center gap-2 cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Shift Completed
                    </div>
                </div>

                <!-- Clock Info -->
                <div class="relative z-10 bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-xl text-center min-w-[200px]">
                    <p class="text-xs text-blue-100 font-bold uppercase tracking-wider mb-1">Current Time</p>
                    <p class="text-3xl font-black mb-2">{{ currentTime }}</p>
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold" :class="isCheckedIn ? 'bg-green-400/20 text-green-100' : 'bg-yellow-400/20 text-yellow-100'">
                        <span class="w-2 h-2 rounded-full mr-2" :class="isCheckedIn ? 'bg-green-400 animate-pulse' : 'bg-yellow-400'"></span>
                        {{ hasCheckedOut ? 'Checked Out' : (isCheckedIn ? 'Checked In' : 'Not Checked In') }}
                    </div>
                </div>
            </div>

            <!-- Monthly Overview Cards -->
            <div v-if="monthlySummary" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Month</p>
                        <h4 class="text-lg font-black text-gray-800">{{ monthlySummary.month_name }}</h4>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Present</p>
                        <h4 class="text-2xl font-black text-green-600">{{ monthlySummary.present }}</h4>
                    </div>
                    <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Late</p>
                        <h4 class="text-2xl font-black text-yellow-600">{{ monthlySummary.late }}</h4>
                    </div>
                    <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Leaves</p>
                        <h4 class="text-2xl font-black text-red-500">0</h4> <!-- লিভ ম্যানেজমেন্ট হলে এখানে ডাইনামিক ডেটা আসবে -->
                    </div>
                    <div class="p-3 bg-red-50 text-red-500 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Dashboard Details Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- This Month's Attendance Table -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            This Month's Attendance
                        </h3>
                    </div>
                    <div class="overflow-x-auto max-h-96 custom-scrollbar">
                        <table class="w-full text-left border-collapse">
                            <thead class="sticky top-0 z-10">
                                <tr class="bg-white border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                                    <th class="p-4 font-semibold">Date</th>
                                    <th class="p-4 font-semibold">Check In</th>
                                    <th class="p-4 font-semibold">Check Out</th>
                                    <th class="p-4 font-semibold text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="att in monthlyAttendances" :key="att.id" class="hover:bg-gray-50">
                                    <td class="p-4 text-sm font-medium text-gray-900">{{ att.date }}</td>
                                    <td class="p-4 text-sm text-gray-600 font-mono">{{ att.check_in || '--:--' }}</td>
                                    <td class="p-4 text-sm text-gray-600 font-mono">{{ att.check_out || '--:--' }}</td>
                                    <td class="p-4 text-center">
                                        <span v-if="att.status === 'present'" class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded capitalize">Present</span>
                                        <span v-else-if="att.status === 'late'" class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded capitalize">Late</span>
                                        <span v-else class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded capitalize">{{ att.status }}</span>
                                    </td>
                                </tr>
                                <tr v-if="monthlyAttendances.length === 0">
                                    <td colspan="4" class="p-8 text-center text-gray-500 text-sm">No attendance records found for this month yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Side Widget -->
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
                </div>

            </div>
        </div>

    </AdminLayout>
    
    <!-- Late Reason Modal -->
    <div v-if="showLateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100 animate-fade-in">
            <div class="flex items-center gap-3 mb-4 text-yellow-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="text-lg font-bold text-gray-900">You are running late!</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Please provide a valid reason for arriving late today so the management can review it.</p>
            
            <textarea 
                v-model="lateReasonText" 
                rows="3" 
                class="w-full border-gray-300 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm mb-4" 
                placeholder="E.g., Heavy traffic on the way, personal emergency..."
            ></textarea>

            <div class="flex justify-end gap-3">
                <button @click="showLateModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-200 transition">Cancel</button>
                <button @click="handleCheckIn" class="px-5 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition shadow-sm">Submit & Check In</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const page = usePage();
const showLateModal = ref(false);
const lateReasonText = ref('');
const isCheckingIn = ref(false);

// Roles
const hasRole = (roleName) => {
    const auth = page.props.auth;
    
    if (!auth?.user?.roles) {
        return false;
    }
    
    return auth.user.roles.includes(roleName);
};

// 🟢 FIXED: Added fully safe optional chaining for role check
const isEmployee = () => {
    return hasRole('employee') || hasRole('Employee') || page.props.auth?.user?.role === 'employee';
};

// Clock
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

// Data from backend
const todayAttendance = computed(() => page.props.todayAttendance);
const monthlyAttendances = computed(() => page.props.monthlyAttendances || []);
const monthlySummary = computed(() => page.props.monthlySummary);

// Status logic
const isCheckedIn = computed(() => todayAttendance.value && !todayAttendance.value.check_out);
const hasCheckedOut = computed(() => todayAttendance.value && todayAttendance.value.check_out);

// API Calls
const handleCheckIn = () => {
    const now = new Date();
    const currentHour = now.getHours();
    const currentMinute = now.getMinutes();
    
    const isLateTime = (currentHour > 9) || (currentHour === 9 && currentMinute > 10);

    if (isLateTime && !lateReasonText.value) {
        showLateModal.value = true;
        return;
    }

    router.post(route('attendance.check-in'), {
        late_reason: lateReasonText.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showLateModal.value = false;
            lateReasonText.value = '';
            alert('Checked In Successfully!');
        }
    });
};

const handleCheckOut = () => {
    router.post(route('attendance.check-out'), {}, {
        preserveScroll: true,
        onSuccess: () => alert('Checked Out Successfully!')
    });
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>