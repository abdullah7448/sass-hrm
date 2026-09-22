<template>
    <AdminLayout>
        <template #header>
            Job Applications
        </template>

        <!-- Filters Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mt-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                
                <!-- Search Filter (Name, Email, Phone) -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Search Candidate</label>
                    <input 
                        v-model="filters.search" 
                        type="text" 
                        placeholder="Search by name, email or phone..." 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                </div>

                <!-- Position Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Position</label>
                    <select v-model="filters.position" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Positions</option>
                        <option v-for="pos in availablePositions" :key="pos" :value="pos">{{ pos }}</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status</label>
                    <select v-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Statuses</option>
                        <option v-for="status in availableStatuses" :key="status" :value="status">{{ status }}</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Applied Date</label>
                    <div class="flex gap-2">
                        <input 
                            v-model="filters.date" 
                            type="date" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        />
                        <!-- Reset Filters Button -->
                        <button @click="resetFilters" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-md text-sm font-semibold transition" title="Clear Filters">
                            ✕
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Candidate Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Candidate Info</th>
                            <th class="p-4 font-semibold">Applied Position</th>
                            <th class="p-4 font-semibold">Applied Date</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- v-for এ এখন candidates এর বদলে filteredCandidates ব্যবহার করা হয়েছে -->
                        <tr v-for="candidate in filteredCandidates" :key="candidate.id" class="hover:bg-gray-50 transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-gray-900">{{ candidate.name }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ candidate.email }} | {{ candidate.phone }}</div>
                            </td>
                            <td class="p-4 text-sm font-semibold text-blue-600">
                                {{ candidate.position }}
                            </td>
                            <td class="p-4 text-sm text-gray-600 font-medium">
                                {{ formatDate(candidate.created_at) }}
                            </td>
                            <td class="p-4">
                                <span 
                                    class="px-3 py-1 rounded-full text-xs font-bold"
                                    :class="candidate.status === 'Approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                                >
                                    {{ candidate.status || 'Pending' }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-3">
                                <button @click="openModal(candidate)" class="text-blue-600 hover:text-blue-900 text-sm font-semibold bg-blue-50 px-3 py-1 rounded">
                                    View Details
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredCandidates.length === 0">
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <p class="font-medium text-gray-600">No matching applications found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Candidate Details Modal (আপনার আগের কোড হুবহু রাখা হয়েছে) -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ selectedCandidate.name }}'s Details</h3>
                        <p class="text-sm text-gray-500">Applying for: {{ selectedCandidate.position }}</p>
                    </div>
                    <button @click="closeModal" class="text-gray-400 hover:text-red-500 text-2xl font-bold">&times;</button>
                </div>

                <!-- Tabs Menu -->
                <div class="flex border-b border-gray-200 bg-white">
                    <button @click="activeTab = 'iq'" :class="{'border-blue-500 text-blue-600': activeTab === 'iq', 'text-gray-500 hover:text-gray-700': activeTab !== 'iq'}" class="px-6 py-3 border-b-2 font-semibold text-sm transition-colors">
                        IQ Test
                    </button>
                    <button @click="activeTab = 'departmental'" :class="{'border-blue-500 text-blue-600': activeTab === 'departmental', 'text-gray-500 hover:text-gray-700': activeTab !== 'departmental'}" class="px-6 py-3 border-b-2 font-semibold text-sm transition-colors">
                        Departmental
                    </button>
                    <button @click="activeTab = 'documents'" :class="{'border-blue-500 text-blue-600': activeTab === 'documents', 'text-gray-500 hover:text-gray-700': activeTab !== 'documents'}" class="px-6 py-3 border-b-2 font-semibold text-sm transition-colors">
                        Documents
                    </button>
                </div>

                <!-- Tabs Content (Scrollable) -->
                <div class="p-6 overflow-y-auto flex-1 bg-gray-50">
                    
                    <!-- IQ Tab -->
                    <div v-if="activeTab === 'iq'" class="space-y-4">
                        <h4 class="font-bold text-gray-700 mb-2 border-b pb-2">IQ Assessment Results</h4>
                        <div v-for="(item, index) in getAnswersByCategory('iq')" :key="index" class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <p class="font-bold text-gray-800 text-sm flex gap-2"><span>Q:</span> <span v-html="item.question"></span></p>
                            <p class="text-gray-600 mt-2 text-sm bg-gray-50 p-2 rounded">
                                Ans: <span class="font-semibold text-blue-600">{{ item.answer || 'Not answered' }}</span>
                            </p>
                        </div>
                        <div v-if="getAnswersByCategory('iq').length === 0" class="text-gray-500 italic text-center py-4">No IQ answers found.</div>
                    </div>

                    <!-- Departmental Tab -->
                    <div v-if="activeTab === 'departmental'" class="space-y-4">
                        <h4 class="font-bold text-gray-700 mb-2 border-b pb-2">Departmental Results ({{ selectedCandidate.position }})</h4>
                        <div v-for="(item, index) in getAnswersByCategory('departmental')" :key="index" class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <p class="font-bold text-gray-800 text-sm flex gap-2"><span>Q:</span> <span v-html="item.question"></span></p>
                            <p class="text-gray-600 mt-2 text-sm bg-gray-50 p-2 rounded">
                                Ans: <span class="font-semibold text-blue-600">{{ item.answer || 'Not answered' }}</span>
                            </p>
                        </div>
                        <div v-if="getAnswersByCategory('departmental').length === 0" class="text-gray-500 italic text-center py-4">No departmental answers found for this position.</div>
                    </div>

                    <!-- Documents Tab -->
                    <div v-if="activeTab === 'documents'" class="space-y-4">
                        <h4 class="font-bold text-gray-700 mb-2 border-b pb-2">Uploaded Documents</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- CV -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-gray-800">Resume / CV</p>
                                    <p class="text-xs text-gray-500 mt-1">PDF or Image Document</p>
                                </div>
                                <a v-if="selectedCandidate.resume_path" :href="'/storage/' + selectedCandidate.resume_path" target="_blank" class="px-4 py-2 bg-blue-100 text-blue-700 text-sm font-bold rounded hover:bg-blue-200">View</a>
                                <span v-else class="text-red-500 text-sm font-bold">Missing</span>
                            </div>

                            <!-- NID -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-gray-800">National ID</p>
                                    <p class="text-xs text-gray-500 mt-1">Identity Proof</p>
                                </div>
                                <a v-if="selectedCandidate.nid_path" :href="'/storage/' + selectedCandidate.nid_path" target="_blank" class="px-4 py-2 bg-blue-100 text-blue-700 text-sm font-bold rounded hover:bg-blue-200">View</a>
                                <span v-else class="text-red-500 text-sm font-bold">Missing</span>
                            </div>

                            <!-- Certificate -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-gray-800">Certificate</p>
                                    <p class="text-xs text-gray-500 mt-1">Educational Proof</p>
                                </div>
                                <a v-if="selectedCandidate.certificate_path" :href="'/storage/' + selectedCandidate.certificate_path" target="_blank" class="px-4 py-2 bg-blue-100 text-blue-700 text-sm font-bold rounded hover:bg-blue-200">View</a>
                                <span v-else class="text-red-500 text-sm font-bold">Missing</span>
                            </div>
                        </div>
                    </div>

                </div>
                
                <!-- Modal Footer Actions (Update this section in your Index.vue) -->
                <div class="p-4 border-t border-gray-200 bg-white flex justify-end items-center space-x-3">
                    <!-- Close Button -->
                    <button @click="closeModal" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded font-semibold text-sm">Close</button>
                    
                    <!-- Status Update Dropdown (New Feature) -->
                    <div v-if="selectedCandidate.status !== 'Approved'" class="flex items-center space-x-2 border-l pl-3 ml-3 border-gray-300">
                        <label class="text-xs font-bold text-gray-500 uppercase">Change Status:</label>
                        <select 
                            @change="updateStatus(selectedCandidate.id, $event.target.value)" 
                            class="px-3 py-1.5 border border-gray-300 rounded text-sm font-semibold bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="" disabled selected>Select Action</option>
                            <option value="Call for Interview">Call for Interview</option>
                            <option value="Hold">On Hold</option>
                            <option value="Rejected">Reject Candidate</option>
                        </select>
                    </div>

                    <!-- Approve Button (Only shows if not already approved) -->
                    <button v-if="selectedCandidate.status !== 'Approved'" @click="approveCandidate" class="px-4 py-2 text-white bg-green-500 hover:bg-green-600 rounded font-semibold text-sm">
                        Approve to Employee
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    candidates: { type: Array, default: () => [] },
    questions: { type: Array, default: () => [] }
});

