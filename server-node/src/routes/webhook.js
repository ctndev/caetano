const express = require('express');
const { sendMessage } = require('../services/whatsapp');
const { validateApiSecret } = require('../middleware/auth');

const router = express.Router();

router.post('/send', validateApiSecret, async (req, res) => {
    try {
        const { number, message } = req.body;

        if (!number || !message) {
            return res.status(400).json({ error: 'number and message are required' });
        }

        await sendMessage(number, message);
        res.json({ success: true });
    } catch (error) {
        console.error('[Send] Error:', error.message);
        res.status(500).json({ error: 'Failed to send message' });
    }
});

module.exports = router;
