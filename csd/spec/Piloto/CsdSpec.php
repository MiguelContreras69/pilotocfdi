<?php

namespace spec\Piloto;

use PhpSpec\ObjectBehavior;

class CsdSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith();
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Piloto\Csd');
    }

}
