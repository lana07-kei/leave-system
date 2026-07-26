<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('leave:cancel-expired')->daily();
