<?php

namespace AhsanDev\Support\Livewire;

trait ResourceIndex
{
    use Filterable;

    public $model;

    public $search;

    public $selectAll = false;

    public $selected = [];

    public $filters = [];

    public $currentItem;

    public $sortField;

    public $sortDirection;

    public function __construct()
    {
        parent::__construct();

        $this->queryString = ['search', 'sortField', 'sortDirection', 'filters'];
        $this->listeners = ['confirmProceed', 'modalSubmitDone'];
    }

    /**
     * This method is trick for loading state after modal submission.
     */
    public function loading()
    {
    }

    public function modalSubmitDone()
    {
        //
    }

    public function sort($field = null)
    {
        if (! $field) {
            return;
        }

        $this->sortDirection = $this->sortField === $field
            ? $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc'
            : 'asc';

        $this->sortField = $field;
    }

    public function confirmProceed()
    {
        if (empty($this->selected)) {
            $items = (array) $this->currentItem;
        } else {
            $items = $this->selected;
        }

        $this->model->destroy($items);
        $this->flash('Record Deleted Successfully!');
    }

    public function confirmDelete($id = null)
    {
        if ($id) {
            $this->currentItem = $id;
            $this->emit('confirm');
        } else {
            $this->emit('confirm', ['bulk' => true]);
        }
    }

    public function updatedSelected($value)
    {
        if (count($this->selected) === $this->query()->count()) {
            $this->selectAll = true;
        } else {
            $this->selectAll = false;
        }
    }

    public function updatedSelectAll($value)
    {
        if (count($this->selected) === $this->query()->count()) {
            $this->selected = [];
        } else {
            $this->selected = $this->query()->pluck('id')->map(function ($id) {
            return (string) $id;
            })->toArray();
        }
    }

    public function resetFilters()
    {
        $this->filters = [];
    }

    public function ids()
    {
        return $this->query()->pluck('id')->map(function ($id) {
        return (string) $id;
        })->toArray();
    }
}
