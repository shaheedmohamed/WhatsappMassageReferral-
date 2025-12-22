# WhatsApp Service

Node.js service using whatsapp-web.js for WhatsApp integration.

## Installation

```bash
npm install
```

## Running

```bash
npm start
```

The service will run on port 3000 by default.

## Endpoints

- GET /status - Check WhatsApp connection status
- GET /qr - Get QR code for authentication
- POST /send-message - Send a message
- GET /chats - Get all chats
- GET /messages/:chatId - Get messages from a chat
- POST /logout - Logout from WhatsApp
