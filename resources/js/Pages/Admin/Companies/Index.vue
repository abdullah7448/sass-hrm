<template>
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center w-full">
                <span>Companies / Clients</span>
                <!-- আগে শুধু <button> ছিল, এখন <Link> দিয়ে রাউট বসিয়ে দিন -->
                <Link :href="route('companies.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow transition-colors">
                    + Add New Company
                </Link>
            </div>
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Company Name</th>
                            <th class="p-4 font-semibold">Contact Info</th>
                            <th class="p-4 font-semibold">Joined Date</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="company in companies" :key="company.id" class="hover:bg-gray-50 transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-gray-900">{{ company.name }}</div>
                                <div class="text-xs text-gray-500 mt-1">ID: #{{ company.id }}</div>
                            </td>
                            <td class="p-4 text-sm text-gray-600">
                                <div>{{ company.email }}</div>
                                <div>{{ company.phone }}</div>
                            </td>
                            <td class="p-4 text-sm text-gray-600">
                                {{ new Date(company.created_at).toLocaleDateString() }}
                            </td>
                            <td class="p-4">
                                <span v-if="company.status === 'active'" class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Active</span>
                                <span v-else class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Inactive</span>
                            </td>
                            <!-- <td class="p-4 text-right space-x-3"> এর ভেতরের অংশটুকু এটি দিয়ে রিপ্লেস করুন -->
                            <td class="p-4 text-right space-x-3">
                                <Link :href="route('companies.edit', company.id)" class="text-blue-600 hover:text-blue-900 text-sm font-semibold">
                                    Edit
                                </Link>
                                
                                <Link :href="route('companies.destroy', company.id)" method="delete" as="button" class="text-red-500 hover:text-red-700 text-sm font-semibold" preserve-scroll>
                                    Delete
                                </Link>

                                <!-- কোম্পানির অ্যাকশন বাটনের জায়গায় এটি যুক্ত করুন -->
                                <Link 
                                    :href="route('companies.manage', company.id)" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-bold transition-colors"
                                >
                                    Login As / Manage
                                </Link>
                            </td>
                        </tr>
                        
                        <!-- Empty State -->
                        <tr v-if="companies.length === 0">
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                No companies found. Add a new company to get started.
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
    companies: Array
});
</script>