<?php

use App\Schedule\SendMotivation;

Schedule::call(new SendMotivation)->wednesdays()->timezone('America/New_York')->at('12:00');
Schedule::call(new SendMotivation)->fridays()->timezone('America/New_York')->at('12:00');
Schedule::call(new SendMotivation)->sundays()->timezone('America/New_York')->at('12:00');
