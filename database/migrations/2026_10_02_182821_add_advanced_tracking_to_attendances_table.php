<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('check_out');
            $table->text('late_reason')->nullable()->after('ip_address');
            $table->boolean('is_late_approved')->default(false)->after('late_reason');
            $table->integer('working_minutes')->nullable()->after('is_late_approved'); // কাজের সময় (Average Work Hour) বের করার জন্য
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'late_reason', 'is_late_approved', 'working_minutes']);
        });
    }
};