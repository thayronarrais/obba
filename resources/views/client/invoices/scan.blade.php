@extends('wowdash.layout.layout')

@php
    $title = 'Ler código QR';
    $subTitle = 'Ler código QR';
@endphp

@section('content')
    <div class="grid grid-cols-12 gap-5">
        <div class="md:col-span-12 col-span-12">
            <div class="card border-0">
                <div class="card-header">
                    <h5 class="text-lg font-semibold mb-0">Ler código QR</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div id="qr-reader" style="width: 500px"></div>
                    <div id="qr-reader-results"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Stop scanning
            html5QrcodeScanner.clear();
            
            // Display success message
            document.getElementById('qr-reader-results').innerText = 'Código QR lido com sucesso! A processar...';

            // Create a form dynamically
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("invoice.process-simple-qr") }}';

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Add QR code text
            const qrCodeInput = document.createElement('input');
            qrCodeInput.type = 'hidden';
            qrCodeInput.name = 'qr_code_text';
            qrCodeInput.value = decodedText;
            form.appendChild(qrCodeInput);

            // Append the form to the body and submit
            document.body.appendChild(form);
            form.submit();
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning.
            // console.warn(`Code scan error = ${error}`);
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader",
            { fps: 10, qrbox: {width: 350, height: 350} },
            /* verbose= */ false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>
@endpush
