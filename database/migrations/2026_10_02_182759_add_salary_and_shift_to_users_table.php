<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('basic_salary', 10, 2)->nullable()->after('company_id');
            // Shift Type: 'morning' (9 AM) or 'evening' (2 PM)
            $table->string('shift_type')->default('morning')->after('basic_salary'); 
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['basic_salary', 'shift_type']);
        });
    }
};