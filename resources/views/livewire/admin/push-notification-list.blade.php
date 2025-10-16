<div>
    <style>
        .custom-select-icon-style::before {
            font-size: 16px;
            font-weight: 700;
            padding: 0px 3px;
        }
        .active-rider-type{
            background-color: #8c57ff;
            color: #fff;
        }
    </style>
    <div class="d-flex justify-content-start align-items-center mb-2">
      <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn {{$user_type=="B2C" ? 'active-rider-type' : 'btn-label-secondary'}} waves-effect btn-sm" wire:click="changeUserType('B2C')" >B2C</button>
        <button type="button" class="btn {{$user_type=="B2B" ? 'active-rider-type' : 'btn-label-secondary'}} waves-effect btn-sm" wire:click="changeUserType('B2B')">B2B</button>
      </div>
    </div>
    <div class="row">
      <!-- Left Side (Customer Tabs) -->
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-header p-3">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                  @php
                      $allCount = 3;
                      $unassignedCount = 1000;
                      $assignedCount = 1;
                      $overdueCount = 1365;
                  @endphp
              <li class="nav-item" wire:click="changeTab('all')">
                <a class="nav-link {{$tab=='all'?'active':''}}" data-bs-toggle="tab" href="#tab-all" role="tab">
                  All <span class="badge rounded-pill badge-center h-px-20 w-px-{{ strlen($allCount) }}0 bg-label-secondary ms-1_5 pt-50">{{ $allCount }}</span>
                </a>
              </li>
              <li class="nav-item" wire:click="changeTab('unassigned')">
                <a class="nav-link {{$tab=='unassigned'?'active':''}}" data-bs-toggle="tab" href="#tab-unassigned" role="tab">
                  Unassigned <span class="badge rounded-pill badge-center h-px-20 w-px-{{ strlen($unassignedCount) }}0 bg-label-warning ms-1_5 pt-50">{{ $unassignedCount }}</span>
                </a>
              </li>
              <li class="nav-item" wire:click="changeTab('assigned')">
                <a class="nav-link {{$tab=='assigned'?'active':''}}" data-bs-toggle="tab" href="#tab-assigned" role="tab">
                  Assigned <span class="badge rounded-pill badge-center h-px-20 w-px-{{ strlen($assignedCount) }}0 bg-label-success ms-1_5 pt-50">{{ $assignedCount }}</span>
                </a>
              </li>
              <li class="nav-item" wire:click="changeTab('overdue')">
                <a class="nav-link {{$tab=='overdue'?'active':''}}" data-bs-toggle="tab" href="#tab-overdue" role="tab">
                  Overdue <span class="badge rounded-pill badge-center h-px-20 w-px-{{ strlen($overdueCount) }}0 bg-label-danger ms-1_5 pt-50">{{ $overdueCount }}</span>
                </a>
              </li>
              <li class="nav-item" wire:click="changeTab('custom_select')">
                <a class="nav-link {{$tab=='custom_select'? 'active' :''}}" data-bs-toggle="tab" href="#tab-custom_select" role="tab">
                  Custom Select 
                <i class="menu-icon tf-icons ri-group-line custom-select-icon-style"></i>
                </a>
              </li>
            </ul>
          </div>

          <div class="tab-content p-3">
            <!-- All Customers -->
            <div class="tab-pane fade {{$tab=='all'?'show active':''}}" id="tab-all" role="tabpanel">
               <p>All</p>
              <ul class="list-group list-group-flush">
                  @forelse ($all_users as $all_user)
                    @php
                      $vehicleStatus = FetchUserVehicleStatus($all_user->id);
                    @endphp
                      <li class="list-group-item d-flex align-items-center cursor-pointer">
                          <div class="form-check me-3">
                              <input class="form-check-input" type="checkbox" wire:model="selectedUsers" value="{{ $all_user->id }}">
                          </div>
                          <div>
                              <h6 class="mb-0"><a href="{{ route('admin.customer.details', $all_user->id) }}" target="_blank"> {{ ucwords($all_user->name) }}</a></h6>
                              <small class="text-muted">{{ $all_user->country_code }}{{ $all_user->mobile }}</small>
                              @if($user_type=="B2B")
                              | <small class="badge rounded-pill badge-center bg-label-danger">
                                  ORG: <span class="text-dark"><a href="{{route('admin.organization.dashboard', $all_user->organization_details->id)}}" class="text-danger"> {{ucwords($all_user->organization_details->name)}} </a></span>
                              </small>
                              @endif
                          </div>
                          <span class="badge bg-label-{{ $vehicleStatus['color'] }} rounded-pill ms-auto"
                              data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              data-bs-html="true"
                              data-bs-original-title="{!! $vehicleStatus['tooltip'] !!}">
                            {{ ucwords($vehicleStatus['status']) }}
                        </span>
                      </li>
                  @empty
                      <div class="alert alert-warning">No riders will appear here.</div>
                  @endforelse
              </ul>
              <div class="d-flex justify-content-end mt-3 paginator">
                  {{ $all_users->links() }}
              </div>
            </div>

            <!-- Unassigned -->
            <div class="tab-pane fade {{$tab=='unassigned'?'show active':''}}" id="tab-unassigned" role="tabpanel">
               <p>unassigned</p>
              <ul class="list-group list-group-flush">
                  @forelse ($unassigned_users as $unassigned_user)
                    @php
                      $vehicleStatus = FetchUserVehicleStatus($unassigned_user->id);
                    @endphp
                      <li class="list-group-item d-flex align-items-center cursor-pointer">
                          <div class="form-check me-3">
                              <input class="form-check-input" type="checkbox" wire:model="selectedUsers" value="{{ $unassigned_user->id }}">
                          </div>
                          <div>
                              <h6 class="mb-0"><a href="{{ route('admin.customer.details', $unassigned_user->id) }}" target="_blank"> {{ ucwords($unassigned_user->name) }}</a></h6>
                              <small class="text-muted">{{ $unassigned_user->country_code }}{{ $unassigned_user->mobile }}</small>
                              @if($user_type=="B2B")
                              | <small class="badge rounded-pill badge-center bg-label-danger">
                                  ORG: <span class="text-dark"><a href="{{route('admin.organization.dashboard', $unassigned_user->organization_details->id)}}" class="text-danger"> {{ucwords($unassigned_user->organization_details->name)}} </a></span>
                              </small>
                              @endif
                          </div>
                          <span class="badge bg-label-{{ $vehicleStatus['color'] }} rounded-pill ms-auto"
                              data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              data-bs-html="true"
                              data-bs-original-title="{!! $vehicleStatus['tooltip'] !!}">
                            {{ ucwords($vehicleStatus['status']) }}
                        </span>
                      </li>
                  @empty
                      <div class="alert alert-warning">No riders will appear here.</div>
                  @endforelse
              </ul>
              <div class="d-flex justify-content-end mt-3 paginator">
                  {{ $unassigned_users->links() }}
              </div>
            </div>

            <!-- Assigned -->
            <div class="tab-pane fade {{$tab=='assigned'?'show active':''}}" id="tab-assigned" role="tabpanel">
              <p>Assigned</p>
              <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex align-items-center">
                  <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" id="customer1">
                  </div>
                  <div>
                    <h6 class="mb-0">Souvik Mondal</h6>
                    <small class="text-muted">+919876543210</small>
                  </div>
                  <span class="badge bg-label-success rounded-pill ms-auto">Assigned</span>
                </li>
                <li class="list-group-item d-flex align-items-center">
                  <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" id="customer2">
                  </div>
                  <div>
                    <h6 class="mb-0">Koushik Adhikary</h6>
                    <small class="text-muted">+919876541289</small>
                  </div>
                  <span class="badge bg-label-warning rounded-pill ms-auto">Unassigned</span>
                </li>
                <li class="list-group-item d-flex align-items-center">
                  <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" id="customer3">
                  </div>
                  <div>
                    <h6 class="mb-0">Prakash Verma</h6>
                    <small class="text-muted">+919876544567</small>
                  </div>
                  <span class="badge bg-label-danger rounded-pill ms-auto">Overdue</span>
                </li>
              </ul>
              <div class="alert alert-success">Assigned customers will appear here.</div>
            </div>

            <!-- Overdue -->
            <div class="tab-pane fade {{$tab=='overdue'?'show active':''}}" id="tab-overdue" role="tabpanel">
              <p>overdue</p>
              <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex align-items-center">
                  <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" id="customer1">
                  </div>
                  <div>
                    <h6 class="mb-0">Souvik Mondal</h6>
                    <small class="text-muted">+919876543210</small>
                  </div>
                  <span class="badge bg-label-success rounded-pill ms-auto">Assigned</span>
                </li>
                <li class="list-group-item d-flex align-items-center">
                  <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" id="customer2">
                  </div>
                  <div>
                    <h6 class="mb-0">Koushik Adhikary</h6>
                    <small class="text-muted">+919876541289</small>
                  </div>
                  <span class="badge bg-label-warning rounded-pill ms-auto">Unassigned</span>
                </li>
                <li class="list-group-item d-flex align-items-center">
                  <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" id="customer3">
                  </div>
                  <div>
                    <h6 class="mb-0">Prakash Verma</h6>
                    <small class="text-muted">+919876544567</small>
                  </div>
                  <span class="badge bg-label-danger rounded-pill ms-auto">Overdue</span>
                </li>
              </ul>
              <div class="alert alert-danger">Overdue customers will appear here.</div>
            </div>
            <!-- Custom Select -->
            <div class="tab-pane fade {{$tab=='custom_select'?'show active':''}}" id="tab-custom_select" role="tabpanel"> 
              <p>custom_select</p>
              <div class="d-flex justify-content-end align-items-center mb-2 container mx-1">
                <p class="badge bg-primary rounded-pill cursor-pointer mb-0">
                  <i class="menu-icon tf-icons ri-group-line custom-select-icon-style"></i>Select All
                </p>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex align-items-center">
                  <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" id="customer1">
                  </div>
                  <div>
                    <h6 class="mb-0">Souvik Mondal</h6>
                    <small class="text-muted">+919876543210</small>
                  </div>
                  <span class="badge bg-label-success rounded-pill ms-auto">Assigned</span>
                </li>
                <li class="list-group-item d-flex align-items-center">
                  <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" id="customer2">
                  </div>
                  <div>
                    <h6 class="mb-0">Koushik Adhikary</h6>
                    <small class="text-muted">+919876541289</small>
                  </div>
                  <span class="badge bg-label-warning rounded-pill ms-auto">Unassigned</span>
                </li>
                <li class="list-group-item d-flex align-items-center">
                  <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" id="customer3">
                  </div>
                  <div>
                    <h6 class="mb-0">Prakash Verma</h6>
                    <small class="text-muted">+919876544567</small>
                  </div>
                  <span class="badge bg-label-danger rounded-pill ms-auto">Overdue</span>
                </li>
              </ul>
              <div class="alert alert-danger">Overdue customers will appear here.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side (Send Message) -->
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Send Message</h5>
            <span class="badge bg-primary">Selected: 5 Riders</span>
          </div>
          <div class="card-body">
            <form>
              <div>
                <label for="messageText" class="form-label">Message</label>
                <textarea class="form-control" id="messageText" rows="5" placeholder="Type your message..."></textarea>
              </div>
              <small class="form-label d-block mt-1">
                Selected rider(s) will receive this message.
              </small>
              <button type="submit" class="btn btn-success w-100 mt-3">
                <i class="fas fa-paper-plane me-2"></i>Send
              </button>
            </form>
          </div>
        </div>
      </div>

    </div>
</div>
@section('page-script')
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endsection