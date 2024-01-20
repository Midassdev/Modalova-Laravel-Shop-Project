<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;

class SourceTest extends TestCase
{

    /**
     * Remove accents from a string
     *
     * @return void
     */
    public function test__remove_accents()
    {
        // given
        $input = 'foo ééé ààà bar';

        // when
        $output = remove_accents($input);

        // then
        $this->assertEquals($output, 'foo eee aaa bar');
    }
}
