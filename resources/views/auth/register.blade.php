<x-guest-layout>

    <form id="registerForm" method="POST" action="{{ route('register') }}">
        @csrf

        <!-- First Name -->
        <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" class="block mt-1 w-full"
                type="text" name="first_name" :value="old('first_name')"
                 autocomplete="given-name" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div>
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" class="block mt-1 w-full"
                type="text" name="last_name" :value="old('last_name')"
                 autocomplete="family-name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full"
                type="email" name="email" :value="old('email')" 
                 autocomplete="username"
                pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$"
            />
            <span id="emailError" class="text-red-600 text-sm mt-1"></span>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                type="password" name="password"
                minlength="6"  autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password" name="password_confirmation"
                 autocomplete="new-password" />
            <span id="passwordMatchError" class="text-red-600 text-sm"></span>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
               href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>


    <!-- Front-end Validation Script -->
    <script>
        const emailField = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirmation');
        const passwordMatchError = document.getElementById('passwordMatchError');
        const form = document.getElementById('registerForm');

        // Email duplicate check (AJAX)
        emailField.addEventListener('blur', function () {
            emailError.textContent = '';
            const email = this.value;

            if (email.length < 3) return;

            fetch("{{ route('email.check') }}?email=" + email)
                .then(res => res.json())
                .then(data => {
                    if (data.exists) {
                        emailError.textContent = "This email is already registered.";
                        emailField.setCustomValidity("Duplicate email");
                    } else {
                        emailField.setCustomValidity("");
                    }
                });
        });

        // Password match validation
        function checkPasswordMatch() {
            if (password.value !== passwordConfirm.value) {
                passwordMatchError.textContent = "Passwords do not match";
                passwordConfirm.setCustomValidity("Passwords do not match");
            } else {
                passwordMatchError.textContent = "";
                passwordConfirm.setCustomValidity("");
            }
        }

        password.addEventListener('input', checkPasswordMatch);
        passwordConfirm.addEventListener('input', checkPasswordMatch);

        // Form Submit Validation
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                form.reportValidity();
            }
        });
    </script>

</x-guest-layout>
