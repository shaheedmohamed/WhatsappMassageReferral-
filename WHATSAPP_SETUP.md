# تطبيق Laravel لإدارة رسائل WhatsApp

## نظرة عامة
تطبيق Laravel متكامل يتصل مع WhatsApp Cloud API لاستقبال وإدارة الرسائل الواردة مع لوحة تحكم إدارية.

## المميزات
- ✅ استقبال رسائل WhatsApp عبر Webhook
- ✅ التحقق من Webhook باستخدام VERIFY_TOKEN
- ✅ تخزين الرسائل في قاعدة بيانات MySQL
- ✅ إعادة توجيه الرسائل تلقائياً لرقم المسؤول
- ✅ لوحة تحكم إدارية لعرض الرسائل
- ✅ الرد على الرسائل من لوحة التحكم
- ✅ إرسال رسائل جديدة
- ✅ بحث وفلترة الرسائل
- ✅ إحصائيات الرسائل
- ✅ معالجة الأخطاء وتسجيلها
- ✅ بنية نظيفة (Controller, Service, Model)
- ✅ جاهز لأتمتة روبوت الدردشة مستقبلاً

## المتطلبات
- PHP 8.2+
- MySQL 5.7+
- Composer
- Laravel 12.x

## التثبيت

### 1. تثبيت المشروع
```bash
composer install
```

### 2. إعداد قاعدة البيانات
قم بتحديث ملف `.env` بمعلومات قاعدة البيانات:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shaheed
DB_USERNAME=root
DB_PASSWORD=
```

### 3. تشغيل Migrations
```bash
php artisan migrate
```

### 4. إعداد WhatsApp Cloud API

#### الحصول على Access Token:
1. انتقل إلى [Meta for Developers](https://developers.facebook.com/)
2. أنشئ تطبيق جديد أو استخدم تطبيق موجود
3. أضف منتج WhatsApp Business Platform
4. احصل على Access Token من لوحة التحكم
5. احصل على Phone Number ID

#### تحديث ملف .env:
```env
WHATSAPP_ACCESS_TOKEN=your_access_token_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id_here
WHATSAPP_VERIFY_TOKEN=your_custom_verify_token
ADMIN_WHATSAPP_NUMBER=966500000000
```

**ملاحظة:** `WHATSAPP_VERIFY_TOKEN` يمكن أن يكون أي نص عشوائي تختاره (مثل: `my_secure_token_123`)

### 5. إعداد Webhook في Meta

1. في لوحة تحكم Meta، انتقل إلى إعدادات WhatsApp
2. في قسم Webhook، أضف:
   - **Callback URL:** `https://yourdomain.com/whatsapp/webhook`
   - **Verify Token:** نفس القيمة في `WHATSAPP_VERIFY_TOKEN`
3. اشترك في الأحداث التالية:
   - `messages`

### 6. تشغيل التطبيق
```bash
php artisan serve
```

الوصول للوحة التحكم: `http://localhost:8000/dashboard`

## البنية المعمارية

### الملفات الرئيسية

#### 1. Migration
- `database/migrations/2025_12_16_185303_create_whatsapp_messages_table.php`
  - جدول لتخزين الرسائل الواردة
  - يحتوي على: message_id, from_number, from_name, message_body, timestamps, replied status

#### 2. Model
- `app/Models/WhatsappMessage.php`
  - Model للرسائل مع Scopes مفيدة
  - `scopeUnreplied()` - الرسائل بدون رد
  - `scopeRecent()` - ترتيب حسب الأحدث
  - `scopeFromNumber()` - رسائل من رقم محدد

#### 3. Service
- `app/Services/WhatsAppService.php`
  - `sendMessage()` - إرسال رسالة
  - `forwardMessageToAdmin()` - إعادة توجيه للمسؤول
  - `parseWebhookMessage()` - تحليل الرسائل الواردة
  - `verifyWebhook()` - التحقق من Webhook

#### 4. Controllers
- `app/Http/Controllers/WhatsAppWebhookController.php`
  - `verify()` - التحقق من Webhook (GET)
  - `webhook()` - استقبال الرسائل (POST)

