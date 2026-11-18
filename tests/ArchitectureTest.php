<?php

arch('the Alunta Filament package uses no application code')
    ->expect('KasperStuck\Alunta\Filament')
    ->not->toUse('App');
