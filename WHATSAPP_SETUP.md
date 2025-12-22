# دليل تشغيل نظام WhatsApp

## نظرة عامة
تم نقل كامل نظام WhatsApp من المشروع السابق. النظام يتكون من:
- خادم Node.js (whatsapp-service) للاتصال بواتساب
- Laravel Backend للإدارة وحفظ الرسائل
- واجهات عرض جميلة للمحادثات ولوحة التحكم

## المميزات
✅ الاتصال بواتساب عبر QR Code
✅ عرض جميع المحادثات
✅ إرسال واستقبال الرسائل
✅ لوحة تحكم لإدارة الرسائل
✅ حفظ الرسائل في قاعدة البيانات
✅ إحصائيات الرسائل

## خطوات التشغيل

### 1. تثبيت Dependencies

#### Laravel Dependencies
```bash
composer install
```

#### Node.js Dependencies (للـ WhatsApp Service)
```bash
cd whatsapp-service
npm install
cd ..
```

### 2. إعداد قاعدة البيانات

تأكد من إعدادات قاعدة البيانات في ملف `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Whatsapprefreal
DB_USERNAME=root
DB_PASSWORD=
```

ثم قم بتشغيل Migrations:
```bash
php artisan migrate
```

### 3. تحديث ملف .env

تأكد من وجود هذه الإعدادات في ملف `.env`:
```env
WHATSAPP_NODE_SERVICE_URL=http://localhost:3000
ADMIN_PHONE=966500000000
```

**ملاحظة:** غير `ADMIN_PHONE` إلى رقم هاتفك (بدون + أو -)

### 4. تشغيل خادم WhatsApp (Node.js)

في نافذة Terminal منفصلة:
```bash
cd whatsapp-service
npm start
```

سترى رسالة:
```
🚀 خادم WhatsApp يعمل على المنفذ 3000
📡 API متاح على: http://localhost:3000
⏳ جاري الاتصال بواتساب...
```

### 5. تشغيل Laravel

في نافذة Terminal أخرى:
```bash
php artisan serve
```

### 6. الاتصال بواتساب

1. افتح المتصفح واذهب إلى: `http://localhost:8000`
2. سيتم توجيهك تلقائياً لصفحة الاتصال
3. امسح QR Code بواتساب من هاتفك:
   - افتح واتساب
   - اذهب إلى: **الإعدادات > الأجهزة المرتبطة > ربط جهاز**
   - امسح الكود
4. بعد المسح، سيتم توجيهك لصفحة المحادثات

## الصفحات المتاحة

### 1. صفحة الاتصال
`http://localhost:8000/whatsapp/connect`
- عرض QR Code للاتصال
- التحقق من حالة الاتصال

### 2. صفحة المحادثات
`http://localhost:8000/whatsapp/chats`
- عرض جميع المحادثات
- إرسال واستقبال الرسائل
- البحث في المحادثات
- إرسال رسائل جديدة

### 3. لوحة التحكم
`http://localhost:8000/dashboard`
- عرض الرسائل المحفوظة
- إحصائيات الرسائل
- الرد على الرسائل
- البحث والفلترة

## البنية المعمارية

### الملفات الرئيسية

#### Backend (Laravel)
- **Models:** `app/Models/WhatsappMessage.php`
- **Controllers:**
  - `app/Http/Controllers/WhatsAppWebController.php`
  - `app/Http/Controllers/DashboardController.php`
  - `app/Http/Controllers/WhatsAppWebhookController.php`
- **Services:** `app/Services/WhatsAppService.php`
- **Migration:** `database/migrations/2025_12_21_024500_create_whatsapp_messages_table.php`
- **Routes:** `routes/web.php`

#### Frontend (Views)
- **Layouts:** `resources/views/layouts/app.blade.php`
- **WhatsApp:**
  - `resources/views/whatsapp/connect.blade.php`
  - `resources/views/whatsapp/chats.blade.php`
- **Dashboard:**
  - `resources/views/dashboard/index.blade.php`
  - `resources/views/dashboard/show.blade.php`

#### WhatsApp Service (Node.js)
- **Main:** `whatsapp-service/server.js`
- **Package:** `whatsapp-service/package.json`

## API Endpoints

### WhatsApp Web API
- `GET /whatsapp` - الصفحة الرئيسية
- `GET /whatsapp/connect` - صفحة الاتصال
- `GET /whatsapp/chats` - صفحة المحادثات
- `GET /whatsapp/api/status` - حالة الاتصال
- `GET /whatsapp/api/qr` - الحصول على QR Code
- `GET /whatsapp/api/chats` - قائمة المحادثات
- `GET /whatsapp/api/messages/{chatId}` - رسائل محادثة معينة
- `POST /whatsapp/api/send` - إرسال رسالة
- `POST /whatsapp/api/logout` - تسجيل الخروج

### Dashboard API
- `GET /dashboard` - لوحة التحكم
- `GET /dashboard/messages/{id}` - تفاصيل رسالة
- `POST /dashboard/messages/{id}/reply` - الرد على رسالة
- `POST /dashboard/send-message` - إرسال رسالة جديدة

## استكشاف الأخطاء

### خطأ: "خادم WhatsApp غير متصل"
**الحل:**
1. تأكد من تشغيل خادم Node.js: `cd whatsapp-service && npm start`
2. تحقق من أن المنفذ 3000 غير مستخدم
3. تأكد من `WHATSAPP_NODE_SERVICE_URL=http://localhost:3000` في `.env`

### خطأ: "فشل تحميل المحادثات"
**الحل:**
1. تأكد من مسح QR Code أولاً
2. انتظر 5-10 ثواني بعد المسح
3. أعد تحميل الصفحة

### خطأ في قاعدة البيانات
**الحل:**
```bash
php artisan migrate:fresh
```

### QR Code لا يظهر
**الحل:**
1. تحقق من أن خادم Node.js يعمل
2. افتح Console في المتصفح وتحقق من الأخطاء
3. جرب إعادة تشغيل خادم Node.js

## ملاحظات مهمة

1. **الاتصال الدائم:** بعد مسح QR Code، سيبقى الاتصال نشطاً حتى لو أغلقت المتصفح
2. **البيانات المحفوظة:** جميع بيانات الاتصال محفوظة في مجلد `.wwebjs_auth/`
3. **تسجيل الخروج:** لتسجيل الخروج، استخدم زر Logout في صفحة المحادثات
4. **رقم الأدمن:** غير `ADMIN_PHONE` في `.env` لتلقي إشعارات الرسائل

## الأوامر المفيدة

### تشغيل كل شيء مرة واحدة
```bash
# Terminal 1 - Laravel
php artisan serve

# Terminal 2 - WhatsApp Service
cd whatsapp-service && npm start
```

### إعادة تشغيل WhatsApp Service
```bash
cd whatsapp-service
npm start
```

### مسح بيانات الاتصال (لإعادة المسح)
```bash
cd whatsapp-service
rm -rf .wwebjs_auth .wwebjs_cache
```

## التطوير المستقبلي

يمكن إضافة:
- ✅ الردود التلقائية
- ✅ روبوت دردشة (Chatbot)
- ✅ إرسال الصور والملفات
- ✅ المجموعات
- ✅ الإحصائيات المتقدمة
- ✅ نظام التذاكر

## الدعم

إذا واجهت أي مشاكل:
1. تحقق من Logs في Terminal
2. تحقق من Console في المتصفح
3. تأكد من تشغيل كل من Laravel و Node.js

---

**تم النقل بنجاح! 🎉**

جميع الملفات والإعدادات جاهزة للعمل.
