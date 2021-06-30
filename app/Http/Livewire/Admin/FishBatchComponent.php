<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

class FishBatchComponent extends Component
{
  public function render()
  {
    return view('livewire.admin.fish-batch-component')
      ->layout('layouts.admin-layout', $this->layoutData);
  }

  /**
   * Este metodo se encarga de servir las variables que requiere el
   * layout de administración
   * @return array
   */
  public function getLayoutDataProperty()
  {
    $data = [
      'title' => 'Lotes',
      'contentTitle' => "Lotes de Peces",
      'breadcrumb' => [
        'Panel' => route('admin.dashboard'),
        'Lotes' => route('admin.fish_batch'),
      ],
    ];

    return $data;
  }
}
