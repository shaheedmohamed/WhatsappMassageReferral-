const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const dotenv = require('dotenv');
const multer = require('multer');
const fs = require('fs');
const path = require('path');

dotenv.config();

const app = express();
const PORT = process.env.PORT || 3000;

// Configure multer for file uploads
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        const uploadDir = './uploads';
        if (!fs.existsSync(uploadDir)) {
            fs.mkdirSync(uploadDir);
        }
        cb(null, uploadDir);
    },
    filename: (req, file, cb) => {
        cb(null, Date.now() + '-' + file.originalname);
    }
});
const upload = multer({ storage: storage });

app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Store multiple clients
const clients = new Map();
const qrCodes = new Map();
const clientStatus = new Map();

const initializeWhatsApp = (sessionId) => {
    if (clients.has(sessionId)) {
        console.log(`⚠️ الجلسة ${sessionId} موجودة بالفعل`);
        return clients.get(sessionId);
    }

    console.log(`🔄 تهيئة جلسة جديدة: ${sessionId}`);
    
    const client = new Client({
        authStrategy: new LocalAuth({ clientId: sessionId }),
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
        console.log(`\n🔐 QR Code جاهز للجلسة ${sessionId}!\n`);
        qrcode.generate(qr, { small: true });
        qrCodes.set(sessionId, qr);
        clientStatus.set(sessionId, 'qr_ready');
    });

    client.on('ready', async () => {
        console.log(`✅ الجلسة ${sessionId} متصلة بواتساب بنجاح!`);
        await new Promise(resolve => setTimeout(resolve, 3000));
        clientStatus.set(sessionId, 'ready');
        qrCodes.delete(sessionId);
        console.log(`✅ الجلسة ${sessionId} جاهزة لاستقبال الطلبات!`);
    });

    client.on('authenticated', () => {
        console.log(`✅ تم التوثيق بنجاح للجلسة ${sessionId}`);
        clientStatus.set(sessionId, 'authenticated');
    });

    client.on('auth_failure', (msg) => {
        console.error(`❌ فشل التوثيق للجلسة ${sessionId}:`, msg);
        clientStatus.set(sessionId, 'auth_failed');
        qrCodes.delete(sessionId);
    });

    client.on('disconnected', (reason) => {
        console.log(`⚠️ تم قطع اتصال الجلسة ${sessionId}:`, reason);
        clientStatus.set(sessionId, 'disconnected');
        qrCodes.delete(sessionId);
        clients.delete(sessionId);
    });

    client.on('message', async (message) => {
        try {
            const contact = await message.getContact();
            console.log(`📩 [${sessionId}] رسالة من: ${contact.pushname || contact.number}`);
        } catch (error) {
            console.error(`❌ خطأ في معالجة الرسالة [${sessionId}]:`, error.message);
        }
    });

    clients.set(sessionId, client);
    clientStatus.set(sessionId, 'initializing');
    client.initialize();
    
    return client;
};

