<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // ডকুমেন্টের কলামগুলো যুক্ত করা হলো (nullable রাখা হয়েছে যাতে সাধারণ ইউজার তৈরিতে সমস্যা না হয়)
            $table->string('resume_path')->nullable()->after('phone');
            $table->string('nid_path')->nullable()->after('resume_path');
            $table->string('certificate_path')->nullable()->after('nid_path');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // রোলব্যাক করার জন্য কলামগুলো ড্রপ করা
            $table->dropColumn(['resume_path', 'nid_path', 'certificate_path']);
        });
    }
};