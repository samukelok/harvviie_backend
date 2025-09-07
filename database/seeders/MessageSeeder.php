<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample contact messages
        Message::factory(15)->contact()->create();
        
        // Create sample service request messages
        Message::factory(8)->serviceRequest()->create();
        
        // Create some unread messages for dashboard
        Message::factory(5)->unread()->create();
    }
}