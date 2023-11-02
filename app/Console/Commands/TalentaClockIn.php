<?php

namespace App\Console\Commands;

use App\Talenta\v1\Request\AuthRequest;
use App\Talenta\v1\Request\LiveAttendanceRequest;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Console\Command;

class TalentaClockIn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'talenta:clock-in';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto clock in Talenta.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle(): void
    {
        $auth = new AuthRequest();

        $email = env('TALENTA_USER_EMAIL');
        $password = env('TALENTA_USER_PASSWORD');

        $login = $auth->login($email, $password);

        if (!$login) {
            $this->warn(__('LOGIN_FAILED EMAIL: ' . $email));

            return;
        }

        $this->info(__('LOGIN_SUCCESS'));

        $history = $auth->liveAttendanceHistory();

        $dataHistories = $history['data']['history'] ?? [];

        $isClockedIn = false;
        $clockedInData = [];



        $liveAttendance = new LiveAttendanceRequest();
        $liveAttendance->setCompanyId($auth->getCompanyId());

        $this->info(__('CLOCK_IN'));
        $this->info(json_encode($liveAttendance->clockIn()));


    }
}