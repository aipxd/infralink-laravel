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
        // 1. facility_categories
        Schema::create('facility_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        // 2. facilities
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('facility_categories')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('building', 100);
            $table->string('location_detail', 255)->nullable();
            $table->integer('capacity');
            $table->text('description')->nullable();
            $table->text('usage_rules')->nullable();
            $table->string('open_time', 5); // "HH:mm"
            $table->string('close_time', 5); // "HH:mm"
            $table->integer('max_duration_minutes')->default(240);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. facility_photos
        Schema::create('facility_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
            $table->string('file_path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // 4. bookings
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
            $table->date('booking_date');
            $table->string('start_time', 5); // "HH:mm"
            $table->string('end_time', 5);   // "HH:mm"
            $table->string('event_name', 200);
            $table->text('purpose');
            $table->integer('participants');
            $table->string('contact_phone', 20);
            $table->enum('status', ['DIAJUKAN', 'DISETUJUI', 'DITOLAK', 'DIBATALKAN'])->default('DIAJUKAN');
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('decided_at')->nullable();
            $table->text('decision_note')->nullable();
            $table->timestamps();

            $table->index(['facility_id', 'booking_date', 'status']);
            $table->index(['user_id', 'status']);
        });

        // 5. booking_status_logs
        Schema::create('booking_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->text('note')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });

        // 6. blocked_slots
        Schema::create('blocked_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
            $table->date('block_date');
            $table->string('start_time', 5); // "HH:mm"
            $table->string('end_time', 5);   // "HH:mm"
            $table->string('reason');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->dateTime('created_at')->useCurrent();

            $table->index(['facility_id', 'block_date']);
        });

        // 7. attachments
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->dateTime('uploaded_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('blocked_slots');
        Schema::dropIfExists('booking_status_logs');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('facility_photos');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('facility_categories');
    }
};
