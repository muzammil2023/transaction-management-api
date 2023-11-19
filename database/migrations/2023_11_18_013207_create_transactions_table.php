<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedDecimal('amount');
            $table->foreignId('user_id')->constrained();
            $table->date('due_on');
            $table->unsignedDecimal('vat');
            $table->boolean('is_vat_inclusive');
            $table->enum('status', ['Paid', 'Outstanding', 'Overdue'])->default('Outstanding');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
