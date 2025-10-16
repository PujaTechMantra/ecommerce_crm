<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class PushNotificationList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $page = 1;
    public $tab = 'all';
    public $user_type = "B2C";
    public function gotoPage($value, $pageName = 'page')
    {
        $this->setPage($value, $pageName);
        $this->page = $value;
    }
    public function changeUserType($type){
        $this->user_type = $type;
        $this->gotoPage(1);
    }
    public function changeTab($tab){
        $this->tab = $tab;
        $this->gotoPage(1);
    }
    public function render()
    {
        $all_users = User::where('user_type', $this->user_type)->paginate(10);
        $unassigned_users = User::where('user_type', $this->user_type)->whereDoesntHave('active_vehicle')->paginate(10);
        return view('livewire.admin.push-notification-list',[
            'all_users' => $all_users,
            'unassigned_users' => $unassigned_users
        ]);
    }
}
