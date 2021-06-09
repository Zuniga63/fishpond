{{-- ------------------------------ --}}
{{-- LIBRERÍAS DE LA PLANTILLA      --}}
{{-- * JQuery                       --}}
{{-- * Bootstrap 4                  --}}
{{-- * Overlay Scroll Bar           --}}
{{-- * JQuery Validations           --}}
{{-- * Admin LTE APP                --}}
{{-- ------------------------------ --}}
<script src="{{ mix('js/admin/all.js') }}" defer></script>

{{-- ------------------------------ --}}
{{-- OTRAS LIBRERÍAS Y CUSTOMS      --}}
{{-- * Toastr                       --}}
{{-- * Sweet Alert 2                --}}
{{-- * Alpine js                    --}}
{{-- ------------------------------ --}}
<script src="{{ mix('js/admin/main.js') }}" defer></script>


{{-- SCRIPTS DE LIVEWIRE --}}
@livewireScripts

@yield('scriptPlugins')

@yield('scripts')
@stack('scripts')