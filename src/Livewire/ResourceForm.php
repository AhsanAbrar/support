<?php

namespace AhsanDev\Support\Livewire;

trait ResourceForm
{
    public $isEdit;

    public $model;

    public function __construct()
    {
        parent::__construct();

        $this->listeners = ['openModal', 'closeModal'];
    }

    public function closeModal()
    {
        $this->reset();
        $this->resetValidation();
    }
}
