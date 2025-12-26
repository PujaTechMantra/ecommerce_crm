<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Coupon;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CouponList extends Component
{
     public $active_tab = 1;

    public $coupon_id;
    public $name;
    public $coupon_code;
    public $coupon_type = 2;
    public $value;
    public $min_amount;
    public $max_time_of_use;
    public $max_time_one_can_use;
    public $start_date;
    public $end_date;

    public $search = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'coupon_code' => 'required|string|max:255|unique:coupons,coupon_code,' . $this->coupon_id,
            'coupon_type' => 'required|in:1,2',
            'value' => 'required|numeric',
            'min_amount' => 'nullable|numeric',
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
            'value' => $this->value,
            'min_amount' => $this->min_amount,
            'max_time_of_use' => $this->max_time_of_use,
            'max_time_one_can_use' => $this->max_time_one_can_use,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        session()->flash('message', 'Coupon created successfully!');
        $this->ActiveCreateTab(1);
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);

        $this->coupon_id = $coupon->id;
        $this->name = $coupon->name;
        $this->coupon_code = $coupon->coupon_code;
        $this->coupon_type = $coupon->coupon_type;
        $this->value = $coupon->value;
        $this->min_amount = $coupon->min_amount;
        $this->max_time_of_use = $coupon->max_time_of_use;
        $this->max_time_one_can_use = $coupon->max_time_one_can_use;
        $this->start_date = Carbon::parse($coupon->start_date)->format('Y-m-d');
        $this->end_date   = Carbon::parse($coupon->end_date)->format('Y-m-d');

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
            'value' => $this->value,
            'min_amount' => $this->min_amount,
            'max_time_of_use' => $this->max_time_of_use,
            'max_time_one_can_use' => $this->max_time_one_can_use,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        session()->flash('message', 'Coupon updated successfully!');
        $this->ActiveCreateTab(1);
    }

    public function delete($id)
    {
        Coupon::findOrFail($id)->delete();
        session()->flash('message', 'Coupon deleted successfully!');
    }

    public function resetForm()
    {
        $this->reset([
            'coupon_id',
            'name',
            'coupon_code',
            'coupon_type',
            'value',
            'min_amount',
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
            ->where(function ($q) {
                $q->where('coupon_code', 'like', "%{$this->search}%")
                ->orWhere('name', 'like', "%{$this->search}%");
            })
            ->latest()
            ->get();

        return view('livewire.master.coupon-list', compact('coupons'));
    }
}
