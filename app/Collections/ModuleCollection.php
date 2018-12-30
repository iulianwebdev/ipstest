<?php

namespace App\Collections;

use App\Module;
use Illuminate\Database\Eloquent\Collection;

class ModuleCollection extends Collection
{
    public function hasLast()
    {
        if ($this->isEmpty()) {
            return false;
        }

        return $this->contains('name', $this->lastModule()->name);
    }

    public function getNextAvailableLabel($label): string
    {
        $nextLabel = $this->generateNextAvailableLabel($label);

        if ($this->contains('name', $nextLabel)) {
            return $nextLabel;
        }

        return '';
    }

    public function nextAvailable(string $nextLabel): Module
    {
        return $this->where('name', $nextLabel)->first();
    }

    /**
     * generates the next available module label
     * by decrementing the label number
     *
     * @param  string $label
     * @return string
     */
    private function generateNextAvailableLabel(string $label): string
    {
        $nextLabel = preg_replace_callback("/(\d+)/", function ($matches) {
            return --$matches[1];
        }, $label, $limit = 1);

        return $nextLabel;
    }

    public function lastModule()
    {
        return $this->first()->withLastLabel();
    }
}