- `app/Http/Controllers/DashboardController.php`
  - `index()` - عرض قائمة الرسائل
  - `show()` - عرض تفاصيل رسالة
  - `reply()` - الرد على رسالة
  - `sendMessage()` - إرسال رسالة جديدة

#### 5. Routes
- `routes/web.php`
  - `GET /whatsapp/webhook` - التحقق من Webhook
  - `POST /whatsapp/webhook` - استقبال الرسائل
  - `GET /dashboard` - لوحة التحكم
  - `GET /dashboard/messages/{id}` - تفاصيل الرسالة
  - `POST /dashboard/messages/{id}/reply` - الرد على رسالة
  - `POST /dashboard/send-message` - إرسال رسالة جديدة

#### 6. Views
- `resources/views/layouts/app.blade.php` - Layout رئيسي
- `resources/views/dashboard/index.blade.php` - قائمة الرسائل
- `resources/views/dashboard/show.blade.php` - تفاصيل الرسالة

## استخدام API

### استقبال رسالة (Webhook)
عند استقبال رسالة من WhatsApp:
1. يتم التحقق من صحة البيانات
2. تحليل الرسالة وحفظها في قاعدة البيانات
3. إعادة توجيه الرسالة تلقائياً لرقم المسؤول
4. تسجيل العملية في Logs

### الرد على رسالة
```javascript
POST /dashboard/messages/{id}/reply
Content-Type: application/json

{
  "reply_message": "شكراً على رسالتك"
}
```

### إرسال رسالة جديدة
```javascript
POST /dashboard/send-message
Content-Type: application/json

{
  "phone_number": "966500000000",
  "message": "مرحباً بك"
}
```

## معالجة الأخطاء

جميع الأخطاء يتم تسجيلها في:
- `storage/logs/laravel.log`

أنواع الأخطاء المسجلة:
- فشل إرسال الرسائل
- أخطاء Webhook
- أخطاء تحليل البيانات
- أخطاء الاتصال بـ API

## الأمان

### CSRF Protection
جميع المسارات محمية بـ CSRF token ما عدا Webhook

### Webhook Verification
يتم التحقق من Webhook باستخدام `WHATSAPP_VERIFY_TOKEN`

### Environment Variables
جميع المعلومات الحساسة في ملف `.env`

## التطوير المستقبلي

### جاهز لأتمتة روبوت الدردشة
البنية الحالية تدعم إضافة:
- ✅ نظام الردود التلقائية
- ✅ معالجة الأوامر (Commands)
- ✅ AI Integration
- ✅ Chatbot Flow Builder
- ✅ Quick Replies
- ✅ Button Messages
- ✅ Template Messages

### اقتراحات للتطوير:
1. إضافة نظام مستخدمين ومصادقة
2. دعم الرسائل الصوتية والصور
3. نظام تذاكر (Ticketing System)
4. تقارير وإحصائيات متقدمة
5. دعم متعدد اللغات
6. API للتكامل مع أنظمة خارجية
7. Chatbot Builder بواجهة مرئية
8. Auto-responder بناءً على الكلمات المفتاحية

## استكشاف الأخطاء

### Webhook لا يعمل
- تأكد من أن URL الخاص بك متاح عبر HTTPS
- تحقق من `WHATSAPP_VERIFY_TOKEN` في `.env`
- راجع Logs في `storage/logs/laravel.log`

### فشل إرسال الرسائل
- تحقق من صحة `WHATSAPP_ACCESS_TOKEN`
- تأكد من `WHATSAPP_PHONE_NUMBER_ID`
- تحقق من صلاحيات Access Token

### قاعدة البيانات
- تأكد من تشغيل `php artisan migrate`
- تحقق من اتصال قاعدة البيانات في `.env`

## الدعم

للمزيد من المعلومات:
- [WhatsApp Cloud API Documentation](https://developers.facebook.com/docs/whatsapp/cloud-api)
- [Laravel Documentation](https://laravel.com/docs)

## الترخيص
MIT License
