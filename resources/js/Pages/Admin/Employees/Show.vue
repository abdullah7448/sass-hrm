<template>
    <AdminLayout>
        <template #header>
            Employee Digital Profile
        </template>

        <div class="mt-4 max-w-6xl mx-auto">
            
            <!-- 🟢 Top Header Actions (Mobile Friendly) -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <Link :href="route('employees.index')" class="flex items-center text-gray-500 hover:text-blue-600 transition font-medium text-sm bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to List
                </Link>
                <button @click="confirmTerminate" class="w-full sm:w-auto px-5 py-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-200 hover:border-red-600 text-sm font-bold rounded-lg shadow-sm transition-colors flex justify-center items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path></svg>
                    Terminate Employee
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                
                <!-- 🟢 Left Sidebar: Fixed Profile Card -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 text-center border-b border-gray-100 bg-gradient-to-b from-gray-50 to-white">
                            <div class="w-24 h-24 mx-auto rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-4xl border-4 border-white shadow-md mb-4 uppercase">
                                {{ employee.name.charAt(0) }}
                            </div>
                            <h2 class="text-xl font-extrabold text-gray-900">{{ employee.name }}</h2>
                            <p class="text-blue-600 font-bold text-sm mt-1">
                                {{ candidateData?.position || 'Employee' }}
                            </p>
                            <span class="inline-block mt-3 px-4 py-1.5 bg-green-100 text-green-700 text-xs font-black uppercase tracking-wider rounded-full shadow-sm">Active Status</span>
                        </div>
                        
                        <!-- Quick Contact Info -->
                        <div class="p-6 space-y-4 bg-white">
                            <div class="flex items-start gap-3">
                                <div class="mt-1 p-2 bg-gray-50 text-gray-400 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div>
                                <div class="overflow-hidden">
                                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Email</p>
                                    <p class="text-sm font-medium text-gray-800 mt-0.5 truncate">{{ employee.email }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="mt-1 p-2 bg-gray-50 text-gray-400 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg></div>
                                <div>
                                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Phone</p>
                                    <p class="text-sm font-medium text-gray-800 mt-0.5">{{ employee.phone || candidateData?.phone || 'Not Provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🟢 Right Content: Tabbed Interface -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        
                        <!-- Tabs Navigation (Mobile Scrollable) -->
                        <div class="border-b border-gray-200 bg-gray-50">
                            <nav class="flex overflow-x-auto custom-scrollbar" aria-label="Tabs">
                                <button 
                                    v-for="tab in tabs" 
                                    :key="tab.id"
                                    @click="activeTab = tab.id"
                                    :class="[
                                        activeTab === tab.id 
                                            ? 'border-blue-500 text-blue-600 bg-white' 
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                        'whitespace-nowrap py-4 px-6 border-b-2 font-bold text-sm transition-colors flex items-center gap-2 outline-none'
                                    ]"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="tab.icon"></svg>
                                    {{ tab.name }}
                                </button>
                            </nav>
                        </div>

                       <!-- 🟢 TAB 1: Personal Info & Salary Setup -->
                        <div v-show="activeTab === 'personal'" class="p-6 md:p-8 animate-fade-in space-y-8">
                            
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Personal & Job Information</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Full Name</p>
                                        <p class="font-medium text-gray-900">{{ employee.name }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Date of Joining</p>
                                        <p class="font-medium text-gray-900">{{ formatDate(employee.created_at) }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Designation / Role</p>
                                        <p class="font-medium text-gray-900">{{ candidateData?.position || 'Employee' }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">System Role</p>
                                        <p class="font-medium text-blue-600 uppercase">{{ employee.role || 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- 🟢 Admin Salary & Shift Configuration Form -->
                            <div class="border-t border-gray-100 pt-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Salary & Shift Configuration (Admin Only)
                                </h3>
                                
                                <form @submit.prevent="updateEmployeeProfile" class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-2xl border border-gray-200">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Basic Salary (BDT)</label>
                                        <input 
                                            type="number" 
                                            v-model="profileForm.basic_salary" 
                                            class="w-full border-gray-300 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                                            placeholder="E.g., 25000"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Assigned Shift</label>
                                        <select 
                                            v-model="profileForm.shift_type" 
                                            class="w-full border-gray-300 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                                        >
                                            <option value="morning">Morning Shift (9:00 AM)</option>
                                            <option value="evening">Evening Shift (2:00 PM)</option>
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2 flex justify-end">
                                        <button 
                                            type="submit" 
                                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow transition"
                                            :disabled="profileForm.processing"
                                        >
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>

                        <!-- 🟢 TAB 2: Documents -->
                        <div v-show="activeTab === 'documents'" class="p-6 md:p-8 animate-fade-in">
                            <h3 class="text-lg font-bold text-gray-900 mb-6">Recruitment Documents</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- NID Card -->
                                <div class="border border-gray-200 rounded-xl p-4 bg-gray-50 flex items-start gap-4 hover:shadow-md transition">
                                    <div class="p-3 bg-white rounded-lg shadow-sm border border-gray-100 text-blue-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">National ID Card</p>
                                        <a v-if="employee.nid_path || candidateData?.nid_path" :href="`/storage/${employee.nid_path || candidateData.nid_path}`" target="_blank" class="text-xs text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded font-bold mt-2 inline-block transition">View Document</a>
                                        <p v-else class="text-xs text-red-500 font-bold mt-2 bg-red-50 px-2 py-1 rounded inline-block">Not Uploaded</p>
                                    </div>
                                </div>
                                
                                <!-- Certificate/CV -->
                                <div class="border border-gray-200 rounded-xl p-4 bg-gray-50 flex items-start gap-4 hover:shadow-md transition">
                                    <div class="p-3 bg-white rounded-lg shadow-sm border border-gray-100 text-indigo-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">Certificate / CV</p>
                                        <a v-if="employee.certificate_path || candidateData?.certificate_path" :href="`/storage/${employee.certificate_path || candidateData.certificate_path}`" target="_blank" class="text-xs text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded font-bold mt-2 inline-block transition">View Document</a>
                                        <p v-else class="text-xs text-red-500 font-bold mt-2 bg-red-50 px-2 py-1 rounded inline-block">Not Uploaded</p>
                                    </div>
                                </div>

                                <!-- Resume -->
                                <div class="border border-gray-200 rounded-xl p-4 bg-gray-50 flex items-start gap-4 hover:shadow-md transition">
                                    <div class="p-3 bg-white rounded-lg shadow-sm border border-gray-100 text-green-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">Resume File</p>
                                        <a v-if="employee.resume_path || candidateData?.resume_path" :href="`/storage/${employee.resume_path || candidateData.resume_path}`" target="_blank" class="text-xs text-white bg-green-600 hover:bg-green-700 px-3 py-1.5 rounded font-bold mt-2 inline-block transition">View Document</a>
                                        <p v-else class="text-xs text-red-500 font-bold mt-2 bg-red-50 px-2 py-1 rounded inline-block">Not Uploaded</p>
                                    </div>
                                </div>

                                <!-- Portfolio Link -->
                                <div v-if="candidateData?.portfolio" class="border border-gray-200 rounded-xl p-4 bg-gray-50 flex flex-col justify-center hover:shadow-md transition">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        <span class="font-bold text-gray-800 text-sm">Portfolio Link</span>
                                    </div>
                                    <a :href="candidateData.portfolio" target="_blank" class="text-sm text-blue-600 hover:underline truncate">{{ candidateData.portfolio }}</a>
                                </div>
                            </div>
                        </div>

                        <!-- 🟢 TAB 3: Attendance Tracker -->
                        <div v-show="activeTab === 'attendance'" class="animate-fade-in">
                            <div class="p-6 md:p-8">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                                    <h3 class="text-lg font-bold text-gray-900">Attendance Tracker</h3>
                                    
                                    <!-- Filter Tool -->
                                    <div class="flex items-center gap-3 bg-gray-50 p-2 rounded-lg border border-gray-200">
                                        <input 
                                            type="month" 
                                            v-model="selectedMonthYear" 
                                            class="border-none bg-transparent text-sm focus:ring-0 text-gray-700 font-medium"
                                        >
                                        <button 
                                            @click="applyFilter" 
                                            class="px-4 py-1.5 bg-blue-600 text-white text-sm font-bold rounded hover:bg-blue-700 transition shadow-sm"
                                        >
                                            Filter
                                        </button>
                                    </div>
                                </div>

                                <!-- Summary Mini-Cards -->
                                <div class="grid grid-cols-3 gap-4 mb-6">
                                    <div class="p-4 bg-green-50 rounded-xl border border-green-100 text-center">
                                        <h4 class="text-2xl font-black text-green-600">{{ attSummary.present }}</h4>
                                        <p class="text-xs font-bold text-green-800 uppercase mt-1">Present</p>
                                    </div>
                                    <div class="p-4 bg-yellow-50 rounded-xl border border-yellow-100 text-center">
                                        <h4 class="text-2xl font-black text-yellow-600">{{ attSummary.late }}</h4>
                                        <p class="text-xs font-bold text-yellow-800 uppercase mt-1">Late</p>
                                    </div>
                                    <div class="p-4 bg-red-50 rounded-xl border border-red-100 text-center">
                                        <h4 class="text-2xl font-black text-red-500">{{ attSummary.absent }}</h4>
                                        <p class="text-xs font-bold text-red-800 uppercase mt-1">Absent</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendance Table -->
                            <div class="overflow-x-auto max-h-96 custom-scrollbar border-t border-gray-100">
                                <table class="w-full text-left border-collapse">
                                    <thead class="sticky top-0 bg-gray-50 shadow-sm z-10">
                                        <tr class="border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                                            <th class="p-4 font-bold">Date</th>
                                            <th class="p-4 font-bold">Check In</th>
                                            <th class="p-4 font-bold">Check Out</th>
                                            <th class="p-4 font-bold text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <tr v-for="att in attendances" :key="att.id" class="hover:bg-blue-50/50 transition">
                                            <td class="p-4 text-sm font-medium text-gray-900">{{ att.date }}</td>
                                            <td class="p-4 text-sm text-gray-600 font-mono">{{ att.check_in || '--:--' }}</td>
                                            <td class="p-4 text-sm text-gray-600 font-mono">{{ att.check_out || '--:--' }}</td>
                                            <td class="p-4 text-center">
                                                <span v-if="att.status === 'present'" class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded capitalize">Present</span>
                                                <span v-else-if="att.status === 'late'" class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded capitalize">Late</span>
                                                <span v-else-if="att.status === 'absent'" class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded capitalize">Absent</span>
                                                <span v-else class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded capitalize">{{ att.status }}</span>
                                            </td>
                                        </tr>
                                        <tr v-if="attendances.length === 0">
                                            <td colspan="4" class="p-12 text-center flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <p class="text-gray-500 font-medium">No attendance records found for {{ attSummary.month_name }}.</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 🟢 TAB 4: Payroll & Pay Slip (Dynamic) -->
                        <div v-show="activeTab === 'payroll'" class="p-6 md:p-8 animate-fade-in space-y-6">
                            
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Salary & Pay Slip</h3>
                                    <p class="text-xs text-gray-500">Calculated for: {{ payroll?.month_name }}</p>
                                </div>
                                <button 
                                    @click="printPaySlip" 
                                    class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold text-sm rounded-xl shadow transition flex items-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Print Pay Slip
                                </button>
                            </div>

                            <!-- Pay Slip Card Preview (Dynamic Data) -->
                            <div id="printable-payslip" class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm space-y-6">
                                <div class="flex justify-between items-start border-b border-gray-100 pb-6">
                                    <div>
                                        <h2 class="text-2xl font-black text-blue-600">COMPANY PAY SLIP</h2>
                                        <p class="text-xs text-gray-400 mt-1">Statement for {{ payroll?.month_name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-800">{{ employee.name }}</p>
                                        <p class="text-xs text-gray-500">ID: #EMP-{{ employee.id }}</p>
                                        <p class="text-xs text-blue-600 font-semibold mt-1">Shift: {{ employee.shift_type || 'Morning' }}</p>
                                    </div>
                                </div>

                                <!-- Salary Breakdown Table -->
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg text-sm">
                                        <span class="font-medium text-gray-600">Full Monthly Basic Salary</span>
                                        <span class="font-bold text-gray-900">৳ {{ payroll?.basic_salary || 0 }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg text-sm text-blue-800">
                                        <span class="font-medium">Earned Basic (Present Days: {{ payroll?.total_working_days }} Days)</span>
                                        <span class="font-bold">+ ৳ {{ payroll?.earned_basic || 0 }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg text-sm text-green-700">
                                        <span class="font-medium">Attendance Bonus</span>
                                        <span class="font-bold">+ ৳ {{ payroll?.attendance_bonus || 0 }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg text-sm text-red-600">
                                        <span class="font-medium">Late Penalty Deduction (Unapproved Lates: {{ payroll?.unapproved_late }})</span>
                                        <span class="font-bold">- ৳ {{ payroll?.late_deduction || 0 }}</span>
                                    </div>
                                </div>

                                <!-- Net Pay Box -->
                                <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl text-white flex justify-between items-center">
                                    <div>
                                        <p class="text-xs uppercase tracking-wider font-bold text-blue-100">Net Payable Salary</p>
                                        <h4 class="text-3xl font-black mt-1">৳ {{ payroll?.net_salary || 0 }}</h4>
                                    </div>
                                    <div class="text-right text-xs text-blue-100">
                                        <p>Authorized Signature</p>
                                        <div class="mt-8 border-t border-white/40 pt-1 w-32">Management</div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    employee: Object,
    candidateData: Object,
    attendances: Array,
    attSummary: Object,
    filterDate: String,
    payroll: Object,
});

// Profile Update Form State (প্রপস থেকে সঠিক ভ্যালু বাইন্ড করা হলো)
const profileForm = useForm({
    name: props.employee.name || '',
    phone: props.employee.phone || '',
    basic_salary: props.employee.basic_salary || '',
    shift_type: props.employee.shift_type || 'morning',
});

const updateEmployeeProfile = () => {
    profileForm.put(route('employees.update', props.employee.id), {
        preserveScroll: true,
        onSuccess: () => {
            // সফলভাবে সেভ হলে সুন্দর অ্যালার্ট দেখাবে
            alert('Employee Profile, Salary & Shift Updated Successfully!');
        },
        onError: (errors) => {
            console.error(errors);
            alert('Failed to update. Please check the fields.');
        }
    });
};

// 🟢 Tab State Management
const activeTab = ref('personal');

const tabs = [
    { id: 'personal', name: 'Personal Info', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />' },
    { id: 'documents', name: 'Documents', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />' },
    { id: 'attendance', name: 'Attendance', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />' },
    { id: 'payroll', name: 'Payroll', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />' },
];

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

// Attendance Filter Logic
const selectedMonthYear = ref(props.filterDate);

const applyFilter = () => {
    router.get(route('employees.show', props.employee.id), { 
        month_year: selectedMonthYear.value 
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const printPaySlip = () => {
    const printContent = document.getElementById('printable-payslip').innerHTML;
    const originalContent = document.body.innerHTML;

    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    window.location.reload(); // রিলোড দিয়ে আগের স্টেট ফিরিয়ে আনা
};
</script>

<style scoped>
/* Custom Scrollbar for Tables & Tabs */
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

/* Hide scrollbar for Tabs on mobile but keep functionality */
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

/* Smooth fade in animation for tabs */
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>