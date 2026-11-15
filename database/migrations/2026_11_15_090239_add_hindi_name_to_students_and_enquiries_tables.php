<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('hindi_name')->nullable()->after('full_name');
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->string('hindi_name')->nullable()->after('student_name');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('hindi_name');
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn('hindi_name');
        });
    }
};
