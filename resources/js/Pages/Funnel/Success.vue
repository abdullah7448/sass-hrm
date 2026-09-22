<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Border color dynamically based on status -->
            <div class="bg-white py-8 px-4 shadow-xl sm:rounded-lg sm:px-10 text-center border-t-4"
                :class="candidate.status === 'Rejected' ? 'border-red-500' : 'border-green-500'">
                
                <!-- Icon dynamically based on status -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-6"
                    :class="candidate.status === 'Rejected' ? 'bg-red-100' : 'bg-green-100'">
                    
                    <svg v-if="candidate.status === 'Rejected'" class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>

                    <svg v-else class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                
                <h2 class="text-2xl font-extrabold text-gray-900 mb-2">
                    {{ candidate.status === 'Rejected' ? 'Application Reviewed' : 'Application Submitted!' }}
                </h2>
                
                <div class="bg-gray-50 p-4 rounded-lg my-4 text-left border border-gray-100">
                    <p class="text-sm text-gray-600 mb-1"><strong>Name:</strong> {{ candidate.name }}</p>
                    <p class="text-sm text-gray-600 mb-1"><strong>Position:</strong> {{ candidate.position }}</p>
                    <p class="text-sm text-gray-600 mt-2 flex items-center">
                        <strong>Current Status:</strong> 
                        
                        <!-- Dynamic Status Badge -->
                        <span class="ml-2 px-3 py-1 text-xs font-bold rounded-full uppercase"
                            :class="{
                                'bg-green-100 text-green-700': candidate.status === 'Approved',
                                'bg-blue-100 text-blue-700': candidate.status === 'Call for Interview',
                                'bg-orange-100 text-orange-700': candidate.status === 'Hold',
                                'bg-yellow-100 text-yellow-700': candidate.status === 'Applied' || candidate.status === 'Pending' || !candidate.status,
                                'bg-red-100 text-red-700': candidate.status === 'Rejected'
                            }"
                        >
                            <span v-if="candidate.status === 'Rejected'">Not Selected (Try Later)</span>
                            <span v-else>{{ candidate.status || 'Applied' }}</span>
                        </span>
                    </p>
                </div>

                <p class="mt-2 text-sm text-gray-600 leading-relaxed mb-6">
                    <span v-if="candidate.status === 'Rejected'">
                        Thank you for your interest in joining our team. Although your profile is impressive, we have decided to move forward with other candidates at this time. We wish you the best in your career!
                    </span>
                    <span v-else>
                        Thank you for applying. We have securely received your application and assessment results. 
                        <br><br>
                        <strong>Our HR team is currently reviewing your profile and will contact you shortly.</strong>
                    </span>
                </p>

                <div class="text-xs text-gray-400">
                    You can bookmark this page to check your status later.
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    candidate: {
        type: Object,
        required: true
    }
});
</script>