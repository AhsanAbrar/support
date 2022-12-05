<?php

namespace AhsanDev\Support\Livewire;

trait ResourceIndexCopy
{
    // public $model;
    // public $currentItem;
    public $search;

    // public $selectedItems = [];
    // public $selectAll = false;
    // public $bulkDisabled = true;

    // public function __construct()
    // {
    //     $this->queryString = ['search', 'sortField', 'sortDirection'];
    //     $this->listeners = ['confirmProceed', 'confirmProceedBulk'];
    // }

    // public function confirmProceed()
    // {
    //     $this->currentItem->delete();
    // }

    // public function confirmDelete(User $user)
    // {
    //     $this->currentItem = $user;
    //     $this->emit('confirm');
    // }

    // public function updatedSelectedItems($value)
    // {
    //     dd($value);
    //     // info('SelectedItems Value: ' . json_encode($value));
    //     // info('SelectedItems[]: ' . json_encode($this->selectedItems));
    //     $totalItems = $this->query()->count();

    //     if (count($value) === $totalItems) {
    //         $this->selectAll = true;
    //     } else {
    //         $this->selectAll = false;
    //     }
    // }

    // public function updatedSelectAll($value)
    // {
    //     // info('updatedSelectAll[]: ' . json_encode($this->selectedItems));
    //     if ($value) {
    //         foreach ($this->query() as $item) {
    //             if (!in_array($item->id, $this->selectedItems)) {
    //                 $this->selectedItems[] = $item->id;
    //             }
    //         }
    //     } else {
    //         $this->selectedItems = [];
    //     }
    // }

    // public function confirmProceedBulk()
    // {
    //     if (($key = array_search(1, $this->selectedItems)) !== false) {
    //         unset($this->selectedItems[$key]);
    //     }

    //     $this->model->destroy($this->selectedItems);
    // }

    // public function confirmDeleteBulk()
    // {
    //     $this->emit('confirm', ['bulk' => true]);
    // }
}