// =====================================
// Search and Filter State
// =====================================
const filters = ref({
    search: '',
    position: '',
    status: '',
    date: ''
});

// Reset Filters Function
const resetFilters = () => {
    filters.value = { search: '', position: '', status: '', date: '' };
};

// Generate unique positions for dropdown dynamically
const availablePositions = computed(() => {
    const positions = props.candidates.map(c => c.position).filter(Boolean);
    return [...new Set(positions)]; // Remove duplicates
});

// Generate unique statuses for dropdown dynamically
const availableStatuses = computed(() => {
    const statuses = props.candidates.map(c => c.status || 'Pending').filter(Boolean);
    return [...new Set(statuses)];
});

// Date formatter (helper)
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toISOString().split('T')[0]; // Format: YYYY-MM-DD
};

// Computed property to instantly filter candidates
const filteredCandidates = computed(() => {
    return props.candidates.filter(candidate => {
        // 1. Search Logic (Name, Email, Phone)
        const searchTerm = filters.value.search.toLowerCase();
        const matchesSearch = !searchTerm || 
            candidate.name.toLowerCase().includes(searchTerm) || 
            (candidate.email && candidate.email.toLowerCase().includes(searchTerm)) || 
            (candidate.phone && candidate.phone.toLowerCase().includes(searchTerm));
        
        // 2. Position Logic
        const matchesPosition = !filters.value.position || candidate.position === filters.value.position;
        
        // 3. Status Logic
        const currentStatus = candidate.status || 'Pending';
        const matchesStatus = !filters.value.status || currentStatus === filters.value.status;
        
        // 4. Date Logic
        let matchesDate = true;
        if (filters.value.date && candidate.created_at) {
            const candidateDate = formatDate(candidate.created_at);
            matchesDate = candidateDate === filters.value.date;
        }

        return matchesSearch && matchesPosition && matchesStatus && matchesDate;
    });
});

