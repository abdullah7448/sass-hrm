<template>
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header Section -->
            <div class="bg-blue-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">{{ step_title || 'Assessment Test' }}</h2>
                <p class="text-blue-100 text-sm mt-1">Please answer the following questions. Your progress is auto-saved.</p>
            </div>
            
            <form @submit.prevent="submitAnswers" class="p-6">
                
                <!-- Questions Loop -->
                <div v-for="(q, index) in questions" :key="q.id" class="mb-8 bg-gray-50 p-5 rounded-lg border border-gray-100">
                    
                    <!-- Question Title with HTML Support -->
                    <div class="font-bold text-gray-800 text-lg mb-4 flex gap-2">
                        <span>{{ index + 1 }}.</span> 
                        <span v-html="q.question_text"></span>
                    </div>

                    <!-- Answer Type: MCQ or Yes/No (Radio Buttons) -->
                    <div v-if="q.type === 'mcq' || q.type === 'yes_no'" class="space-y-3 pl-5">
                        <label v-for="(opt, idx) in parseOptions(q.options)" :key="idx" class="flex items-start cursor-pointer hover:bg-gray-100 p-2 rounded transition-colors">
                            <input 
                                type="radio" 
                                :name="'question_'+q.id" 
                                :value="opt" 
                                v-model="form.answers[q.id]" 
                                @change="saveProgress"
                                class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                            />
                            <span class="ml-3 text-gray-700" v-html="opt"></span>
                        </label>
                    </div>

                    <!-- Answer Type: Written Text (Textarea) -->
                    <div v-if="q.type === 'text'" class="pl-5">
                        <textarea 
                            v-model="form.answers[q.id]" 
                            @input="saveProgress"
                            rows="4" 
                            placeholder="Type your answer here..." 
                            class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 p-3 shadow-sm"
                        ></textarea>
                    </div>
                </div>

                <!-- Empty State (No questions found) -->
                <div v-if="questions.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="font-medium text-gray-600">No questions found for this phase.</p>
                    <p class="text-sm mt-1">You can skip this step and proceed to the next phase.</p>
                </div>

                <!-- Error Messages from Server -->
                <div v-if="Object.keys(form.errors).length > 0" class="mb-4 p-3 bg-red-50 text-red-600 rounded text-sm font-semibold border border-red-100">
                    Please answer all the required questions before continuing.
                </div>

                <!-- Action Button -->
                <div class="mt-6 flex justify-end">
                    <button 
                        type="submit" 
                        :disabled="form.processing" 
                        class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-md disabled:opacity-50"
                    >
                        {{ questions.length === 0 ? 'Skip & Continue' : 'Submit & Continue' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    questions: {
        type: Array,
        default: () => []
    },
    step_title: {
        type: String,
        default: ''
    },
    next_route: {
        type: String,
        default: ''
    }
});

const form = useForm({
    answers: {}
});

// JSON Option Parser
const parseOptions = (options) => {
    try {
        return typeof options === 'string' ? JSON.parse(options) : options;
    } catch(e) {
        return [];
    }
};

// Dynamic Storage Key: Creates a unique key for IQ, Departmental, and Rules so they don't overwrite each other
const storageKey = computed(() => {
    return 'assessment_progress_' + (props.step_title ? props.step_title.replace(/\s+/g, '_').toLowerCase() : 'default');
});

// Load progress from Local Storage when page loads
onMounted(() => {
    const savedProgress = localStorage.getItem(storageKey.value);
    if (savedProgress) {
        form.answers = JSON.parse(savedProgress);
    }
});

// Save progress to Local Storage on every input/change
const saveProgress = () => {
    localStorage.setItem(storageKey.value, JSON.stringify(form.answers));
};

// Submit Form via Inertia
const submitAnswers = () => {
    form.post(props.next_route, {
        preserveScroll: true,
        onSuccess: () => {
            // Clear only this specific step's storage after successful submission
            localStorage.removeItem(storageKey.value);
        }
    });
};
</script>