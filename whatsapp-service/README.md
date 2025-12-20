# WhatsApp Service - QR Code Connection

خادم Node.js للاتصال بواتساب عبر QR Code باستخدام whatsapp-web.js

## البدء السريع

### 1. تثبيت المكتبات
```bash
npm install
```

### 2. إعداد البيئة
افتح ملف `.env` وأضف رقم الأدمن:
```env
PORT=3000
ADMIN_PHONE=966500000000
```

### 3. تشغيل الخادم
```bash
npm start
```

### 4. مسح QR Code
- سيظهر QR Code في Terminal
- افتح واتساب على هاتفك
- اذهب إلى: الإعدادات > الأجهزة المرتبطة > ربط جهاز
- امسح الكود

## API Endpoints

- `GET /status` - حالة الاتصال
- `GET /qr` - الحصول على QR Code
- `POST /send-message` - إرسال رسالة
- `POST /logout` - تسجيل الخروج

## ملاحظات
- يجب أن يبقى الخادم يعمل طوال الوقت
- ملفات الجلسة تُحفظ في `.wwebjs_auth/`
- جميع الرسائل الواردة تُرسل تلقائياً للأدمن
