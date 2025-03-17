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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('department_id')->index()->nullable()->references('id')->on('departments')->onDelete('set null');
            $table->foreignId('parent_id')->index()->nullable()->references('id')->on('categories')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->boolean('active')->default(true);
            $table->softDeletes();  // Add soft delete column for "deleted_at" field.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
