<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('imageable_type');
            // $table->uuid('imageable_uuid')->nullable();
            // $table->unsignedBigInteger('imageable_id')->nullable();
            $table->string('imageable_id')->nullable(); 
            $table->string('name', 255);
            $table->string('path', 255);
            $table->string('mime', 255);
            $table->boolean('has_no_model')->default(false);
            $table->unsignedBigInteger('size')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('moderate_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('moderate_at')->nullable();
            $table->string('moderate_reason')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }


        /**
     * Reverse the migrations.
     */
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
