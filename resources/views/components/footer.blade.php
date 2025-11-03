@php
    $settings = \App\Models\SiteSetting::first();
@endphp

@if($settings && ($settings->footer_column_1 || $settings->footer_column_2 || $settings->footer_column_3))
<footer class="bg-gray-900 text-gray-300 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- ستون اول -->
            @if($settings->footer_column_1)
            <div class="footer-column">
                {!! $settings->footer_column_1 !!}
            </div>
            @endif

            <!-- ستون دوم -->
            @if($settings->footer_column_2)
            <div class="footer-column">
                {!! $settings->footer_column_2 !!}
            </div>
            @endif

            <!-- ستون سوم -->
            @if($settings->footer_column_3)
            <div class="footer-column">
                {!! $settings->footer_column_3 !!}
            </div>
            @endif
        </div>
    </div>

    <!-- Copyright -->
    <div class="border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-sm text-gray-400">
                © {{ date('Y') }} تمامی حقوق محفوظ است.
            </p>
        </div>
    </div>
</footer>

<style>
    .footer-column h1,
    .footer-column h2,
    .footer-column h3,
    .footer-column h4 {
        color: #ffffff;
        font-weight: bold;
        margin-bottom: 1rem;
    }

    .footer-column h3 {
        font-size: 1.125rem;
    }

    .footer-column p {
        margin-bottom: 0.75rem;
        line-height: 1.6;
    }

    .footer-column ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-column ul li {
        margin-bottom: 0.5rem;
    }

    .footer-column a {
        color: #9ca3af;
        text-decoration: none;
        transition: color 0.2s;
    }

    .footer-column a:hover {
        color: #ffffff;
    }

    .footer-column .social-links {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .footer-column .social-links a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        background-color: #374151;
        border-radius: 0.5rem;
        transition: background-color 0.2s;
    }

    .footer-column .social-links a:hover {
        background-color: #4b5563;
    }
</style>
@endif
