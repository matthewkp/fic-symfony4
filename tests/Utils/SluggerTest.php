<?php

namespace App\Tests\Utils;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SluggerTest extends KernelTestCase
{
    const TEXT_TO_TEST = 'Mon url slugger fonctionne bien en français',
        TEXT_TO_ASSERT = 'mon-url-slugger-fonctionne-bien-en-francais';

    public function testSlugger()
    {
        self::bootKernel();
        $container = self::$container;
        $slugger = $container->get('App\Utils\Slugger');

        $slug = $slugger->run(self::TEXT_TO_TEST);

        $this->assertEquals(self::TEXT_TO_ASSERT, $slug);
    }
}
