# 🚀 M3aarf Youtube Course Scraper

> This content is AI-generated :)

An intelligent, **Laravel-based** web application built to automatically discover, scrape, and curate educational YouTube playlists (courses) using AI-generated search queries. Designed with a modern, responsive, RTL Bootstrap 5 interface tailored for Arabic content.

This project was built to demonstrate architectural best practices, clean code, third-party API integration, background queue processing, and seamless UX design—making it a perfect showcase for a technical interview.

---

## 🌟 Key Features & Capabilities

- **🧠 AI-Powered Discovery**: Integrates natively with the **Google Gemini 2.5 Flash API** to dynamically brainstorm 10-15 highly relevant course titles (search queries) based on broad user categories (e.g., "Programming", "Marketing").
- **🎥 YouTube Data Integration**: Automatically queries the **YouTube Data v3 API** utilizing the AI-generated titles to fetch the precise educational playlists and metadata (thumbnails, channel names, descriptions).
- **⚙️ Background Processing (Queues)**: Designed for scalability, the heavy API operations are dispatched to Laravel's background queue (`ProcessYoutubeScrapperJob`), preventing browser timeouts and keeping the main application thread responsive.
- **⚡ Asynchronous AJAX Polling**: The frontend submits data asynchronously and polls the server every 2 seconds to check if the background scraping job is finished, automatically reloading the courses exclusively when ready.
- **🛑 Graceful Kill Switch**: Implemented a real-time `Stop` button that securely halts nested API loops mid-execution via Cache flags, saving precious API quotas limits if the user decides to cancel.
- **🛡️ Idempotent Database Operations**: Handles duplicate API responses seamlessly by utilizing Laravel's `firstOrCreate` mechanism—ensuring unique playlists are stored.
- **🎨 Beautiful, Modular RTL UI**: Frontend heavily customized with Bootstrap 5 RTL, CSS grid layouts, interactive flexbox adjustments, custom CSS extracted cleanly to `public/css/style.css`, and customized centered pagination tracking.

---

## 🏗️ Architectural Structure & Design Patterns

To maintain robust, scalable code, I followed the **Service-Oriented Architecture** (SOA) and the **Single Responsibility Principle** (SRP).

- **`YouTubeEducationalPlaylistGeneratorController`**: Very slim controller. Only handles incoming HTTP routing and JSON boundary definitions, immediately delegating workload.
- **`YouTubeEducationalPlaylistGeneratorService`**: Contains core application business logic, manages the cache flags (for the stop behavior), transforms request arrays, and orchestrates the dispatch of Queue Jobs.
- **`ProcessYoutubeScrapperJob`**: The background worker that wraps the heavy processing inside a Database Transaction (`DB::transaction`) to ensure data integrity during parallel processing.
- **`ScrapperHandler`**: The central brain that runs the algorithm: iterates over categories, dynamically injects prompts, talks to the AI, talks to YouTube, validates arrays, gracefully breaks if the "kill-switch" cache flag is detected, and persists Data Objects.
- **`GeminiChatBot`**: Extracted class dedicated solely to forging precise headers (`x-goog-api-key`), parsing JSON cleanly, throwing informative connection exceptions, and communicating with the Gemini 2.5 Flash LLM.
- **`YouTubeScrapper`**: Encapsulates the configuration and execution of the YouTube `search` endpoint, logging HTTP payload responses securely to Laravel logs for easier debugging.

---

## ⚙️ Requirements

- **PHP 8.2+**
- **Composer**
- **MySQL / PostgreSQL** (as configured in `.env`)
- **Google Gemini API Key**
- **YouTube Data v3 API Key**

---

## 🚀 Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone <repository_url>
   cd Youtube
   ```

2. **Install PHP and Node dependencies:**
   ```bash
   composer install
   ```

3. **Configure the Environment:**
   Copy the example environment variables file and generate your application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Initialize the Database:**
   Migrate the database to create the `playlists` tables and job batches:
   ```bash
   php artisan migrate
   ```

---

## 🔑 API Keys Configuration

For the AI models and fetching systems to work, you must inject your API keys into the `.env` file. 

Open the newly created `.env` file and append the following variables at the bottom:
```env
GEMINI_API_KEY="your_gemini_api_key_here"
YOUTUBE_API_KEY="your_youtube_api_key_here"

# Also ensure your queue connection is set to support background processing:
QUEUE_CONNECTION=database
```

---

## 💻 How to Run the Project

Since this project utilizes background scraping to prevent HTTP timeouts, you need to run **two** separate terminal processes locally.

**Terminal 1 (The Web Server):**
Start your local PHP server:
```bash
php artisan serve
```

**Terminal 2 (The Queue Worker):**
Start the background job processor that will explicitly catch the dispatched AI requests:
```bash
php artisan queue:listen --queue=youtube_scrapper
```
> **Note:** The command above is perfect for local development. In a production environment, you should use **Supervisor** to permanently keep the queue workers running in the background.

Once both are running, open your browser and navigate to:
👉 `http://localhost:8000/`

---

## 📖 Usage Guide

1. Open the application in your browser.
2. In the text area labeled "أدخل التصنيفات", type in modern topics you want to harvest courses for (e.g., `البرمجة`, `التسويق الإلكتروني`, `الذكاء الاصطناعي`). *Each category must be on a new line!*
3. Click the red **"ابدأ الجمع" (Start Fetching)** button.
4. An AJAX request is fired under the hood. The system will show a spinner and begin polling the server.
5. Watch your terminal running `php artisan queue:listen --queue=youtube_scrapper` perfectly process the `ProcessYoutubeScrapperJob`.
6. *(Optional)* If you change your mind, click **"إيقاف" (Stop)** to gracefully sever the process.
7. Once finished, the interface will automatically refresh, rendering a beautifully paginated, grid-based dashboard of newly discovered YouTube Courses.
