<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DeleteOldSupportTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature ='support:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kapanan destek taleplerini 1 ay sonra siler (ekleri dahil)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
