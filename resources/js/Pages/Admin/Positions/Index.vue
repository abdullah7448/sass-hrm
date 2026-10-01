<template>
    <AdminLayout>
        <template #header>
            Job Positions & Assessments
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-4">
            <!-- Header Actions -->
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-700">Manage Positions</h3>
                <form @submit.prevent="submitNewPosition" class="flex items-center space-x-2">
                    <input v-model="form.title" type="text" placeholder="e.g. Graphic Designer" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 sm:text-sm" required />
                    <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700">
                        + Add Position
                    </button>
                </form>
            </div>

            <!-- Apply URL Section (New Feature) -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mt-4 flex flex-col md:flex-row items-center justify-between shadow-sm">
            <div class="mb-4 md:mb-0 text-center md:text-left">
                <h4 class="text-blue-900 font-extrabold text-lg flex items-center justify-center md:justify-start gap-2">
                    🔗 Public Application Link
                </h4>
                <p class="text-blue-700 text-sm mt-1">Share this unique link on Facebook or Job Portals to collect candidates.</p>
            </div>
            <div class="flex items-center gap-2 w-full md:w-auto">
                <input type="text" readonly :value="applyUrl" class="bg-white border border-blue-300 text-gray-700 text-sm font-medium rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-80 p-2.5 outline-none cursor-text shadow-inner" />
                <button @click="copyLink" :class="copied ? 'bg-green-500 hover:bg-green-600' : 'bg-blue-600 hover:bg-blue-700'" class="px-5 py-2.5 text-white font-bold text-sm rounded-lg transition-all shadow-md whitespace-nowrap flex items-center gap-1">
                    <span v-if="copied">✓ Copied</span>
                    <span v-else>Copy Link</span>
                </button>
            </div>
        </div>

            <!-- Positions Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Position Title</th>
                            <th class="p-4 font-semibold text-center">Status</th>
                            <th class="p-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="position in positions" :key="position.id">
                            <td class="p-4 font-bold text-gray-900 text-sm">{{ position.title }}</td>
                            <td class="p-4 text-center">
                                <button @click="toggleStatus(position)" :class="position.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-3 py-1 rounded-full text-xs font-bold">
                                    {{ position.is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="p-4 text-right space-x-3">
                                <!-- Manage Questions Button -->
                                <button @click="openQuestionModal(position)" class="text-blue-600 hover:text-blue-900 text-sm font-bold bg-blue-50 px-4 py-2 rounded shadow-sm border border-blue-100">
                                    Manage Questions
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- Question Builder Modal (Tabs & HTML Support) -->
        <!-- ========================================== -->
        <div v-if="showQModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-5xl max-h-[95vh] flex flex-col overflow-hidden">
                
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Assessment Builder</h3>
                        <p class="text-sm text-gray-500">Managing questions for: <span class="font-bold text-blue-600">{{ selectedPos.title }}</span></p>
                    </div>
                    <button @click="closeQuestionModal" class="text-gray-400 hover:text-red-500 text-3xl font-bold">&times;</button>
                </div>

                <!-- Tabs (Office Rules removed) -->
                <div class="flex border-b border-gray-200 bg-white">
                    <button @click="qTab = 'iq'" :class="{'border-blue-500 text-blue-600 border-b-2': qTab === 'iq'}" class="px-6 py-3 font-semibold text-sm">IQ Test (Global)</button>
                    <button @click="qTab = 'departmental'" :class="{'border-blue-500 text-blue-600 border-b-2': qTab === 'departmental'}" class="px-6 py-3 font-semibold text-sm">Departmental ({{ selectedPos.title }})</button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 bg-gray-100 flex gap-6">
                    
                    <!-- Left Column: Add New Question Form -->
                    <div class="w-1/2 bg-white p-5 rounded-lg shadow-sm border border-gray-200 self-start">
                        <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">Create New Question</h4>
                        <form @submit.prevent="submitQuestion">
                            
                            <!-- Question Type Selector -->
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Answer Type</label>
                                <select v-model="qForm.type" class="w-full border-gray-300 rounded-md focus:ring-blue-500 text-sm font-medium bg-gray-50">
                                    <option value="mcq">Multiple Choice (Custom Options)</option>
                                    <option value="yes_no">Yes / No Question</option>
                                    <option value="text">Written Answer (Text Box)</option>
                                </select>
                            </div>

                            <!-- Question Input (HTML Supported) -->
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Question (Supports HTML)</label>
                                <textarea v-model="qForm.question_text" rows="2" placeholder="e.g. <div class='text-red-500'>Write your answer...</div>" class="w-full border-gray-300 rounded-md focus:ring-blue-500 text-sm"></textarea>
                                <!-- Live Preview -->
                                <div v-if="qForm.question_text" class="mt-2 p-3 bg-gray-50 border border-dashed border-gray-300 rounded text-sm">
                                    <span class="text-xs text-gray-400 block mb-1">Question Preview:</span>
                                    <div v-html="qForm.question_text"></div>
                                </div>
                            </div>

                            <!-- Conditional Options Input (Only for MCQ) -->
                            <div v-if="qForm.type === 'mcq'" class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Options (Supports HTML)</label>
                                <div v-for="(opt, index) in qForm.options" :key="index" class="flex gap-2 mb-2 items-start">
                                    <div class="flex-1">
                                        <input v-model="qForm.options[index]" type="text" placeholder="Option text or HTML" class="w-full border-gray-300 rounded-md text-sm" />
                                        <div v-if="qForm.options[index]" class="mt-1 p-2 bg-gray-50 border border-gray-200 rounded text-sm flex items-center min-h-[30px]">
                                            <div v-html="qForm.options[index]"></div>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeOption(index)" class="mt-1 px-3 py-2 bg-red-100 text-red-600 rounded hover:bg-red-200 font-bold">X</button>
                                </div>
                                <button type="button" @click="addOption" class="mt-1 text-sm text-blue-600 font-bold hover:underline">+ Add Option</button>
                            </div>

                            <!-- Information for Text or Yes/No -->
                            <div v-if="qForm.type === 'text'" class="mb-4 p-3 bg-blue-50 text-blue-700 rounded text-sm border border-blue-100">
                                📝 Candidates will see a multi-line text box to write their answer.
                            </div>
                            <div v-if="qForm.type === 'yes_no'" class="mb-4 p-3 bg-green-50 text-green-700 rounded text-sm border border-green-100">
                                🔘 Options will automatically be generated as "Yes" and "No".
                            </div>

                            <button type="submit" :disabled="qForm.processing" class="w-full py-2 bg-blue-600 text-white rounded font-bold hover:bg-blue-700 transition-colors">
                                Save Question
                            </button>
                        </form>
                    </div>

                    <!-- Right Column: Existing Questions List -->
                    <div class="w-1/2 space-y-4">
                        <h4 class="font-bold text-gray-800 mb-2">Existing Questions ({{ qTab }})</h4>
                        
                        <div v-for="q in filteredQuestions" :key="q.id" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                            <!-- Badge for Type -->
                            <span class="inline-block px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase rounded mb-2">
                                {{ q.type.replace('_', ' ') }}
                            </span>

                            <!-- Question Title -->
                            <div class="font-bold text-gray-900 mb-2" v-html="q.question_text"></div>
                            
                            <!-- Options Grid -->
                            <div v-if="q.options && q.type !== 'text'" class="grid grid-cols-2 gap-2 mt-2">
                                <div v-for="(opt, idx) in parseJSON(q.options)" :key="idx" class="p-2 bg-gray-50 border border-gray-100 rounded text-sm flex items-center gap-2">
                                    <span class="font-bold text-gray-400">{{ idx + 1 }}.</span>
                                    <span v-html="opt"></span>
                                </div>
                            </div>
                            <div v-if="q.type === 'text'" class="mt-2 p-3 border border-dashed border-gray-300 rounded bg-gray-50 text-gray-400 text-sm italic">
                                Textarea field will be rendered here.
                            </div>
                            
                            <div class="mt-3 text-right">
                                <!-- Global হলে ডিলিট বাটন হাইড, শুধু ব্যাজ দেখাবে -->
                                <span v-if="q.is_global" class="text-xs text-green-600 bg-green-100 px-3 py-1 rounded font-bold">
                                    Default Template
                                </span>
                                <!-- নিজস্ব প্রশ্ন হলে ডিলিট বাটন দেখাবে -->
                                <button v-else @click="deleteQuestion(q.id)" class="text-xs text-red-500 hover:underline font-semibold">
                                    Delete
                                </button>
                            </div>
                        </div>

                        <div v-if="filteredQuestions.length === 0" class="text-center p-8 text-gray-500 bg-white rounded-lg border border-dashed border-gray-300">
                            No questions added yet.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
    
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    positions: Array,
    questions: Array,
    applyUrl: String
});