// =====================================
// Modal State and Logic (আগের মতোই)
// =====================================
const showModal = ref(false);
const selectedCandidate = ref(null);
const activeTab = ref('iq');

const openModal = (candidate) => {
    selectedCandidate.value = candidate;
    activeTab.value = 'iq';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedCandidate.value = null;
};

const getAnswersByCategory = (category) => {
    if (!selectedCandidate.value || !selectedCandidate.value.assessment) return [];
    
    let rawAnswers = selectedCandidate.value.assessment.answers;
    let parsedAnswers = typeof rawAnswers === 'string' ? JSON.parse(rawAnswers) : rawAnswers;

    const filteredQuestions = props.questions.filter(q => {
        if (category === 'iq') return q.category === 'iq';
        if (category === 'departmental') return q.category === 'departmental' && q.department === selectedCandidate.value.position;
        return false;
    });
    
    return filteredQuestions.map(q => {
        return {
            question: q.question_text || q.title || 'Unknown Question', 
            answer: parsedAnswers ? parsedAnswers[q.id] : null
        }
    });
}

const approveCandidate = () => {
    if (confirm(`Are you sure you want to approve ${selectedCandidate.value.name} as an Employee?`)) {
        router.post(route('applications.approve', selectedCandidate.value.id), {}, {
            onSuccess: () => {
                closeModal();
            }
        });
    }
};

// Function to update candidate status (e.g., Call for Interview, Rejected)
const updateStatus = (id, newStatus) => {
    if (confirm(`Are you sure you want to mark this candidate as "${newStatus}"?`)) {
        router.post(route('applications.update_status', id), { status: newStatus }, {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            }
        });
    }
};
</script>