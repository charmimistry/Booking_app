<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // core fields
            $table->date('booking_date')->index();
            $table->time('start_time');
            $table->time('end_time');

            // metadata
            $table->string('customer_name', 150)->index();
            $table->string('customer_email', 150)->index();

            // booking type: full_day, half_day, custom
            $table->enum('booking_type', ['full_day', 'half_day', 'custom'])->index();

            // slot is nullable; values: first_half, second_half
            $table->enum('slot', ['first_half', 'second_half'])->nullable()->index();

            $table->timestamps();

            // composite index for fast overlap queries on a given day
            $table->index(['booking_date', 'start_time', 'end_time']);

            // For dedup checks you might want a unique index if you define "duplicate" strictly.
            // (Not added here because duplicates defined by overlap, not identical rows.)
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}