// Copy to Clipboard Logic
const copied = ref(false);
const copyLink = () => {
    navigator.clipboard.writeText(props.applyUrl);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000); // ২ সেকেন্ড পর আবার 'Copy Link' হয়ে যাবে
};

// Position Form Logic
const form = useForm({ title: '' });
const submitNewPosition = () => {
    form.post(route('positions.store'), { onSuccess: () => form.reset() });
};
const toggleStatus = (position) => {
    router.put(route('positions.update', position.id), { is_active: !position.is_active }, { preserveScroll: true });
};

// ==============================
// Question Builder Logic
// ==============================
const showQModal = ref(false);
const selectedPos = ref(null);
const qTab = ref('iq');

const qForm = useForm({
    category: '',
    department: '',
    type: 'mcq',
    question_text: '',
    options: ['', ''] 
});

const openQuestionModal = (position) => {
    selectedPos.value = position;
    qTab.value = 'iq';
    showQModal.value = true;
};

const closeQuestionModal = () => {
    showQModal.value = false;
    qForm.reset();
};

const addOption = () => qForm.options.push('');
const removeOption = (index) => qForm.options.splice(index, 1);

// const submitQuestion = () => {
//     qForm.category = qTab.value;
//     qForm.department = qTab.value === 'iq' ? 'All' : selectedPos.value.title;
    
