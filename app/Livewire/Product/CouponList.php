<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Coupon;
use Illuminate\Support\Str;

class CouponList extends Component
{
     public $active_tab = 1;

    public $coupon_id;
    public $name;
    public $coupon_code;
    public $coupon_type = 2;
    public $amount;
    public $max_time_of_use;
    public $max_time_one_can_use;
    public $start_date;
    public $end_date;

    public $search = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'coupon_code' => 'required|string|max:255',
            'coupon_type' => 'required|in:1,2',
            'amount' => 'required|numeric',
            'max_time_of_use' => 'required|integer',
            'max_time_one_can_use' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }

    public function ActiveCreateTab($tab)
    {
        $this->resetForm();
        $this->active_tab = $tab;
    }

    public function toggleStatus($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->status = !$coupon->status;
        $coupon->save();

        session()->flash('message', 'Coupon status updated successfully!');
    }

    public function store()
    {
        $this->validate();

        Coupon::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'coupon_code' => $this->coupon_code,
            'coupon_type' => $this->coupon_type,
            'amount' => $this->amount,
            'max_time_of_use' => $this->max_time_of_use,
            'max_time_one_can_use' => $this->max_time_one_can_use,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        session()->flash('success', 'Coupon created successfully!');
        $this->ActiveCreateTab(1);
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);

        $this->coupon_id = $coupon->id;
        $this->name = $coupon->name;
        $this->coupon_code = $coupon->coupon_code;
        $this->coupon_type = $coupon->coupon_type;
        $this->amount = $coupon->amount;
        $this->max_time_of_use = $coupon->max_time_of_use;
        $this->max_time_one_can_use = $coupon->max_time_one_can_use;
        $this->start_date = $coupon->start_date;
        $this->end_date = $coupon->end_date;

        $this->active_tab = 3;
    }

    public function update()
    {
        $this->validate();

        Coupon::where('id', $this->coupon_id)->update([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'coupon_code' => $this->coupon_code,
            'coupon_type' => $this->coupon_type,
            'amount' => $this->amount,
            'max_time_of_use' => $this->max_time_of_use,
            'max_time_one_can_use' => $this->max_time_one_can_use,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        session()->flash('success', 'Coupon updated successfully!');
        $this->ActiveCreateTab(1);
    }

    public function delete($id)
    {
        Coupon::findOrFail($id)->delete();
        session()->flash('success', 'Coupon deleted successfully!');
    }

    public function resetForm()
    {
        $this->reset([
            'coupon_id',
            'name',
            'coupon_code',
            'coupon_type',
            'amount',
            'max_time_of_use',
            'max_time_one_can_use',
            'start_date',
            'end_date'
        ]);

        $this->resetErrorBag();
    }

    public function render()
    {
        $coupons = Coupon::whereNull('deleted_at')
            ->where('coupon_code', 'like', "%{$this->search}%")
            ->latest()
            ->get();

        return view('livewire.product.coupon-list', compact('coupons'));
    }
}
