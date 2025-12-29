<div>
  <div class="position-relative">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner py-6 mx-4">

        <div class="card p-7">
          <div class="app-brand justify-content-center mt-5">
            <a href="{{ url('/') }}" class="app-brand-link gap-3">
              <span class="app-brand-text demo text-heading fw-semibold">Admin</span>
            </a>
          </div>

          <div class="card-body mt-1">
            <h4 class="mb-1">Forgot Password? 🔑</h4>
            <p class="mb-5">Enter your registered mobile number to receive an OTP.</p>

            {{-- Step 1: Mobile --}}
            @if ($step === 1)
              <form wire:submit.prevent="sendOtp" class="mb-5">
                @csrf
                <div class="form-floating form-floating-outline mb-5">
                  <input type="text" wire:model="mobile" class="form-control" placeholder="Enter your mobile number">
                  <label>Mobile Number</label>
                  @error('mobile') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-5">
                  <button class="btn btn-primary d-grid w-100" type="submit">
                    Send OTP
                  </button>
                </div>
              </form>
            @endif

            {{-- Step 2: OTP --}}
            @if ($step === 2)
              <form wire:submit.prevent="verifyOtp" class="mb-5">
                @csrf
                <div class="form-floating form-floating-outline mb-5">
                  <input type="text" wire:model="otp" class="form-control" placeholder="Enter OTP">
                  <label>OTP</label>
                  @error('otp') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-5">
                  <button class="btn btn-success d-grid w-100" type="submit">Verify OTP</button>
                  <p class="mt-2 text-danger fw-semibold">
                    Resend OTP in <span id="otp-timer">60s</span>
                  </p>
                </div>
              </form>
            @endif


            {{-- Step 3: Reset password --}}
            @if ($step === 3)
              <form wire:submit.prevent="resetPassword" class="mb-5">
                @csrf
                <div class="form-floating form-floating-outline mb-5">
                  <input type="password" wire:model="password" class="form-control" placeholder="New Password">
                  <label>New Password</label>
                  @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-floating form-floating-outline mb-5">
                  <input type="password" wire:model="password_confirmation" class="form-control" placeholder="Confirm Password">
                  <label>Confirm Password</label>
                  @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-5">
                  <button class="btn btn-primary d-grid w-100" type="submit">Reset Password</button>
                </div>
              </form>
            @endif

            <div class="text-center">
              <a href="{{ route('login') }}" class="text-primary fw-semibold">Back to Login</a>
            </div>
          </div>
        </div>

        <img src="{{ asset('assets/img/tree-3.png') }}" alt="auth-tree" class="authentication-image-object-left d-none d-lg-block">
        <!-- <img src="{{ asset('assets/img/login-back1.webp') }}" alt="auth-tree" class="authentication-image-object-right d-none d-lg-block"> -->
      </div>
    </div>
  </div>
</div>
@section('page-script')
<script>
      window.addEventListener('startTimer', function (event) {
          let timer = 60;
          let counter = setInterval(function () {
              if (timer > 0) {
                  timer--;
                  document.querySelector('#otp-timer').innerText = timer + 's';
              } else {
                  clearInterval(counter);
                  Livewire.dispatch('timerExpired'); // safe way
              }
          }, 1000);
      });
</script>
@endsection


