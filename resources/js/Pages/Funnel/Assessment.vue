<template>
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-extrabold text-gray-900">
                    {{ step === 1 ? 'Part 1: General IQ Test' : 'Part 2: Departmental Test' }}
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ step === 1 ? 'Let us test your basic problem-solving skills.' : 'Questions related to your applied position.' }}
                </p>
            </div>

            <div class="bg-white shadow sm:rounded-md p-6 sm:p-8">
                <form @submit.prevent="step === 1 ? nextStep() : submitAssessment()" class="space-y-8">
                    
                    <!-- IQ Questions (Step 1) -->
                    <div v-if="step === 1">
                        <div v-for="(question, index) in iqQuestions" :key="question.id" class="border-b pb-6 mb-6 last:border-b-0 last:mb-0 last:pb-0">
                            <p class="text-lg font-medium text-gray-900 mb-4">{{ index + 1 }}. {{ question.question_text }}</p>
                            <div class="space-y-3">
                                <label v-for="(option, optIndex) in JSON.parse(question.options)" :key="optIndex" 
                                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition-colors"
                                    :class="{'border-blue-500 bg-blue-50': form.answers[question.id] === option}">
                                    <input type="radio" :value="option" v-model="form.answers[question.id]" class="h-4 w-4 text-blue-600" required>
                                    <span class="ml-3 text-sm text-gray-700">{{ option }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Departmental Questions (Step 2) -->
                    <div v-if="step === 2">
                        <div v-for="(question, index) in deptQuestions" :key="question.id" class="border-b pb-6 mb-6 last:border-b-0 last:mb-0 last:pb-0">
                            <p class="text-lg font-medium text-gray-900 mb-4">{{ index + 1 }}. {{ question.question_text }}</p>
                            <div class="space-y-3">
                                <label v-for="(option, optIndex) in JSON.parse(question.options)" :key="optIndex" 
                                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition-colors"
                                    :class="{'border-blue-500 bg-blue-50': form.answers[question.id] === option}">
                                    <input type="radio" :value="option" v-model="form.answers[question.id]" class="h-4 w-4 text-blue-600" required>
                                    <span class="ml-3 text-sm text-gray-700">{{ option }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="pt-4 flex justify-end">
                        <button v-if="step === 1" type="submit" class="w-full py-3 px-4 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            Next: Departmental Questions →
                        </button>
                        
                        <div v-else class="w-full flex space-x-4">
                            <button type="button" @click="step = 1" class="w-1/3 py-3 px-4 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                ← Back
                            </button>
                            <button type="submit" :disabled="form.processing" class="w-2/3 py-3 px-4 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 disabled:opacity-50">
                                <span v-if="form.processing">Submitting...</span>
                                <span v-else>Submit & Go to Documents →</span>
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ questions: Array });
const step = ref(1);

const form = useForm({ answers: {} });

onMounted(() => {
    props.questions.forEach(q => { form.answers[q.id] = ''; });
});

// ক্যাটাগরি অনুযায়ী প্রশ্নগুলো আলাদা করে নিচ্ছি
const iqQuestions = computed(() => props.questions.filter(q => q.category === 'iq'));
const deptQuestions = computed(() => props.questions.filter(q => q.category === 'departmental'));

const nextStep = () => {
    step.value = 2;
    window.scrollTo(0, 0); // স্ক্রল করে উপরে নিয়ে যাবে
};

const submitAssessment = () => {
    form.post(route('candidate.assessment'));
};
</script>