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
        Schema::table('guests', function (Blueprint $table) {
            if (!Schema::hasIndex('guests', 'guests_email_index')) {
                $table->index('email');
            }
            if (!Schema::hasIndex('guests', 'guests_status_index')) {
                $table->index('status');
            }
            if (!Schema::hasIndex('guests', 'guests_checked_in_index')) {
                $table->index('checked_in');
            }
        });

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasIndex('events', 'events_status_index')) {
                $table->index('status');
            }
            if (!Schema::hasIndex('events', 'events_created_at_index')) {
                $table->index('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropIndexIfExists('guests_email_index');
            $table->dropIndexIfExists('guests_status_index');
            $table->dropIndexIfExists('guests_checked_in_index');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndexIfExists('events_status_index');
            $table->dropIndexIfExists('events_created_at_index');
        });
    }
};