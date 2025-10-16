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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customer_id')->nullable();
            $table->string('name');
            $table->string('country_code', 20)->default('+91');
            $table->string('mobile')->nullable();
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode')->nullable();
            $table->enum('vehicle_assign_status', ['deallocate','suspended'])->nullable();

            $table->string('driving_licence_front')->nullable();
            $table->string('driving_licence_back')->nullable();
            $table->integer('driving_licence_status')->default(0)->comment('0:Pending, 1:Uploaded, 2:Verified, 3:Cancelled');

            $table->string('aadhar_card_front')->nullable();
            $table->string('aadhar_card_back')->nullable();
            $table->string('aadhar_number')->nullable();
            $table->integer('aadhar_card_status')->default(0)->comment('0:Pending, 1:Uploaded, 2:Verified, 3:Cancelled');

            $table->string('pan_card_front')->nullable();
            $table->string('pan_card_back')->nullable();
            $table->integer('pan_card_status')->default(0)->comment('0:Pending, 1:Uploaded, 2:Verified, 3:Cancelled');

            $table->string('current_address_proof_front')->nullable();
            $table->string('current_address_proof_back')->nullable();
            $table->integer('current_address_proof_status')->default(0)->comment('0:Pending, 1:Uploaded, 2:Verified, 3:Cancelled');

            $table->string('passbook_front')->nullable();
            $table->tinyInteger('passbook_status')->default(0)->comment('0:Pending, 1:Uploaded, 2:Verified, 3:Cancelled');

            $table->tinyInteger('status')->default(1);

            $table->string('profile_image')->nullable();
            $table->integer('profile_image_status')->default(0)->comment('0:Pending, 1:Uploaded, 2:Verified, 3:Cancelled');

            $table->unsignedBigInteger('suspended_by')->nullable();
            $table->dateTime('kyc_uploaded_at')->nullable();
            $table->unsignedBigInteger('kyc_verified_by')->nullable()->comment('user id');
            $table->enum('is_verified', ['unverified','verified','rejected'])->default('unverified')->comment('For KYC Verification');
            $table->dateTime('kyc_verified_at')->useCurrent();
            $table->dateTime('date_of_rejection')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();

            $table->string('remember_token', 100)->nullable();
            $table->text('fcm_token')->nullable();
            $table->enum('device_type', ['android','ios'])->nullable()->comment('android / ios');

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
