<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['created_by']);
            
            // Modify the column
            $table->unsignedBigInteger('created_by')->nullable()->change();
            
            // Add foreign key back
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['created_by']);
            
            // Modify column back
            $table->unsignedBigInteger('created_by')->nullable(false)->change();
            
            // Add foreign key back
            $table->foreign('created_by')->references('id')->on('users');
        });
    }
};
