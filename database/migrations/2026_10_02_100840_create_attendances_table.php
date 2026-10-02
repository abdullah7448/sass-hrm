<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete(); // Company Isolation এর জন্য
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Employee ID
            
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            
            // Status: present, absent, late, half_day
            $table->string('status')->default('present');
            $table->text('note')->nullable(); // কোনো রিমার্ক বা লেট হওয়ার কারণ থাকলে
            
            $table->timestamps();

            // একজন এমপ্লয়ি যেন একই তারিখে দুইবার অ্যাটেনডেন্স রেকর্ড তৈরি করতে না পারে
            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};