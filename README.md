<p align="left"> <img src="https://api.sadiq.workers.dev/app/github/repo/av-dl/views" alt="" /> </p>

# AV-DL

A lightweight, self-hosted web application for downloading videos and audio from any streaming site supported by [yt-dlp](https://github.com/yt-dlp/yt-dlp). Built with PHP — no frameworks, no dependencies, just a clean dark UI and a server that does the heavy lifting.

## ✨ Features

- **Universal Download** — Supports all [1800+ sites](https://github.com/yt-dlp/yt-dlp/blob/master/supportedsites.md) that yt-dlp supports (YouTube, Vimeo, Twitter, etc.)
- **Video & Audio** — Download as MP4 (video) or MP3 (audio) with selectable quality (144p–1080p)
- **Real-Time Progress** — Live download progress streamed to the browser via Server-Sent Events (SSE)
- **Flexible Download Modes**
  - **Direct download** — File is piped straight to your browser
  - **Save to server** — File is kept on the server for later access
  - **Save + download** — Both: saved on the server and downloaded to your browser
- **Basic Authentication** — Protects the interface with configurable username/password credentials
- **Range Request Support** — Resumable file downloads with proper `Content-Range` headers
- **Preferences Persistence** — Remembers your last-used quality, format, and mode via `localStorage`
- **Zero Dependencies** — Pure PHP, vanilla HTML/CSS/JS — no Composer, no npm, no build step

## 📋 Prerequisites

| Dependency | Purpose |
|---|---|
| **PHP 8.0+** | Runtime (with `proc_open` enabled) |
| **yt-dlp** | Media downloading engine |
| **ffmpeg** | Audio extraction & format merging |

The app checks for `yt-dlp` and `ffmpeg` on startup and will display an error if either is missing.

## 🚀 Installation

### 1. Clone the repository

```bash
git clone https://github.com/sadiq-bd/av-dl.git
cd av-dl
```

### 2. Install system dependencies

```bash
# Debian / Ubuntu
sudo apt install php ffmpeg
sudo curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -o /usr/local/bin/yt-dlp
sudo chmod a+rx /usr/local/bin/yt-dlp

# Arch Linux
sudo pacman -S php ffmpeg yt-dlp
```

### 3. Configure

```bash
cp src/example.config.php src/config.php
```

Edit `src/config.php`:

```php
<?php

$config = (object) [
    'downloadDir' => '/path/to/downloads',   // must be writable by the web server
    'basicAuth' => [
        'username' => 'your-password'         // add as many users as needed
    ]
];
```

> [!IMPORTANT]
> The `downloadDir` must exist and be writable by the web server process (e.g. `www-data`).

### 4. Set up a web server

Point your web server's document root to the `public/` directory.

<details>
<summary><strong>Nginx example</strong></summary>

```nginx
server {
    listen 80;
    server_name dl.example.com;
    root /path/to/av-dl/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

</details>

<details>
<summary><strong>Apache example</strong></summary>

```apache
<VirtualHost *:80>
    ServerName dl.example.com
    DocumentRoot /path/to/av-dl/public

    <Directory /path/to/av-dl/public>
        AllowOverride All
        Require all granted

        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^ index.php [L]
    </Directory>
</VirtualHost>
```

</details>

<details>
<summary><strong>PHP built-in server (development only)</strong></summary>

```bash
php -S localhost:8000 -t public/
```

</details>

## 🗂 Project Structure

```
av-dl/
├── public/
│   └── index.php          # Entry point (document root)
└── src/
    ├── index.php           # Router
    ├── config.php          # User config (gitignored)
    ├── example.config.php  # Config template
    ├── basicAuth.php       # HTTP Basic Auth middleware
    ├── requirements.php    # Runtime dependency checks
    ├── utils.php           # yt-dlp command builders & helpers
    ├── download.php        # SSE download handler
    ├── getFile.php         # File serving with range support
    └── ui.php              # Frontend (HTML/CSS/JS)
```

## 🔧 Usage

1. Open the app in your browser and authenticate with your configured credentials
2. Paste a video URL from any supported site
3. Select your preferred **quality** and **format** (MP4 / MP3)
4. Choose a **download mode** (direct / save / both)
5. Click **Continue** and watch the real-time progress

## 📝 License

This project is open source. Feel free to use, modify, and distribute as you see fit.

## 🙏 Credits

- [yt-dlp](https://github.com/yt-dlp/yt-dlp) — The backbone media downloader
- [ffmpeg](https://ffmpeg.org/) — Audio/video processing

---

Made with ❤️ by [Sadiq](https://sadiq.is-a.dev)
