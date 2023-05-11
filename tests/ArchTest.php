<?php

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'ddd'])
    ->each->not->toBeUsed();
