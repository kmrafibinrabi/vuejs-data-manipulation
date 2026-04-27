require('dotenv').config()
const express = require('express')
const multer = require('multer')
const axios = require('axios')
const cors = require('cors')
const fs = require('fs')
const path = require('path')

const app = express()
const PORT = 8000

// Middleware
app.use(cors())
app.use(express.json())
app.use(express.urlencoded({ extended: true }))

// Create uploads directory if it doesn't exist
const uploadsDir = path.join(__dirname, 'uploads')
if (!fs.existsSync(uploadsDir)) {
  fs.mkdirSync(uploadsDir, { recursive: true })
}

// Multer configuration for image uploads
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, uploadsDir)
  },
  filename: (req, file, cb) => {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9)
    cb(null, file.fieldname + '-' + uniqueSuffix + path.extname(file.originalname))
  }
})

const upload = multer({
  storage: storage,
  limits: { fileSize: 5 * 1024 * 1024 }, // 5MB limit
  fileFilter: (req, file, cb) => {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
    if (allowedTypes.includes(file.mimetype)) {
      cb(null, true)
    } else {
      cb(new Error('Only image files are allowed'))
    }
  }
})

// Helper function to convert image to base64
const imageToBase64 = (filePath) => {
  const imageBuffer = fs.readFileSync(filePath)
  return imageBuffer.toString('base64')
}

// API endpoint for image research
app.post('/api/image-research', upload.single('image'), async (req, res) => {
  try {
    if (!req.file) {
      return res.status(400).json({ error: 'No image file provided' })
    }

    const question = req.body.question
    if (!question) {
      return res.status(400).json({ error: 'No question provided' })
    }

    // Convert image to base64
    const imageBase64 = imageToBase64(req.file.path)
    const mimeType = req.file.mimetype

    // Determine media type
    const mediaTypeMap = {
      'image/jpeg': 'image/jpeg',
      'image/png': 'image/png',
      'image/gif': 'image/gif',
      'image/webp': 'image/webp'
    }
    const mediaType = mediaTypeMap[mimeType] || 'image/jpeg'

    // Call Gemini Vision API - UPDATED MODEL NAME
    const geminiApiKey = process.env.GEMINI_API_KEY
    if (!geminiApiKey) {
      return res.status(500).json({ error: 'Gemini API key not configured' })
    }

    // FIXED: Using gemini-2.5-flash instead of retired gemini-1.5-flash
    const geminiUrl = `https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=${geminiApiKey}`

    const payload = {
      contents: [
        {
          parts: [
            {
              inlineData: {
                mimeType: mediaType,
                data: imageBase64
              }
            },
            {
              text: question
            }
          ]
        }
      ]
    }

    const response = await axios.post(geminiUrl, payload, {
      headers: { 'Content-Type': 'application/json' }
    })

    const answer = response.data.candidates?.[0]?.content?.parts?.[0]?.text || 'No answer generated'

    // Clean up uploaded file (optional - remove after processing)
    fs.unlinkSync(req.file.path)

    res.json({
      success: true,
      answer: answer,
      originalImage: req.file.originalname
    })

  } catch (err) {
    // Clean up file on error
    if (req.file && fs.existsSync(req.file.path)) {
      fs.unlinkSync(req.file.path)
    }

    console.error('API Error:', err.message)

    if (err.response?.status === 400) {
      return res.status(400).json({
        error: 'Bad request to Gemini API',
        details: err.response.data
      })
    }

    res.status(500).json({
      error: 'Something went wrong',
      message: err.message
    })
  }
})

// Health check endpoint
app.get('/health', (req, res) => {
  res.json({ status: 'Backend is running!' })
})

// Start server
app.listen(PORT, () => {
  console.log(`✅ Backend server is running on http://localhost:${PORT}`)
  console.log(`📸 Image Research API: POST http://localhost:8000/api/image-research`)
})