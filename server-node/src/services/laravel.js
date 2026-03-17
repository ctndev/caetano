const axios = require('axios');

const laravelApi = axios.create({
    baseURL: process.env.LARAVEL_API_URL || 'http://localhost:8000/api',
    timeout: 30000,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

laravelApi.interceptors.request.use((config) => {
    config.headers['X-Api-Secret'] = process.env.API_SECRET || '';
    return config;
});

async function sendToLaravel(number, message, type = 'text') {
    try {
        const response = await laravelApi.post('/whatsapp/message', {
            number,
            message,
            type,
        });
        return response.data;
    } catch (error) {
        const status = error.response?.status;
        const detail = error.response?.data?.error || error.message;
        console.error(`[Laravel] Error (${status}): ${detail}`);
        if (status === 403) {
            console.error(`[Laravel] Number ${number} not in allowed_numbers`);
            return null;
        }
        return null;
    }
}

async function sendAudioToLaravel(number, audioBuffer, mimetype) {
    try {
        const FormData = (await import('form-data')).default || require('form-data');
        const form = new FormData();
        form.append('number', number);
        form.append('type', 'audio');
        form.append('audio', audioBuffer, {
            filename: 'audio.ogg',
            contentType: mimetype || 'audio/ogg',
        });

        const response = await laravelApi.post('/whatsapp/message', form, {
            headers: {
                ...form.getHeaders(),
                'X-Api-Secret': process.env.API_SECRET || '',
            },
            timeout: 60000,
        });
        return response.data;
    } catch (error) {
        const status = error.response?.status;
        const detail = error.response?.data?.error || error.message;
        console.error(`[Laravel] Audio error (${status}): ${detail}`);
        return null;
    }
}

module.exports = { sendToLaravel, sendAudioToLaravel };
