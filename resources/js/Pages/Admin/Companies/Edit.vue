<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('companies.index')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </Link>
                <span>Edit Company: {{ company.name }}</span>
            </div>
        </template>

        <div class="max-w-3xl bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-4">
            <div class="p-8">
                <form @submit.prevent="submit">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Company Name <span class="text-red-500">*</span></label>
                        <input v-model="form.name" type="text" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                            <input v-model="form.email" type="email" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                            <input v-model="form.phone" type="text" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select v-model="form.status" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <Link :href="route('companies.index')" class="px-6 py-3 text-gray-500 hover:text-gray-700 font-medium mr-4">Cancel</Link>
                        <button type="submit" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold shadow transition-colors disabled:opacity-50">
                            Update Company
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    company: Object
});

const form = useForm({
    name: props.company.name,
    email: props.company.email,
    phone: props.company.phone || '',
    status: props.company.status,
});

const submit = () => {
    form.put(route('companies.update', props.company.id));
};
</script>