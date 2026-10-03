<template>
    <div class="flex h-screen bg-gray-50 font-sans overflow-hidden">
        
        <!-- Mobile Sidebar Backdrop -->
        <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black/50 transition-opacity lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 text-white h-full flex flex-col shadow-2xl transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto">
            
            <!-- Logo Area -->
            <div class="flex items-center justify-between h-20 px-6 border-b border-gray-800">
                <span class="text-2xl font-extrabold tracking-wider text-blue-500">Blu<span class="text-white">HRM</span></span>
                <!-- Close Button for Mobile -->
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <!-- 🟢 সুপার অ্যাডমিন ব্যানার: শুধু তখনই দেখাবে যখন সে কোনো কোম্পানি ম্যানেজ করছে -->
            <div v-if="$page.props.active_company && (hasRole('super_admin') || hasRole('Super Admin'))" class="mx-4 mt-6 p-4 bg-indigo-900 rounded-xl border border-indigo-700 shadow-inner">
                <div class="text-[10px] uppercase tracking-wider text-indigo-300 font-bold mb-1">
                    Currently Managing
                </div>
                <div class="text-white font-bold text-sm truncate mb-3">
                    {{ $page.props.active_company.name }}
                </div>
                
                <!-- Exit Button -->
                <Link :href="route('companies.exit')" method="post" as="button" class="w-full bg-red-500 hover:bg-red-600 text-white text-xs font-bold py-2 px-3 rounded shadow transition-colors text-center flex justify-center items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Exit Company
                </Link>
            </div>
            
            <!-- Navigation Links -->
            <div class="overflow-y-auto overflow-x-hidden flex-grow custom-scrollbar">
                <ul class="flex flex-col py-6 space-y-1">
                    <li class="px-6 mb-2 mt-2">
                        <div class="text-xs font-semibold tracking-widest text-gray-400 uppercase">Main Menu</div>
                    </li>
                    
                    <!-- Dashboard -->
                    <li>
                        <Link :href="route('dashboard')" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span class="ml-3 text-sm font-medium">Dashboard</span>
                        </Link>
                    </li>

                    <!-- =======================================
                         ১. Master Access (Super Admin) Menu 
                    ======================================== -->
                    <template v-if="hasRole('super_admin') || hasRole('Super Admin')">
                        <li class="px-6 mb-2 mt-6"><div class="text-xs font-semibold text-gray-500 uppercase">Master Access</div></li>
                        
                        <!-- Companies -->
                        <li>
                            <Link :href="route('companies.index')" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="ml-3 text-sm font-medium">Companies</span>
                            </Link>
                        </li>
                        
                        <!-- Subscriptions -->
                        <li>
                            <a href="#" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                <span class="ml-3 text-sm font-medium">Subscriptions</span>
                            </a>
                        </li>
                        
                        <!-- System Settings -->
                        <li>
                            <a href="#" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                                <span class="ml-3 text-sm font-medium">System Settings</span>
                            </a>
                        </li>
                    </template>

                   <!-- =======================================
                        ২. Company Admin Menu
                    ======================================== -->
                   <template v-if="hasRole('Company Admin') || ((hasRole('super_admin') || hasRole('Super Admin')) && $page.props.active_company)">
                        <li class="px-6 mb-2 mt-6"><div class="text-xs font-semibold text-gray-500 uppercase">Recruitment</div></li>
                        
                        <!-- Applications Link -->
                        <li>
                            <Link :href="route('applications.index')" :class="{'bg-gray-800 text-white border-blue-500': route().current('applications.index')}" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="ml-3 text-sm font-medium">Job Applications</span>
                            </Link>
                        </li>
                        
                        <li class="px-6 mb-2 mt-4"><div class="text-xs font-semibold text-gray-500 uppercase">HR Management</div></li>
                        
                        <!-- Employees Link -->
                        <li>
                            <Link :href="route('employees.index')" :class="{'bg-gray-800 text-white border-blue-500': route().current('employees.index')}" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span class="ml-3 text-sm font-medium">Employees</span>
                            </Link>
                        </li>

                        <!-- Job Positions Link -->
                        <li>
                            <Link :href="route('positions.index')" :class="{'bg-gray-800 text-white border-blue-500': route().current('positions.index')}" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="ml-3 text-sm font-medium">Job Positions</span>
                            </Link>
                        </li>

                        <!-- Daily Attendance Link -->
                        <li>
                            <Link :href="route('company.attendance.today')" :class="{'bg-gray-800 text-white border-blue-500': route().current('company.attendance.today')}" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="ml-3 text-sm font-medium">Daily Attendance</span>
                            </Link>
                        </li>

                        <!-- Leave Requests Link (Admin View) -->
                        <li>
                            <Link :href="route('leaves.index')" :class="{'bg-gray-800 text-white border-blue-500': route().current('leaves.index')}" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="ml-3 text-sm font-medium">Leave Requests</span>
                            </Link>
                        </li>
                    </template>

                    <!-- =======================================
                        ৩. Employee Menu (শুধু এমপ্লয়িরা দেখবে)
                    ======================================== -->
                    <template v-if="hasRole('Employee')">
                        <li class="px-6 mb-2 mt-6"><div class="text-xs font-semibold text-gray-500 uppercase">My Workspace</div></li>
                            
                       <!-- My Attendance -->
                        <li>
                            <Link :href="route('my-attendance')" :class="{'bg-gray-800 text-white border-blue-500': route().current('my-attendance')}" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="ml-3 text-sm font-medium">My Attendance</span>
                            </Link>
                        </li>

                        <!-- Leave & Holidays (Employee View) -->
                        <li>
                            <Link :href="route('leaves.index')" :class="{'bg-gray-800 text-white border-blue-500': route().current('leaves.index')}" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="ml-3 text-sm font-medium">Leave & Holidays</span>
                            </Link>
                        </li>
                        
                        <!-- My Documents -->
                        <li>
                            <Link href="#" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="ml-3 text-sm font-medium">My Documents</span>
                            </Link>
                        </li>
                    </template>

                </ul>
            </div>
            
            <!-- User Profile & Logout (🟢 FIXED: Added safe optional chaining) -->
            <div class="p-5 border-t border-gray-800 bg-gray-950">
                <div class="flex items-center mb-4">
                    <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white uppercase shadow-lg">
                        {{ $page.props.auth?.user?.name?.charAt(0) || 'U' }}
                    </div>
                    <div class="ml-3 overflow-hidden">
                        <p class="text-sm font-medium text-white truncate">{{ $page.props.auth?.user?.name || 'Guest User' }}</p>
                        <p class="text-xs text-gray-400 mt-1 truncate">{{ userRoleName }}</p>
                    </div>
                </div>
                <Link :href="route('logout')" method="post" as="button" class="w-full py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded transition-colors shadow">
                    Logout
                </Link>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col h-screen overflow-y-auto">
            
            <!-- Header with Mobile Hamburger Toggle Button -->
            <header class="bg-white shadow-sm sticky top-0 z-10 flex items-center justify-between px-6 lg:px-8 h-20">
                <div class="flex items-center gap-4">
                    <!-- Hamburger Toggle Button (Mobile Only) -->
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 lg:hidden focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    
                    <div class="text-xl font-bold text-gray-800">
                        <slot name="header">Dashboard</slot>
                    </div>
                </div>
            </header>
            
            <div class="p-6 lg:p-8">
                <slot />
            </div>
        </main>
        
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const sidebarOpen = ref(false); // Mobile sidebar toggle state

const hasRole = (roleName) => {
    const roles = page.props.auth?.user?.roles;
    
    // রোল না থাকলে সরাসরি false
    if (!roles) return false;
    
    // ব্যাকএন্ড থেকে Object বা Array যেভাবেই আসুক, সেটাকে পিওর Array-তে কনভার্ট করা
    const roleArray = Array.isArray(roles) ? roles : Object.values(roles);
    
    // কেস-ইনসেনসিটিভ চেক (যাতে 'super_admin' আর 'Super Admin' দুটোর জন্যই কাজ করে)
    return roleArray.some(r => String(r).toLowerCase() === String(roleName).toLowerCase());
};

// Profile Role Name (🟢 FIXED: Added safe optional chaining)
const userRoleName = computed(() => {
    const roles = page.props.auth?.user?.roles || [];
    const roleArray = Object.values(roles);
    return roleArray.length > 0 ? roleArray[0] : 'User';
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #4b5563; }
</style>