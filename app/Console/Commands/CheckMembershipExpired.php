<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MembershipUser;
use App\Enums\CommonConstant;
use App\Services\Membership\MembershipServiceInterface;
use Illuminate\Support\Facades\Log;

class CheckMembershipExpired extends Command
{
    /**
     * Tên lệnh
     *
     * @var string
     */
    protected $signature = 'membership:check-expired';

    /**
     * Mô tả
     *
     * @var string*/
    protected $description = 'Update expired membership status and send email to user';
    /**
     * Xử lý chính
     */
    public function handle(MembershipServiceInterface $memberShipservice)
    {
        $expiredMemberships = $memberShipservice->checkMembershipExpired();
        $expiredMembershipsSoon = $memberShipservice->remindMembershipExpiringSoon();

        $this->info("Checked and notice " . $expiredMembershipsSoon . " expired memberships.");
        $this->info("Checked and updated " . $expiredMemberships . " expired memberships.");
        Log::info("Checked and notice {$expiredMembershipsSoon} expired memberships.");
        Log::info("Checked and updated {$expiredMemberships} expired memberships.");
        return Command::SUCCESS;
    }
}
