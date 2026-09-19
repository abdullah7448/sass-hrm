<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('candidates', function (Blueprint $table) {
        $table->id();
        $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
        $table->string('name');
        $table->string('email');
        $table->string('phone');
        $table->string('position');
        $table->integer('iq_score')->nullable();
        $table->integer('department_score')->nullable();
        
        // নতুন ৩টি কলাম যোগ করা হলো ফাইল সেভ করার জন্য
        $table->string('resume_path')->nullable();
        $table->string('nid_path')->nullable();
        $table->string('certificate_path')->nullable();
        
        $table->string('status')->default('Pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
