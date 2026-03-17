const express = require('express');
const { getStatus, getQrCode } = require('../services/whatsapp');

const router = express.Router();

router.get('/status', (_req, res) => {
    res.json(getStatus());
});

router.get('/qrcode', (_req, res) => {
    res.json(getQrCode());
});

module.exports = router;