// Initialize a new session
app.post('/initialize', async (req, res) => {
    try {
        const { sessionId } = req.body;
        
        if (!sessionId) {
            return res.status(400).json({
                success: false,
                error: 'يجب توفير sessionId'
            });
        }
        
        if (clients.has(sessionId)) {
            const status = clientStatus.get(sessionId);
            return res.json({
                success: true,
                message: 'الجلسة موجودة بالفعل',
                status: status,
                ready: status === 'ready'
            });
        }
        
        initializeWhatsApp(sessionId);
        
        res.json({
            success: true,
            message: 'تم بدء تهيئة الجلسة',
            sessionId: sessionId
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

// Get status for a session or all sessions
app.get('/status/:sessionId?', (req, res) => {
    const sessionId = req.params.sessionId || req.query.session_id;
    
    if (!sessionId) {
        const allSessions = Array.from(clients.keys()).map(sid => ({
            sessionId: sid,
            status: clientStatus.get(sid) || 'unknown',
            ready: clientStatus.get(sid) === 'ready'
        }));
        
        return res.json({
            success: true,
            sessions: allSessions,
            totalSessions: clients.size
        });
    }
    
    const status = clientStatus.get(sessionId);
    const qrCode = qrCodes.get(sessionId);
    const isReady = status === 'ready';
    
    res.json({
        success: true,
        sessionId: sessionId,
        ready: isReady,
        status: status || 'not_initialized',
        qrCode: qrCode || null,
        message: isReady ? 'متصل بواتساب' : (qrCode ? 'في انتظار مسح QR Code' : 'جاري التهيئة...')
    });
});

// Get QR code for a session
app.get('/qr/:sessionId', (req, res) => {
    const sessionId = req.params.sessionId;
    const qrCode = qrCodes.get(sessionId);
    const status = clientStatus.get(sessionId);
    
    if (qrCode) {
        res.json({
            success: true,
            qrCode: qrCode,
            message: 'امسح هذا الكود بواتساب'
        });
    } else if (status === 'ready') {
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

// Send message
app.post('/send-message', async (req, res) => {
    try {
        const { to, message, sessionId } = req.body;

        if (!sessionId) {
            return res.status(400).json({
                success: false,
                error: 'يجب توفير sessionId'
            });
        }

        const client = clients.get(sessionId);
        const status = clientStatus.get(sessionId);

        if (!client || status !== 'ready') {
            return res.status(503).json({
                success: false,
                error: 'الجلسة غير متصلة. يرجى مسح QR Code أولاً'
            });
        }

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

// Logout session
app.post('/logout', async (req, res) => {
    try {
        const { sessionId } = req.body;
        
        if (!sessionId) {
            return res.status(400).json({
                success: false,
                error: 'يجب توفير sessionId'
            });
        }
        
        const client = clients.get(sessionId);
        
        if (client) {
            await client.logout();
            await client.destroy();
            clients.delete(sessionId);
            clientStatus.delete(sessionId);
            qrCodes.delete(sessionId);
            
            res.json({
                success: true,
                message: 'تم تسجيل الخروج بنجاح'
            });
        } else {
            res.status(400).json({
                success: false,
                error: 'لا يوجد اتصال نشط لهذه الجلسة'
            });
        }
    } catch (error) {
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

// Get chats for a session
app.get('/chats/:sessionId', async (req, res) => {
    try {
        const sessionId = req.params.sessionId;
        const client = clients.get(sessionId);
        const status = clientStatus.get(sessionId);

        if (!client || status !== 'ready') {
            return res.status(503).json({
                success: false,
                error: 'الجلسة غير متصلة'
            });
        }

        let chats = [];
        let retries = 3;
        
        while (retries > 0) {
            try {
                chats = await client.getChats();
                break;
            } catch (err) {
                retries--;
                if (retries === 0) throw err;
                console.log(`⚠️ [${sessionId}] محاولة إعادة جلب المحادثات... (${3 - retries}/3)`);
                await new Promise(resolve => setTimeout(resolve, 2000));
            }
        }

        const chatList = chats.map((chat) => {
            try {
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
                    } : null,
                    sessionId: sessionId
                };
            } catch (err) {
                console.error(`⚠️ [${sessionId}] خطأ في معالجة محادثة:`, err.message);
                return null;
            }
        }).filter(chat => chat !== null);

        chatList.sort((a, b) => b.timestamp - a.timestamp);

        res.json({
            success: true,
            chats: chatList,
            sessionId: sessionId
        });

    } catch (error) {
        console.error(`❌ خطأ في جلب المحادثات:`, error.message);
        res.status(500).json({
            success: false,
            error: 'فشل جلب المحادثات. حاول مرة أخرى.'
        });
    }
});

// Get messages for a chat
app.get('/messages/:sessionId/:chatId', async (req, res) => {
    try {
        const { sessionId, chatId } = req.params;
        const limit = parseInt(req.query.limit) || 50;
        
        const client = clients.get(sessionId);
        const status = clientStatus.get(sessionId);

        if (!client || status !== 'ready') {
            return res.status(503).json({
                success: false,
                error: 'الجلسة غير متصلة'
            });
        }

        const chat = await client.getChatById(chatId);
        const messages = await chat.fetchMessages({ limit });

        const messageList = messages.map(msg => ({
            id: msg.id._serialized,
            body: msg.body,
            timestamp: msg.timestamp,
            fromMe: msg.fromMe,
            author: msg.author,
            type: msg.type,
            hasMedia: msg.hasMedia,
            mediaUrl: msg.hasMedia ? `/media/${sessionId}/${encodeURIComponent(msg.id._serialized)}` : null
        }));

        res.json({
            success: true,
            messages: messageList.reverse(),
            sessionId: sessionId
        });

    } catch (error) {
        console.error('❌ خطأ في جلب الرسائل:', error);
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

// Get media file for a message
app.get('/media/:sessionId/:messageId', async (req, res) => {
    try {
        const { sessionId, messageId } = req.params;
        
        const client = clients.get(sessionId);
        const status = clientStatus.get(sessionId);

        if (!client || status !== 'ready') {
            return res.status(503).json({
                success: false,
                error: 'الجلسة غير متصلة'
            });
        }

        // Find the message by ID
        const chats = await client.getChats();
        let targetMessage = null;
        
        for (const chat of chats) {
            const messages = await chat.fetchMessages({ limit: 100 });
            targetMessage = messages.find(msg => msg.id._serialized === decodeURIComponent(messageId));
            if (targetMessage) break;
        }

        if (!targetMessage) {
            return res.status(404).json({
                success: false,
                error: 'الرسالة غير موجودة'
            });
        }

        if (!targetMessage.hasMedia) {
            return res.status(400).json({
                success: false,
                error: 'هذه الرسالة لا تحتوي على وسائط'
            });
        }

        // Download the media
        const media = await targetMessage.downloadMedia();
        
        if (!media) {
            return res.status(500).json({
                success: false,
                error: 'فشل تحميل الوسائط'
            });
        }

        // Convert base64 to buffer
        const buffer = Buffer.from(media.data, 'base64');
        
        // Set appropriate content type
        res.setHeader('Content-Type', media.mimetype);
        res.setHeader('Content-Length', buffer.length);
        res.send(buffer);

    } catch (error) {
        console.error('❌ خطأ في جلب الوسائط:', error);
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

// Send audio message
app.post('/send-audio', upload.single('audio'), async (req, res) => {
    try {
        const { to, sessionId } = req.body;
        const audioFile = req.file;

        if (!sessionId) {
            return res.status(400).json({
                success: false,
                error: 'يجب توفير sessionId'
            });
        }

        const client = clients.get(sessionId);
        const status = clientStatus.get(sessionId);

        if (!client || status !== 'ready') {
            return res.status(503).json({
                success: false,
                error: 'الجلسة غير متصلة'
            });
        }

        if (!to || !audioFile) {
            return res.status(400).json({
                success: false,
                error: 'يجب توفير رقم الهاتف والملف الصوتي'
            });
        }

        let phoneNumber = to;
        if (!phoneNumber.includes('@c.us')) {
            phoneNumber = phoneNumber.replace(/[^0-9]/g, '') + '@c.us';
        }

        // Read audio file and create MessageMedia
        const audioData = fs.readFileSync(audioFile.path, { encoding: 'base64' });
        
        // Determine mimetype - force to audio/ogg for voice messages
        let mimetype = 'audio/ogg; codecs=opus';
        if (audioFile.mimetype.includes('webm')) {
            mimetype = 'audio/ogg; codecs=opus';
        }
        
        const media = new MessageMedia(
            mimetype,
            audioData,
            'voice.ogg'
        );

        // Send audio message without sendAudioAsVoice option to avoid WhatsApp Web bugs
        const sentMessage = await client.sendMessage(phoneNumber, media);

        // Delete temporary file with delay to avoid EBUSY error
        setTimeout(() => {
            try {
                if (fs.existsSync(audioFile.path)) {
                    fs.unlinkSync(audioFile.path);
                }
            } catch (err) {
                console.log('⚠️ Could not delete temp file:', err.message);
            }
        }, 1000);

        res.json({
            success: true,
            messageId: sentMessage.id.id,
            timestamp: sentMessage.timestamp,
            message: 'تم إرسال الرسالة الصوتية بنجاح'
        });

    } catch (error) {
        console.error('❌ خطأ في إرسال الرسالة الصوتية:', error);
        
        // Clean up file on error
        if (req.file && fs.existsSync(req.file.path)) {
            fs.unlinkSync(req.file.path);
        }
        
        res.status(500).json({
            success: false,
            error: error.message || 'فشل إرسال الرسالة الصوتية'
        });
    }
});

app.listen(PORT, () => {
    console.log(`\n🚀 خادم WhatsApp يعمل على المنفذ ${PORT}`);
    console.log(`📡 API متاح على: http://localhost:${PORT}`);
    console.log('\n✅ الخادم جاهز لاستقبال طلبات الاتصال');
    console.log('💡 استخدم POST /initialize مع sessionId لبدء جلسة جديدة\n');
});
