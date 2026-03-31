<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'staff')
            ->update(['role' => User::ROLE_CUSTOMER]);
    }

    public function down(): void
    {
        // Không rollback để tránh đưa lại role cũ không còn sử dụng.
    }
};