//     if (qForm.type === 'text') {
//         qForm.options = null;
//     } else if (qForm.type === 'yes_no') {
//         qForm.options = ['Yes', 'No'];
//     } else {
//         qForm.options = qForm.options.filter(opt => opt.trim() !== '');
//     }

//     qForm.post(route('questions.store'), {
//         onSuccess: () => {
//             qForm.question_text = '';
//             qForm.type = 'mcq';
//             qForm.options = ['', ''];
//         },
//         preserveScroll: true
//     });
// };
const submitQuestion = () => {
    qForm.category = qTab.value;
    // 🟢 ফিক্স: IQ হোক বা Departmental, সব সময় নির্দিষ্ট পজিশনের নামেই সেভ হবে
    qForm.department = selectedPos.value.title; 
    
    if (qForm.type === 'text') {
        qForm.options = null;
    } else if (qForm.type === 'yes_no') {
        qForm.options = ['Yes', 'No'];
    } else {
        qForm.options = qForm.options.filter(opt => opt.trim() !== '');
    }

    qForm.post(route('questions.store'), {
        onSuccess: () => {
            qForm.question_text = '';
            qForm.type = 'mcq';
            qForm.options = ['', ''];
        },
        preserveScroll: true
    });
};

const deleteQuestion = (id) => {
    if(confirm('Are you sure you want to delete this question?')) {
        router.delete(route('questions.destroy', id), { preserveScroll: true });
    }
};

const parseJSON = (data) => {
    try { return typeof data === 'string' ? JSON.parse(data) : data; } catch (e) { return []; }
};

// const filteredQuestions = computed(() => {
//     if (!props.questions) return [];
//     return props.questions.filter(q => {
//        // IQ ট্যাবে ক্লিক করলে কোম্পানির সব IQ + গ্লোবাল টেমপ্লেট দেখাবে
//         if (qTab.value === 'iq') return q.category === 'iq';
        
//         // Departmental ট্যাবে ক্লিক করলে শুধু ওই পজিশনের প্রশ্ন দেখাবে
//         return q.category === qTab.value && q.department === selectedPos.value.title;
//     });
// });

const filteredQuestions = computed(() => {
    if (!props.questions) return [];
    return props.questions.filter(q => {
        if (qTab.value === 'iq') {
            // 🟢 ফিক্স: শুধু গ্লোবাল IQ এবং এই নির্দিষ্ট পজিশনের কাস্টম IQ দেখাবে
            return q.category === 'iq' && (q.is_global || q.department === 'All' || q.department === selectedPos.value.title);
        }
        
        // Departmental ট্যাবে ক্লিক করলে শুধু ওই পজিশনের প্রশ্ন দেখাবে
        return q.category === qTab.value && q.department === selectedPos.value.title;
    });
});
</script>