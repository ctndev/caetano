function validateApiSecret(req, res, next) {
    const secret = process.env.API_SECRET;
    if (!secret) {
        return res.status(500).json({ error: 'API_SECRET not configured' });
    }

    const provided = req.headers['x-api-secret'];
    if (provided !== secret) {
        return res.status(401).json({ error: 'Unauthorized' });
    }

    next();
}

module.exports = { validateApiSecret };
