<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>الاتصال بـ Care Bot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 9h12v2H6V9zm8 5H6v-2h8v2zm4-6H6V6h12v2z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Care Bot ويب</h1>
                    <p class="text-gray-600">اتصل بحسابك على Care Bot</p>
                </div>

                <div id="statusContainer" class="mb-6">
                    <div class="flex items-center justify-center p-4 bg-blue-50 rounded-lg">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 ml-3"></div>
                        <span class="text-blue-700">جاري التحقق من الاتصال...</span>
                    </div>
                </div>

                <div id="qrContainer" class="hidden">
                    <div class="bg-gray-50 rounded-xl p-6 mb-6">
                        <div class="text-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-800 mb-2">امسح رمز QR</h2>
                            <p class="text-sm text-gray-600">افتح Care Bot على هاتفك واذهب إلى:</p>
                            <p class="text-sm text-gray-700 font-medium mt-1">الإعدادات > الأجهزة المرتبطة > ربط جهاز</p>
                        </div>
                        <div id="qrCode" class="flex justify-center bg-white p-4 rounded-lg"></div>
                    </div>
                </div>

                <div id="connectedContainer" class="hidden">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-green-800 font-medium">متصل بـ Care Bot بنجاح!</span>
                        </div>
                    </div>
                    <a href="{{ route('whatsapp.chats') }}" class="block w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 text-center">
                        عرض المحادثات
                    </a>
                </div>

                <div class="mt-6 text-center text-sm text-gray-500">
                    <p>سيتم تحديث الحالة تلقائياً</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        let checkInterval;
        let currentQR = null;
        
        async function checkStatus() {
            try {
                const response = await fetch('{{ route("whatsapp.api.status") }}');
                const data = await response.json();
                
                console.log('Status:', data);
                
                const statusContainer = document.getElementById('statusContainer');
                const qrContainer = document.getElementById('qrContainer');
                const connectedContainer = document.getElementById('connectedContainer');
                
                if (data.ready) {
                    statusContainer.classList.add('hidden');
                    qrContainer.classList.add('hidden');
                    connectedContainer.classList.remove('hidden');
                    clearInterval(checkInterval);
                    setTimeout(() => {
                        window.location.href = '{{ route("whatsapp.chats") }}';
                    }, 1500);
                } else if (data.qrCode) {
                    statusContainer.classList.add('hidden');
                    qrContainer.classList.remove('hidden');
                    connectedContainer.classList.add('hidden');
                    
                    if (currentQR !== data.qrCode) {
                        currentQR = data.qrCode;
                        const qrCodeDiv = document.getElementById('qrCode');
                        qrCodeDiv.innerHTML = '';
                        
                        try {
                            new QRCode(qrCodeDiv, {
                                text: data.qrCode,
                                width: 256,
                                height: 256,
                                colorDark: "#000000",
                                colorLight: "#ffffff",
                                correctLevel: QRCode.CorrectLevel.H
                            });
                        } catch (e) {
                            console.error('QR Error:', e);
                            qrCodeDiv.innerHTML = '<p class="text-red-500">خطأ في عرض QR Code</p>';
                        }
                    }
                } else {
                    statusContainer.classList.remove('hidden');
                    qrContainer.classList.add('hidden');
                    connectedContainer.classList.add('hidden');
                }
            } catch (error) {
                console.error('Error checking status:', error);
                document.getElementById('statusContainer').innerHTML = `
                    <div class="flex items-center justify-center p-4 bg-red-50 rounded-lg">
                        <svg class="w-6 h-6 text-red-500 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="text-red-700">خطأ في الاتصال بخادم Care Bot. تأكد من تشغيل خادم Node.js</span>
                    </div>
                `;
            }
        }
        
        checkStatus();
        checkInterval = setInterval(checkStatus, 3000);
    </script>
</body>
</html>
