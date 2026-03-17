require('dotenv').config();

const express = require('express');
const cors = require('cors');
const { initWhatsApp, sendMessage } = require('./services/whatsapp');
const { sendToLaravel, sendAudioToLaravel } = require('./services/laravel');
const statusRoutes = require('./routes/status');
const webhookRoutes = require('./routes/webhook');

const app = express();
const PORT = process.env.PORT || 3001;

const processingNumbers = new Set();

app.disable('x-powered-by');
app.use(cors());
app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ extended: true, limit: '50mb' }));

app.use('/api', statusRoutes);
app.use('/api', webhookRoutes);

app.get('/health', (_req, res) => {
    res.json({ status: 'ok', uptime: process.uptime() });
});

async function handleIncomingMessage(sender, text, type, audioBuffer, mimetype) {
    if (processingNumbers.has(sender)) {
        console.log(`[Message] Ignoring from ${sender}: already processing`);
        return;
    }

    processingNumbers.add(sender);
    console.log(`[Message] From: ${sender}, Type: ${type}`);

    try {
        let response;

        if (type === 'audio' && audioBuffer) {
            response = await sendAudioToLaravel(sender, audioBuffer, mimetype);
        } else {
            response = await sendToLaravel(sender, text, type);
        }

        if (response?.reply) {
            try {
                await sendMessage(sender, response.reply);
                console.log(`[Reply] To: ${sender}`);
            } catch (err) {
                console.error(`[Reply] Failed to send to ${sender}:`, err.message);
            }
        } else {
            console.log(`[Message] No reply for ${sender} (error or empty response)`);
        }
    } catch (err) {
        console.error(`[Message] Unhandled error for ${sender}:`, err.message);
    } finally {
        processingNumbers.delete(sender);
    }
}

app.listen(PORT, () => {
    console.log(`[Server] Running on port ${PORT}`);
    initWhatsApp(handleIncomingMessage);
});
