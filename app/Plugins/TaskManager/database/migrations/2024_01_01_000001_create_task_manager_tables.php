<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour créer les tables du plugin Task Manager
 *
 * Cette migration crée toutes les tables nécessaires au bon fonctionnement
 * du plugin de gestion de tâches.
 */
return new class extends Migration
{
    /**
     * Exécute les migrations.
     */
    public function up(): void
    {
        // Table des catégories
        Schema::create('task_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#3B82F6'); // Couleur hexadécimale
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('task_categories')->onDelete('cascade');
            $table->index(['is_active', 'sort_order']);
        });

        // Table des tâches
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->dateTime('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable(); // Pour les sous-tâches
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            $table->integer('progress')->default(0); // 0-100
            $table->boolean('is_public')->default(false);
            $table->json('tags')->nullable();
            $table->json('attachments')->nullable();
            $table->json('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('task_categories')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('tasks')->onDelete('cascade');

            $table->index(['status', 'priority']);
            $table->index(['assigned_to', 'status']);
            $table->index(['due_date', 'status']);
            $table->index(['created_by', 'status']);
            $table->index(['category_id', 'status']);
        });

        // Table des dépendances entre tâches
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('dependency_id');
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('dependency_id')->references('id')->on('tasks')->onDelete('cascade');

            $table->unique(['task_id', 'dependency_id']);
            $table->index(['task_id']);
            $table->index(['dependency_id']);
        });

        // Table des commentaires de tâches
        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->text('content');
            $table->json('mentions')->nullable(); // Utilisateurs mentionnés
            $table->boolean('is_internal')->default(false); // Commentaire interne
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['task_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        // Table des fichiers attachés aux tâches
        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('path');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->string('disk')->default('local');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['task_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        // Table des étiquettes de tâches
        Schema::create('task_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color', 7)->default('#6B7280');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'name']);
        });

        // Table pivot pour les étiquettes de tâches
        Schema::create('task_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('task_tag_id');
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('task_tag_id')->references('id')->on('task_tags')->onDelete('cascade');

            $table->unique(['task_id', 'task_tag_id']);
            $table->index(['task_id']);
            $table->index(['task_tag_id']);
        });

        // Table des activités de tâches
        Schema::create('task_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->string('action'); // created, updated, status_changed, assigned, etc.
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['task_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });

        // Table des rapports de temps
        Schema::create('task_time_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->decimal('duration', 8, 2)->nullable(); // en heures
            $table->text('description')->nullable();
            $table->boolean('is_billable')->default(false);
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['task_id', 'start_time']);
            $table->index(['user_id', 'start_time']);
            $table->index(['start_time', 'end_time']);
        });

        // Table des notifications de tâches
        Schema::create('task_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // due_date_reminder, assignment, completion, etc.
            $table->text('message');
            $table->json('data')->nullable();
            $table->dateTime('scheduled_at');
            $table->dateTime('sent_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['user_id', 'is_read']);
            $table->index(['scheduled_at', 'sent_at']);
            $table->index(['task_id', 'type']);
        });

        // Table des rapports de tâches
        Schema::create('task_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // daily, weekly, monthly, custom
            $table->json('filters')->nullable();
            $table->json('columns')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_scheduled')->default(false);
            $table->string('schedule_frequency')->nullable(); // daily, weekly, monthly
            $table->dateTime('last_generated_at')->nullable();
            $table->dateTime('next_generation_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['user_id', 'type']);
            $table->index(['is_scheduled', 'next_generation_at']);
        });

        // Table des exports de tâches
        Schema::create('task_exports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('filename');
            $table->string('format'); // csv, xlsx, pdf
            $table->json('filters')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->text('error_message')->nullable();
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });

        // Table des webhooks de tâches
        Schema::create('task_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('url');
            $table->string('method')->default('POST');
            $table->json('headers')->nullable();
            $table->json('events')->nullable(); // created, updated, completed, etc.
            $table->boolean('is_active')->default(true);
            $table->integer('retry_count')->default(0);
            $table->integer('max_retries')->default(3);
            $table->dateTime('last_triggered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'events']);
            $table->index(['last_triggered_at']);
        });

        // Table des logs de webhooks
        Schema::create('task_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('webhook_id');
            $table->unsignedBigInteger('task_id')->nullable();
            $table->string('event');
            $table->json('payload');
            $table->string('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->dateTime('next_retry_at')->nullable();
            $table->timestamps();

            $table->foreign('webhook_id')->references('id')->on('task_webhooks')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');

            $table->index(['webhook_id', 'created_at']);
            $table->index(['task_id', 'event']);
            $table->index(['next_retry_at']);
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_webhook_logs');
        Schema::dropIfExists('task_webhooks');
        Schema::dropIfExists('task_exports');
        Schema::dropIfExists('task_reports');
        Schema::dropIfExists('task_notifications');
        Schema::dropIfExists('task_time_entries');
        Schema::dropIfExists('task_activities');
        Schema::dropIfExists('task_tag');
        Schema::dropIfExists('task_tags');
        Schema::dropIfExists('task_attachments');
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('task_dependencies');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('task_categories');
    }
};
