<?php

namespace App\Livewire\Concerns;

trait InvokableComponent
{
    public function __invoke()
    {
        return $this->render();
    }
}
