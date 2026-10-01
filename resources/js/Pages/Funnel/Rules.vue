<template>
    <div class="min-h-screen bg-gray-100 py-10 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
        <div class="max-w-4xl w-full bg-white shadow-2xl rounded-2xl overflow-hidden">
            
            <!-- Header Section with generous padding -->
            <div class="bg-blue-600 px-10 py-12 text-center">
                <h2 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight mb-4">
                    BuzzBlu Office Rules & Code of Conduct
                </h2>
                <p class="text-blue-100 text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
                    🕌 আমাদের উদ্দেশ্য: একটি সুন্দর ও সুশৃঙ্খল পরিবেশই প্রতিষ্ঠানের আসল শক্তি। এসব নিয়ম চাপ নয়—বরং সম্মান, দায়িত্ববোধ ও একসাথে এগিয়ে চলার মাধ্যম।
                </p>
            </div>

            <form @submit.prevent="submitRules" class="px-8 py-10 sm:px-12">
                
                <!-- Scrollable Document Area with soft borders and inner padding -->
                <div class="bg-white border-2 border-gray-100 rounded-xl p-8 sm:p-10 h-[500px] overflow-y-auto mb-10 shadow-inner custom-scrollbar">
                    
                    <div class="space-y-8 text-gray-700 text-base sm:text-lg leading-relaxed">
                        
                        <div class="flex gap-5">
                            <span class="text-2xl shrink-0">📌</span>
                            <p class="pt-1">গসিপ বা কাজের বাইরের কথাবার্তা অফিস পরিবেশকে নষ্ট করে — এতে জরিমানা বা চাকরিচ্যুতির ঝুঁকি থাকে।</p>
                        </div>

                        <div class="flex gap-5">
                            <span class="text-2xl shrink-0">📌</span>
                            <p class="pt-1">কারো সাথে মজা করলেও সেটা যেন অপমানজনক বা কষ্টদায়ক না হয়, সেদিকে খেয়াল রাখতে হবে। সুতরাং, কাউকে মজা করে কিছু না বলাই উত্তম।</p>
                        </div>

                        <div class="flex gap-5">
                            <span class="text-2xl shrink-0">🏆</span>
                            <p class="pt-1">আমরা শুধুমাত্র <strong>A-Player</strong>-এর সাথে কাজ করতে পছন্দ করি—যারা ভালো আচরণ ও ভালো রেজাল্ট—দু’টোকেই গুরুত্ব দেয়। B-Player (শুধু কালচার) ও C-Player (শুধু রেজাল্ট) নয়, আমরা চাই ভালো ব্যবহার আর বেটার রেজাল্ট একসাথে হোক।</p>
                        </div>

                        <div class="flex gap-5">
                            <span class="text-2xl shrink-0">💰</span>
                            <p class="pt-1">আপনার যদি পদোন্নতি হয় তাহলে, সেলারি আপডেট লেটার হাতে পাওয়ার আগ পর্যন্ত পূর্বের স্যালারি অব্যাহত থাকবে। সেলারি আপডেট লেটার হাতে পেতে সর্বনিম্ন একমাস এবং সর্বোচ্চ তিনবার সময় লাগতে পারে।</p>
                        </div>

                        <div class="flex gap-5">
                            <span class="text-2xl shrink-0">⏰</span>
                            <p class="pt-1">টানা তিন দিন দেরি করলে একদিন অনুপস্থিত (Absent) হিসেবে গণ্য হবে এবং হাজিরা খাতায় সিগনেচার না থাকলে সেটিও Absent হিসেবে ধরা হবে।</p>
                        </div>

                        <div class="flex gap-5 bg-red-50 p-6 rounded-xl border border-red-100">
                            <span class="text-2xl shrink-0">⚠️</span>
                            <p class="pt-1 text-red-800">চাকরি ছাড়ার কমপক্ষে ২ মাস আগে জানাতে হবে। তা না হলে বেতনের সমপরিমাণ টাকা কর্তন করা হতে পারে। আর যদি কেউ পূর্বে না জানিয়ে ইচ্ছাকৃতভাবে এমন আচরণ করেন, যাতে তাকে দ্রুত ফায়ার করতে হয়—তাহলে সেটা "নিজ ইচ্ছায় ফায়ার" ধরা হবে এবং তার বেতনের সমপরিমাণ টাকা কর্তন করা হবে। <br><br><span class="text-sm font-medium text-red-600">(যদি আপনার কোনো জরুরি প্রয়োজনে চাকরি ছাড়তে হয়, দয়া করে আমাদের সঙ্গে খোলামেলা আলোচনা করুন। আমরা ইনশাআল্লাহ সহযোগিতার সর্বোচ্চ চেষ্টা করব)</span></p>
                        </div>

                        <div class="flex gap-5">
                            <span class="text-2xl shrink-0">👨‍💼</span>
                            <p class="pt-1">সিনিয়র বা টিম লিডারের কমান্ড অনুসরণ করা সম্মিলিত কাজের জন্য আবশ্যক। অফিস থেকে বাইরে যেতে হলে সংশ্লিষ্ট ব্যক্তির অনুমতি নিতে হবে।</p>
                        </div>

                        <div class="flex gap-5">
                            <span class="text-2xl shrink-0">🤝</span>
                            <p class="pt-1">অফিসে ভুল হতেই পারে, কিন্তু ভুল হলে তা স্বীকার করে সমাধান করার চেষ্টা করা প্রত্যেকের দায়িত্ব।</p>
                        </div>

                        <div class="flex gap-5">
                            <span class="text-2xl shrink-0">📵</span>
                            <p class="pt-1">অফিস মিটিং চলাকালীন মোবাইল ব্যবহার নিষিদ্ধ। সকলকে নোট নিয়ে মনোযোগসহকারে অংশগ্রহণ করতে হবে।</p>
                        </div>

                        <div class="flex gap-5">
                            <span class="text-2xl shrink-0">💻</span>
                            <p class="pt-1">অফিসের সম্পদ আপনার আমার সবার সম্পদ। তাই সেগুলো নষ্ট বা অপচয় করা যাবে না, তাহলে ফাইন হতে পারে। কাজ শেষে সবসময় ডিভাইস shutdown ও power cable disconnect করে যেতে হবে। রুমে কেউ না থাকলে বের হওয়ার সময় লাইট ও ফ্যান বন্ধ করে যাওয়া আবশ্যক।</p>
                        </div>

                        <div class="flex gap-5">
                            <span class="text-2xl shrink-0">🧹</span>
                            <p class="pt-1">নিজের ডেস্ককে পরিপাটি রাখা শুধু দায়িত্ব নয়, আপনার পরিচয়ের অংশ। নিয়মিত অবহেলার ক্ষেত্রে প্রতিষ্ঠান থেকে সচেতন করার উপযোগী পদক্ষেপ নেওয়া হবে। ব্যক্তিগত কাজ ডেস্কে অনুমতি ছাড়া করা যাবে না এবং ডেস্কে বসে ১ মিনিটের বেশি পার্সোনাল কল অথবা মেসেজ করা যাবেনা।</p>
                        </div>

                        <div class="flex gap-5">
                            <span class="text-2xl shrink-0">🕌</span>
                            <p class="pt-1">অফিসে নামাজ আদায় করার পরিবেশ আছে — এটি শুধু ধর্মীয় দায়িত্ব নয়, আমাদের সংস্কৃতির অংশ।</p>
                        </div>

                        <div class="flex gap-5">
                            <span class="text-2xl shrink-0">🚫</span>
                            <p class="pt-1">প্রাক্তন কর্মীদের (যাদের clearance দেয়া হয়নি) তাদের সঙ্গে অফিসিয়ালভাবে কোনো যোগাযোগ না রাখাই উত্তম।</p>
                        </div>

                        <div class="flex gap-5 bg-green-50 p-6 rounded-xl border border-green-100">
                            <span class="text-2xl shrink-0">📚</span>
                            <p class="pt-1 text-green-800">মাসে অন্তত ১টি বই পড়া প্রত্যাশিত। কেউ যদি বইটি পড়ে অফিসের দায়িত্বপ্রাপ্ত ব্যক্তিকে তা সুন্দরভাবে বুঝাতে পারে, তাকে <strong>৳১,০০০ বোনাস</strong> প্রদান করা হবে।</p>
                        </div>
                        
                        <div class="flex gap-5 bg-blue-50 p-6 rounded-xl border border-blue-100 mt-8">
                            <span class="text-2xl shrink-0">👔</span>
                            <div class="pt-1 text-blue-900">
                                <h4 class="font-bold text-lg mb-3">ড্রেস কোড (বাধ্যতামূলক):</h4>
                                <ul class="list-disc pl-6 space-y-2">
                                    <li><strong>পুরুষদের জন্য:</strong> কলারযুক্ত ফরমাল পোশাক অথবা পাঞ্জাবি।</li>
                                    <li><strong>মহিলাদের জন্য:</strong> বোরকা ও হিজাব পরা বাধ্যতামূলক।</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Big Agreement Checkbox -->
                <div class="flex items-start bg-gray-50 p-6 rounded-xl border-2 border-gray-200 mb-8 transition-colors cursor-pointer" 
                    :class="{'bg-green-50 border-green-400': form.agreed}"
                    @click="form.agreed = !form.agreed">
                    
                    <div class="flex-shrink-0 mt-1">
                        <input id="agree" v-model="form.agreed" type="checkbox" required @click.stop
                            class="h-6 w-6 text-green-600 border-gray-400 rounded focus:ring-green-500 cursor-pointer">
                    </div>
                    <label for="agree" class="ml-4 block text-base sm:text-lg font-semibold text-gray-800 cursor-pointer select-none">
                        আমি উপরের সকল নিয়মাবলী ও শর্তসমূহ মনোযোগ সহকারে পড়েছি এবং আমি এই সকল নিয়মের প্রতি সম্পূর্ণভাবে সম্মত।
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" :disabled="!form.agreed || form.processing" 
                    class="w-full py-5 px-6 rounded-xl text-lg sm:text-xl font-bold text-white transition-all duration-200 shadow-md focus:outline-none focus:ring-4 focus:ring-opacity-50"
                    :class="form.agreed ? 'bg-blue-600 hover:bg-blue-700 hover:shadow-xl hover:-translate-y-1 focus:ring-blue-500' : 'bg-gray-300 cursor-not-allowed text-gray-500'">
                    
                    <span v-if="form.processing">Submitting Final Application...</span>
                    <span v-else>I Agree & Submit Application →</span>
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({ 
    agreed: false,
    answers: {
        'BuzzBlu Office Rules & NDA': 'I have read and completely agreed to all the rules.'
    }
});

const submitRules = () => {
    if (form.agreed) {
        form.post(route('candidate.process_rules'), {
            preserveScroll: true
        });
    }
};
</script>

<style scoped>
/* Clean & minimal scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1; 
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8; 
}
</style>