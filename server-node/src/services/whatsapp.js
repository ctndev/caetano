const wppconnect = require('@wppconnect-team/wppconnect');

let client = null;
let currentStatus = 'disconnected';
let currentQrCode = null;
let connectedPhone = null;

const recentlySent = new Map();
const SENT_TTL_MS = 30_000;

function trackSentContent(number, text) {
    const key = `${number}:${text}`;
    recentlySent.set(key, Date.now());

    if (recentlySent.size > 500) {
        const now = Date.now();
        for (const [k, ts] of recentlySent) {
            if (now - ts > SENT_TTL_MS) recentlySent.delete(k);
        }
    }
}

function wasSentByBot(number, text) {
    const key = `${number}:${text}`;
    const ts = recentlySent.get(key);
    if (!ts) return false;
    if (Date.now() - ts > SENT_TTL_MS) {
        recentlySent.delete(key);
        return false;
    }
    recentlySent.delete(key);
    return true;
}

async function initWhatsApp(onMessage) {
    try {
        client = await wppconnect.create({
            session: 'caetano-bot',
            autoClose: 0,
            puppeteerOptions: {
                executablePath: process.env.CHROME_PATH || '/usr/bin/google-chrome',
                args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'],
            },
            catchQR: (base64Qr, _asciiQR, _attempt, urlCode) => {
                currentQrCode = base64Qr;
                currentStatus = 'qr-code';
                console.log('[WhatsApp] QR Code generated. Scan to connect.');
            },
            statusFind: (statusSession) => {
                console.log('[WhatsApp] Status:', statusSession);
                if (statusSession === 'isLogged' || statusSession === 'inChat') {
                    currentStatus = 'connected';
                    currentQrCode = null;
                }
            },
            logQR: false,
        });

        currentStatus = 'connected';
        currentQrCode = null;

        const hostInfo = await client.getHostDevice();
        connectedPhone = hostInfo?.wid?.user || null;
        console.log('[WhatsApp] Connected! Phone:', connectedPhone);

        client.onAnyMessage(async (message) => {
            if (message.isGroupMsg) return;
            if (message.isStatusV3) return;

            const isFromMe = message.fromMe;

            if (isFromMe && message.from !== message.to) {
                return;
            }

            const actualSender = message.from.replace('@c.us', '');
            const body = message.body || '';

            if (isFromMe && body && wasSentByBot(actualSender, body)) {
                console.log(`[Message] Skipping bot-sent message to ${actualSender}`);
                return;
            }

            console.log(`[Message] type=${message.type} fromMe=${isFromMe} sender=${actualSender} body="${body.substring(0, 80)}"`);

            if (message.type === 'ptt' || message.type === 'audio') {
                try {
                    const buffer = await client.decryptFile(message);
                    await onMessage(actualSender, null, 'audio', buffer, message.mimetype);
                } catch (err) {
                    console.error('[WhatsApp] Error decrypting audio:', err.message);
                }
                return;
            }

            if (body && message.type === 'chat') {
                await onMessage(actualSender, body, 'text');
            }
        });

        client.onStateChange((state) => {
            console.log('[WhatsApp] State changed:', state);
            if (state === 'CONFLICT' || state === 'UNLAUNCHED') {
                client.useHere();
            }
            if (state === 'CONNECTED') {
                currentStatus = 'connected';
            }
        });

    } catch (error) {
        console.error('[WhatsApp] Init error:', error.message);
        currentStatus = 'error';
    }
}

async function sendMessage(number, text) {
    if (!client) throw new Error('WhatsApp client not initialized');

    const chatId = number.includes('@c.us') ? number : `${number}@c.us`;

    trackSentContent(number.replace('@c.us', ''), text);

    const chat = await client.getChatById(chatId);
    if (chat) {
        await client.startTyping(chatId);
        const delay = Math.floor(Math.random() * 2000) + 1000;
        await new Promise((r) => setTimeout(r, delay));
        await client.stopTyping(chatId);
    }

    return client.sendText(chatId, text);
}

function getStatus() {
    return {
        status: currentStatus,
        phone: connectedPhone,
    };
}

function getQrCode() {
    return {
        qrcode: currentQrCode,
        status: currentStatus,
    };
}

module.exports = { initWhatsApp, sendMessage, getStatus, getQrCode };
