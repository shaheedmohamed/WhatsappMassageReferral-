const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const dotenv = require('dotenv');

dotenv.config();

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(bodyParser.json());

let client;
let isReady = false;
let qrCodeData = null;

const initializeWhatsApp = () => {
    client = new Client({
        authStrategy: new LocalAuth(),
        puppeteer: {
            headless: true,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--no-first-run',
                '--no-zygote',
                '--disable-gpu'
            ]
        }
    });

    client.on('qr', (qr) => {
        console.log('\n🔐 QR Code جاهز! امسح الكود التالي بواتساب:\n');
        qrcode.generate(qr, { small: true });
        qrCodeData = qr;
        isReady = false;
    });

    client.on('ready', () => {
        console.log('✅ تم الاتصال بواتساب بنجاح!');
        isReady = true;
        qrCodeData = null;
    });

    client.on('authenticated', () => {
        console.log('✅ تم التوثيق بنجاح!');
    });

    client.on('auth_failure', (msg) => {
        console.error('❌ فشل التوثيق:', msg);
        isReady = false;
    });

    client.on('disconnected', (reason) => {
        console.log('⚠️ تم قطع الاتصال:', reason);
        isReady = false;
        qrCodeData = null;
    });

    client.on('message', async (message) => {
        try {
            const contact = await message.getContact();
            const chat = await message.getChat();
            
            console.log(`📩 رسالة جديدة من: ${contact.pushname || contact.number}`);
            console.log(`💬 الرسالة: ${message.body}`);

            const adminPhone = process.env.ADMIN_PHONE;
            if (adminPhone && message.from !== adminPhone + '@c.us') {
                const formattedMessage = `📩 *رسالة جديدة من WhatsApp*\n\n` +
                    `👤 *المرسل:* ${contact.pushname || 'غير معروف'}\n` +
                    `📱 *الرقم:* ${contact.number}\n` +
                    `💬 *الرسالة:*\n${message.body}\n` +
                    `\n⏰ *الوقت:* ${new Date().toLocaleString('ar-EG')}`;

                await client.sendMessage(adminPhone + '@c.us', formattedMessage);
                console.log('✅ تم إرسال الرسالة للأدمن');
            }
        } catch (error) {
            console.error('❌ خطأ في معالجة الرسالة:', error);
        }
    });

    client.initialize();
};

app.get('/status', (req, res) => {
    res.json({
        success: true,
        ready: isReady,
        qrCode: qrCodeData,
        message: isReady ? 'متصل بواتساب' : (qrCodeData ? 'في انتظار مسح QR Code' : 'جاري التهيئة...')
    });
});

app.post('/send-message', async (req, res) => {
    try {
        if (!isReady) {
            return res.status(503).json({
                success: false,
                error: 'WhatsApp غير متصل. يرجى مسح QR Code أولاً'
            });
        }

        const { to, message } = req.body;

        if (!to || !message) {
            return res.status(400).json({
                success: false,
                error: 'يجب توفير رقم الهاتف والرسالة'
            });
        }

        let phoneNumber = to.replace(/[^0-9]/g, '');
        
        if (!phoneNumber.endsWith('@c.us')) {
            phoneNumber = phoneNumber + '@c.us';
        }

        const sentMessage = await client.sendMessage(phoneNumber, message);

        res.json({
            success: true,
            messageId: sentMessage.id.id,
            timestamp: sentMessage.timestamp,
            message: 'تم إرسال الرسالة بنجاح'
        });

    } catch (error) {
        console.error('❌ خطأ في إرسال الرسالة:', error);
        res.status(500).json({
            success: false,
            error: error.message || 'فشل إرسال الرسالة'
        });
    }
});

app.post('/logout', async (req, res) => {
    try {
        if (client) {
            await client.logout();
            isReady = false;
            qrCodeData = null;
            res.json({
                success: true,
                message: 'تم تسجيل الخروج بنجاح'
            });
        } else {
            res.status(400).json({
                success: false,
                error: 'لا يوجد اتصال نشط'
            });
        }
    } catch (error) {
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

app.get('/qr', (req, res) => {
    if (qrCodeData) {
        res.json({
            success: true,
            qrCode: qrCodeData,
            message: 'امسح هذا الكود بواتساب'
        });
    } else if (isReady) {
        res.json({
            success: true,
            qrCode: null,
            message: 'متصل بالفعل'
        });
    } else {
        res.json({
            success: false,
            qrCode: null,
            message: 'جاري التهيئة...'
        });
    }
});

app.get('/chats', async (req, res) => {
    try {
        if (!isReady) {
            return res.status(503).json({
                success: false,
                error: 'WhatsApp غير متصل'
            });
        }

        const chats = await client.getChats();
        const chatList = chats.map((chat) => {
            const lastMessage = chat.lastMessage;
            
            return {
                id: chat.id._serialized,
                name: chat.name || chat.id.user || 'Unknown',
                isGroup: chat.isGroup,
                unreadCount: chat.unreadCount,
                timestamp: chat.timestamp,
                lastMessage: lastMessage ? {
                    body: lastMessage.body || '',
                    timestamp: lastMessage.timestamp,
                    fromMe: lastMessage.fromMe
                } : null
            };
        });

        chatList.sort((a, b) => b.timestamp - a.timestamp);

        res.json({
            success: true,
            chats: chatList
        });

    } catch (error) {
        console.error('❌ خطأ في جلب المحادثات:', error);
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

app.get('/messages/:chatId', async (req, res) => {
    try {
        if (!isReady) {
            return res.status(503).json({
                success: false,
                error: 'WhatsApp غير متصل'
            });
        }

        const { chatId } = req.params;
        const limit = parseInt(req.query.limit) || 50;

        const chat = await client.getChatById(chatId);
        const messages = await chat.fetchMessages({ limit });

        const messageList = messages.map(msg => ({
            id: msg.id._serialized,
            body: msg.body,
            timestamp: msg.timestamp,
            fromMe: msg.fromMe,
            author: msg.author,
            type: msg.type,
            hasMedia: msg.hasMedia
        }));

        res.json({
            success: true,
            messages: messageList.reverse()
        });

    } catch (error) {
        console.error('❌ خطأ في جلب الرسائل:', error);
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

app.listen(PORT, () => {
    console.log(`\n🚀 خادم WhatsApp يعمل على المنفذ ${PORT}`);
    console.log(`📡 API متاح على: http://localhost:${PORT}`);
    console.log('\n⏳ جاري الاتصال بواتساب...\n');
    initializeWhatsApp();
});
