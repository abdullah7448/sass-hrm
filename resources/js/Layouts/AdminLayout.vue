<template>
    <div class="flex h-screen bg-gray-50 font-sans">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white fixed h-full flex flex-col shadow-2xl z-20">
            
            <!-- Logo Area -->
            <div class="flex items-center justify-center h-20 border-b border-gray-800">
                <span class="text-2xl font-extrabold tracking-wider text-blue-500">Blu<span class="text-white">HRM</span></span>
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
                    <template v-if="hasRole('Super Admin')">
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
                    <template v-if="hasRole('Company Admin')">
                        <li class="px-6 mb-2 mt-6"><div class="text-xs font-semibold text-gray-500 uppercase">Recruitment</div></li>
                        
                        <!-- Applications Link -->
                        <li>
                            <Link :href="route('applications.index')" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="ml-3 text-sm font-medium">Job Applications</span>
                            </Link>
                        </li>
                        
                        <li class="px-6 mb-2 mt-4"><div class="text-xs font-semibold text-gray-500 uppercase">HR Management</div></li>
                        
                        <!-- Employees Link -->
                        <li>
                            <a href="#" class="relative flex flex-row items-center h-12 hover:bg-gray-800 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 pl-4 transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span class="ml-3 text-sm font-medium">Employees</span>
                            </a>
                        </li>
                    </template>

                </ul>
            </div>
            
            <!-- User Profile & Logout -->
            <div class="p-5 border-t border-gray-800 bg-gray-950">
                <div class="flex items-center mb-4">
                    <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white uppercase shadow-lg">
                        {{ $page.props.auth.user.name.charAt(0) }}
                    </div>
                    <div class="ml-3 overflow-hidden">
                        <p class="text-sm font-medium text-white truncate">{{ $page.props.auth.user.name }}</p>
                        <p class="text-xs text-gray-400 mt-1 truncate">{{ userRoleName }}</p>
                    </div>
                </div>
                <Link :href="route('logout')" method="post" as="button" class="w-full py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded transition-colors shadow">
                    Logout
                </Link>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 ml-64 flex flex-col h-screen overflow-y-auto">
            
            <!-- এই হেডার অংশটুকু রিপ্লেস করুন -->
            <header class="bg-white shadow-sm sticky top-0 z-10 flex items-center px-8 h-20">
                <div class="text-xl font-bold text-gray-800 w-full">
                    <slot name="header">Dashboard</slot>
                </div>
            </header>
            
            <div class="p-8">
                <slot />
            </div>
        </main>
        
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

// Role Checker
const hasRole = (roleName) => {
    const roles = page.props.auth.user?.roles || [];
    return Object.values(roles).includes(roleName);
};

// Profile Role Name
const userRoleName = computed(() => {
    const roles = page.props.auth.user?.roles || [];
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