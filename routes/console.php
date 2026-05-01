<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:clean-notifications')->weekly();
Schedule::command('app:notify-subscription-renewal')->dailyAt('09:00');
Schedule::command('snapshot:create')->dailyAt('01:00');
