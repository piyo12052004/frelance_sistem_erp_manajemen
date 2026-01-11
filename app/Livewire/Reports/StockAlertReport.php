<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use App\Traits\WithAlert;

#[Layout('layouts.app')]
class StockAlertReport extends Component
{
    use WithAlert;
    use WithPagination;

    public $thresholds = [];
    public $filterName = '';
    public $filterCode = '';
    public $filterQuantityMin = null;
    public $filterQuantityMax = null;

    #[Computed]
    public function stockAlert()
    {
        $query = Product::query()->belowStockAlert();

        if (! empty($this->filterName)) {
            $query->where('name', 'ilike', '%' . $this->filterName . '%'); // postgres-friendly
        }

        if (! empty($this->filterCode)) {
            $query->where('code', 'ilike', '%' . $this->filterCode . '%');
        }

        if ($this->filterQuantityMin !== null) {
            $query->where('quantity', '>=', $this->filterQuantityMin);
        }

        if ($this->filterQuantityMax !== null) {
            $query->where('quantity', '<=', $this->filterQuantityMax);
        }

        return $query->paginate(10);
    }

    public function setThreshold($productId, $threshold)
    {
        $product = Product::find($productId);

        if ($product) {
            $product->stock_alert = $threshold;
            $product->save();
        }
    }

    public function render()
    {
        return view('livewire.reports.stock-alert-report', [
            'products' => $this->stockAlert(),
        ]);
    }
}
