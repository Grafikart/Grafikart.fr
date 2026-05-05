<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:clean')->dailyAt('02:00');
Schedule::command('app:notify-subscription-renewal')->dailyAt('09:00');
Schedule::command('app:dump')->dailyAt('01:00');
